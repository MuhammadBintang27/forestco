<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Reservasi;
use App\Models\PerpanjanganSewa;
use App\Models\Pengaturan;
use App\Support\WhatsApp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    public function index(Request $request): View
    {
        $payments = Pembayaran::query()
            ->whereHas('sewa.reservation', fn ($q) => $q->where('penyewa_id', $request->user()->id))
            ->with(['sewa.reservation.room.property'])
            ->latest()
            ->get();

        return view('pembayaran.index', [
            'payments' => $payments,
        ]);
    }

    public function storeForReservation(Request $request, Reservasi $reservation): RedirectResponse
    {
        abort_unless($reservation->penyewa_id === $request->user()->id, 403);

        $rental = $reservation->rental;

        if ($reservation->status !== 'verified' || ! $rental) {
            throw ValidationException::withMessages([
                'proof' => 'Reservasi ini belum bisa melakukan pembayaran.',
            ]);
        }

        if ($rental->status !== 'menunggu_pembayaran') {
            throw ValidationException::withMessages([
                'proof' => 'Reservasi ini sudah resmi menyewa.',
            ]);
        }

        if ($rental->payments()->where('status', 'review')->exists()) {
            throw ValidationException::withMessages([
                'proof' => 'Bukti transfer sebelumnya masih menunggu verifikasi admin.',
            ]);
        }

        $validated = $request->validate([
            'proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        $path = $validated['proof']->store('bukti-transfer', 'public');

        $rental->payments()->create([
            'jumlah' => $reservation->totalAmount(),
            'bukti_path' => $path,
            'status' => 'review',
            'jatuh_tempo' => $rental->tanggal_mulai,
        ]);

        $waNumber = Pengaturan::current()->no_wa_admin;
        $message = "Halo Admin, saya {$request->user()->nama} sudah mengupload bukti transfer untuk reservasi *{$reservation->property->nama}*. Mohon diverifikasi ya, terima kasih.";

        return redirect()->route('reservasi.show', $reservation)
            ->with('success', 'Bukti transfer berhasil diupload. Menunggu verifikasi admin.')
            ->with('wa_redirect', WhatsApp::link($waNumber, $message));
    }

    public function storeForExtension(Request $request, PerpanjanganSewa $extension): RedirectResponse
    {
        $rental = $extension->rental()->with('reservation.room.property')->first();

        abort_unless($rental->reservation->penyewa_id === $request->user()->id, 403);

        if ($extension->status !== 'awaiting_payment') {
            throw ValidationException::withMessages([
                'proof' => 'Perpanjangan ini belum bisa melakukan pembayaran.',
            ]);
        }

        $validated = $request->validate([
            'proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        $path = $validated['proof']->store('bukti-transfer', 'public');

        $amount = $rental->room->priceForDuration($extension->durasi_diminta_bulan);

        $extension->payments()->create([
            'sewa_id' => $rental->id,
            'jumlah' => $amount,
            'bukti_path' => $path,
            'status' => 'review',
            'jatuh_tempo' => $rental->tanggal_selesai,
        ]);

        $waNumber = Pengaturan::current()->no_wa_admin;
        $message = "Halo Admin, saya {$request->user()->nama} sudah mengupload bukti transfer perpanjangan sewa *{$rental->reservation->property->nama}*. Mohon diverifikasi ya, terima kasih.";

        return redirect()->route('sewa.index')
            ->with('success', 'Bukti transfer perpanjangan berhasil diupload. Menunggu verifikasi admin.')
            ->with('wa_redirect', WhatsApp::link($waNumber, $message));
    }
}
