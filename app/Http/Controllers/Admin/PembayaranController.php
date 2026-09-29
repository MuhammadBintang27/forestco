<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Support\WhatsApp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    public function index(Request $request): View
    {
        // Default ke "Semua Status" - tidak difilter kalau parameter status kosong/absen.
        $status = $request->string('status')->toString();

        $payments = Pembayaran::query()
            ->with(['sewa.reservation.penyewa', 'sewa.reservation.room.property'])
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pembayaran.index', [
            'payments' => $payments,
            'status' => $status,
            'counts' => [
                'menunggu' => Pembayaran::query()->where('status', 'review')->count(),
                'terverifikasi_bulan_ini' => Pembayaran::query()->where('status', 'verified')->whereMonth('updated_at', now()->month)->whereYear('updated_at', now()->year)->count(),
                'ditolak_bulan_ini' => Pembayaran::query()->where('status', 'rejected')->whereMonth('updated_at', now()->month)->whereYear('updated_at', now()->year)->count(),
            ],
        ]);
    }

    public function review(Request $request, Pembayaran $payment): RedirectResponse
    {
        if ($payment->status !== 'review') {
            throw ValidationException::withMessages(['decision' => 'Pembayaran ini sudah diproses sebelumnya.']);
        }

        $validated = $request->validate([
            'decision' => ['required', 'in:verified,rejected'],
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ]);

        $verified = $validated['decision'] === 'verified';

        DB::transaction(function () use ($payment, $verified, $validated) {
            $payment->update([
                'status' => $verified ? 'verified' : 'rejected',
                'catatan_admin' => $validated['catatan_admin'] ?? null,
            ]);

            if ($payment->perpanjangan_sewa_id) {
                $this->applyExtensionPayment($payment, $verified);
            } else {
                $this->applyReservationPayment($payment, $verified);
            }
        });

        $penyewa = $payment->penyewa();
        $propertyName = $payment->sewa->reservation->property->nama;

        $message = $verified
            ? "Halo {$penyewa->nama}, pembayaran Anda untuk *{$propertyName}* telah kami verifikasi. Selamat, Anda resmi menyewa! Terima kasih."
            : "Halo {$penyewa->nama}, mohon maaf bukti transfer untuk *{$propertyName}* belum bisa kami verifikasi".(($validated['catatan_admin'] ?? '') !== '' ? " ({$validated['catatan_admin']})" : '').'. Silakan upload ulang bukti transfer ya.';

        return redirect()->route('admin.pembayaran.index')
            ->with('success', 'Keputusan pembayaran tersimpan.')
            ->with('wa_redirect', WhatsApp::link($penyewa?->no_telepon, $message));
    }

    private function applyReservationPayment(Pembayaran $payment, bool $verified): void
    {
        // Sewa sudah dibuat sejak reservasi diverifikasi (status
        // menunggu_pembayaran) - di sini tinggal mengaktifkannya.
        if (! $verified) {
            return;
        }

        $rental = $payment->sewa;
        $rental->update(['status' => 'active']);

        $rental->room?->update(['status' => 'terisi']);
    }

    private function applyExtensionPayment(Pembayaran $payment, bool $verified): void
    {
        $extension = $payment->rentalExtension;
        $rental = $payment->sewa;

        if (! $verified) {
            $extension->update(['status' => 'awaiting_payment']);

            return;
        }

        $newEndDate = $extension->tanggal_selesai_baru ?? $rental->tanggal_selesai->copy()->addMonths($extension->durasi_diminta_bulan);

        $extension->update(['status' => 'approved', 'tanggal_selesai_baru' => $newEndDate]);

        $rental->update([
            'tanggal_selesai' => $newEndDate,
            'status' => 'active',
            'pengingat_terkirim_pada' => null,
        ]);
    }
}
