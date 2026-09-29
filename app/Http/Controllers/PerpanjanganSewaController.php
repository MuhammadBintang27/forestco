<?php

namespace App\Http\Controllers;

use App\Models\Reservasi;
use App\Models\Sewa;
use App\Models\Pengaturan;
use App\Support\WhatsApp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PerpanjanganSewaController extends Controller
{
    public function create(Request $request, Sewa $rental): View
    {
        abort_unless($rental->reservation->penyewa_id === $request->user()->id, 403);

        $rental->load('reservation.room.property');

        return view('sewa.perpanjang', [
            'rental' => $rental,
        ]);
    }

    public function store(Request $request, Sewa $rental): RedirectResponse
    {
        abort_unless($rental->reservation->penyewa_id === $request->user()->id, 403);

        if ($rental->status !== 'active') {
            throw ValidationException::withMessages(['durasi_diminta_bulan' => 'Masa sewa ini sudah tidak aktif.']);
        }

        $hasPending = $rental->extensions()->whereIn('status', ['requested', 'awaiting_payment'])->exists();

        if ($hasPending) {
            throw ValidationException::withMessages(['durasi_diminta_bulan' => 'Sudah ada pengajuan perpanjangan yang sedang diproses.']);
        }

        $durasiOptions = Reservasi::durasiOptionsFor($rental->room->property->tipe);

        $validated = $request->validate([
            'durasi_diminta_bulan' => ['required', 'integer', 'in:'.implode(',', array_keys($durasiOptions))],
        ]);

        $extension = $rental->extensions()->create([
            'durasi_diminta_bulan' => $validated['durasi_diminta_bulan'],
            'status' => 'requested',
        ]);

        $waNumber = Pengaturan::current()->no_wa_admin;
        $propertyName = $rental->reservation->property->nama;
        $message = "Halo Admin, saya {$request->user()->nama} ingin mengajukan perpanjangan sewa *{$propertyName}* selama {$extension->durasi_diminta_bulan} bulan. Mohon diproses ya, terima kasih.";

        return redirect()->route('sewa.index')
            ->with('success', 'Pengajuan perpanjangan terkirim. Menunggu persetujuan admin.')
            ->with('wa_redirect', WhatsApp::link($waNumber, $message));
    }
}
