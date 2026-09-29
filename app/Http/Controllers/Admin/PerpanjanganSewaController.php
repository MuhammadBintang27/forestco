<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PerpanjanganSewa;
use App\Support\WhatsApp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PerpanjanganSewaController extends Controller
{
    public function index(Request $request): View
    {
        // No "status" in the URL at all -> first visit, default to requested.
        // "status" present but empty (the "Semua Status" option) -> show everything.
        $status = $request->has('status') ? $request->string('status')->toString() : 'requested';

        $extensions = PerpanjanganSewa::query()
            ->with(['rental.reservation.penyewa', 'rental.reservation.room.property', 'payments'])
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.perpanjangan.index', [
            'extensions' => $extensions,
            'status' => $status,
        ]);
    }

    public function review(Request $request, PerpanjanganSewa $extension): RedirectResponse
    {
        if ($extension->status !== 'requested') {
            throw ValidationException::withMessages(['decision' => 'Pengajuan perpanjangan ini sudah diproses sebelumnya.']);
        }

        $validated = $request->validate([
            'decision' => ['required', 'in:approve,reject'],
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ]);

        $rental = $extension->rental()->with('reservation.penyewa', 'reservation.room.property')->first();

        if ($validated['decision'] === 'approve') {
            $extension->update([
                'status' => 'awaiting_payment',
                'tanggal_selesai_baru' => $rental->tanggal_selesai->copy()->addMonths($extension->durasi_diminta_bulan),
                'catatan_admin' => $validated['catatan_admin'] ?? null,
            ]);
        } else {
            $extension->update([
                'status' => 'rejected',
                'catatan_admin' => $validated['catatan_admin'] ?? null,
            ]);
        }

        $penyewa = $rental->reservation->penyewa;
        $propertyName = $rental->reservation->property->nama;

        $message = $validated['decision'] === 'approve'
            ? "Halo {$penyewa->nama}, pengajuan perpanjangan sewa *{$propertyName}* telah kami setujui. Silakan lakukan pembayaran ya."
            : "Halo {$penyewa->nama}, mohon maaf pengajuan perpanjangan sewa *{$propertyName}* belum bisa kami setujui".(($validated['catatan_admin'] ?? '') !== '' ? " ({$validated['catatan_admin']})" : '').'.';

        return redirect()->route('admin.perpanjangan.index')
            ->with('success', 'Keputusan perpanjangan tersimpan.')
            ->with('wa_redirect', WhatsApp::link($penyewa->no_telepon, $message));
    }
}
