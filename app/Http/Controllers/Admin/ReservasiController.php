<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservasi;
use App\Models\Sewa;
use App\Support\WhatsApp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReservasiController extends Controller
{
    public function index(Request $request): View
    {
        // Default ke "Semua Status" - tidak difilter kalau parameter status kosong/absen.
        $status = $request->string('status')->toString();

        $reservations = Reservasi::query()
            ->with(['penyewa', 'room.property'])
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $selected = null;

        if ($request->filled('selected')) {
            $selected = Reservasi::query()
                ->with(['penyewa', 'room.property', 'inspections', 'rental.payments'])
                ->find($request->integer('selected'));
        }

        if (! $selected) {
            $selected = $reservations->first()
                ? Reservasi::query()->with(['penyewa', 'room.property', 'inspections', 'rental.payments'])->find($reservations->first()->id)
                : null;
        }

        return view('admin.reservasi.index', [
            'reservations' => $reservations,
            'status' => $status,
            'selected' => $selected,
            'counts' => [
                'menunggu' => Reservasi::query()->where('status', 'pending')->count(),
                'disetujui_bulan_ini' => Reservasi::query()->where('status', '!=', 'pending')->where('status', '!=', 'rejected')->whereMonth('updated_at', now()->month)->whereYear('updated_at', now()->year)->count(),
                'ditolak_bulan_ini' => Reservasi::query()->where('status', 'rejected')->whereMonth('updated_at', now()->month)->whereYear('updated_at', now()->year)->count(),
            ],
        ]);
    }

    public function review(Request $request, Reservasi $reservation): RedirectResponse
    {
        if ($reservation->status !== 'pending') {
            throw ValidationException::withMessages(['decision' => 'Reservasi ini sudah diproses sebelumnya.']);
        }

        $validated = $request->validate([
            'decision' => ['required', 'in:verify,reject'],
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ]);

        $verified = $validated['decision'] === 'verify';

        DB::transaction(function () use ($reservation, $verified, $validated) {
            $reservation->update([
                'status' => $verified ? 'verified' : 'rejected',
                'catatan_admin' => $validated['catatan_admin'] ?? null,
            ]);

            if ($verified) {
                // Sewa dibuat langsung di titik ini (status menunggu_pembayaran)
                // sesuai ERD: PEMBAYARAN menempel ke SEWA, bukan ke reservasi.
                Sewa::create([
                    'reservasi_id' => $reservation->id,
                    'tanggal_mulai' => now()->toDateString(),
                    'tanggal_selesai' => now()->addMonths($reservation->durasi_bulan)->toDateString(),
                    'status' => 'menunggu_pembayaran',
                ]);
            }
        });

        $propertyName = $reservation->property->nama;
        $message = $validated['decision'] === 'verify'
            ? "Halo {$reservation->penyewa->nama}, reservasi Anda untuk *{$propertyName}* telah kami verifikasi. Anda bisa mengajukan jadwal cek unit atau langsung melakukan pembayaran ya."
            : "Halo {$reservation->penyewa->nama}, mohon maaf reservasi Anda untuk *{$propertyName}* belum bisa kami proses".(($validated['catatan_admin'] ?? '') !== '' ? " ({$validated['catatan_admin']})" : '').'.';

        return redirect()->route('admin.reservasi.index')
            ->with('success', 'Keputusan reservasi tersimpan.')
            ->with('wa_redirect', WhatsApp::link($reservation->penyewa->no_telepon, $message));
    }
}
