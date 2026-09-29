<?php

namespace App\Http\Controllers;

use App\Models\Reservasi;
use App\Models\Pengaturan;
use App\Support\WhatsApp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class JadwalCekController extends Controller
{
    public function store(Request $request, Reservasi $reservation): RedirectResponse
    {
        abort_unless($reservation->penyewa_id === $request->user()->id, 403);

        if ($reservation->status !== 'verified') {
            throw ValidationException::withMessages([
                'tanggal_diminta' => 'Reservasi ini belum bisa mengajukan jadwal cek.',
            ]);
        }

        $latest = $reservation->latestInspection();

        if ($latest && in_array($latest->status, ['pending', 'approved'], true)) {
            throw ValidationException::withMessages([
                'tanggal_diminta' => 'Sudah ada jadwal cek yang sedang diproses untuk reservasi ini.',
            ]);
        }

        $validated = $request->validate([
            'tanggal_diminta' => ['required', 'date', 'after:today'],
            'waktu_diminta' => ['nullable', 'date_format:H:i'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        $inspection = $reservation->inspections()->create([
            'tanggal_diminta' => $validated['tanggal_diminta'],
            'waktu_diminta' => $validated['waktu_diminta'] ?? null,
            'catatan' => $validated['catatan'] ?? null,
            'status' => 'pending',
        ]);

        $waNumber = Pengaturan::current()->no_wa_admin;
        $message = "Halo Admin, saya {$request->user()->nama} ingin mengajukan jadwal kunjungan unit *{$reservation->property->nama}* pada {$inspection->scheduleLabel()}. Mohon konfirmasi ya, terima kasih.";

        return redirect()->route('reservasi.show', $reservation)
            ->with('success', 'Jadwal kunjungan diajukan. Menunggu konfirmasi admin.')
            ->with('wa_redirect', WhatsApp::link($waNumber, $message));
    }
}
