<?php

namespace App\Http\Controllers;

use App\Models\Properti;
use App\Models\Reservasi;
use App\Models\Unit;
use App\Models\Pengaturan;
use App\Support\WhatsApp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReservasiController extends Controller
{
    public function index(Request $request): View
    {
        $reservations = $request->user()->reservations()
            ->with(['room.property', 'rental'])
            ->latest()
            ->get();

        return view('reservasi.index', [
            'reservations' => $reservations,
        ]);
    }

    public function show(Request $request, Reservasi $reservation): View
    {
        abort_unless($reservation->penyewa_id === $request->user()->id, 403);

        $reservation->load(['room.property', 'inspections', 'rental.payments']);

        return view('reservasi.show', [
            'reservation' => $reservation,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'properti_id' => ['required', 'exists:properti,id'],
            'unit_id' => ['nullable', 'exists:unit,id'],
            'durasi_bulan' => ['required', 'integer'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        $property = Properti::query()->where('status', 'published')->findOrFail($validated['properti_id']);

        $durasiOptions = Reservasi::durasiOptionsFor($property->tipe);

        if (! array_key_exists($validated['durasi_bulan'], $durasiOptions)) {
            throw ValidationException::withMessages([
                'durasi_bulan' => 'Durasi sewa tidak valid untuk tipe properti ini. Pilihan: '.implode(', ', $durasiOptions).'.',
            ]);
        }

        if ($property->isKos()) {
            if (empty($validated['unit_id'])) {
                throw ValidationException::withMessages(['unit_id' => 'Silakan pilih kamar terlebih dahulu.']);
            }

            $room = Unit::query()->where('properti_id', $property->id)->findOrFail($validated['unit_id']);
        } else {
            // Rumah/ruko selalu punya tepat 1 unit yang mewakili properti itu sendiri.
            $room = $property->soleUnit();

            abort_if(! $room, 404);
        }

        if (! $room->isAvailable()) {
            throw ValidationException::withMessages(['unit_id' => 'Unit yang dipilih sudah tidak tersedia.']);
        }

        $reservation = Reservasi::create([
            'penyewa_id' => $request->user()->id,
            'unit_id' => $room->id,
            'durasi_bulan' => $validated['durasi_bulan'],
            'catatan' => $validated['catatan'] ?? null,
            'status' => 'pending',
        ]);

        $waNumber = Pengaturan::current()->no_wa_admin;
        $message = "Halo Admin, saya {$request->user()->nama} ingin mengajukan reservasi untuk *{$property->nama}*"
            .($property->isKos() ? " kamar {$room->kode}" : '')
            ." selama {$reservation->durasi_bulan} bulan. Mohon diverifikasi ya, terima kasih.";

        return redirect()->route('reservasi.show', $reservation)
            ->with('success', 'Reservasi berhasil diajukan. Menunggu verifikasi admin.')
            ->with('wa_redirect', WhatsApp::link($waNumber, $message));
    }

    public function cancel(Request $request, Reservasi $reservation): RedirectResponse
    {
        abort_unless($reservation->penyewa_id === $request->user()->id, 403);

        if ($reservation->status !== 'pending') {
            throw ValidationException::withMessages(['status' => 'Reservasi ini sudah diproses dan tidak bisa dibatalkan sendiri.']);
        }

        $reservation->update(['status' => 'dibatalkan']);

        $waNumber = Pengaturan::current()->no_wa_admin;
        $message = "Halo Admin, saya {$request->user()->nama} membatalkan reservasi untuk *{$reservation->property->nama}*. Mohon diabaikan pengajuan sebelumnya, terima kasih.";

        return redirect()->route('reservasi.show', $reservation)
            ->with('success', 'Reservasi berhasil dibatalkan.')
            ->with('wa_redirect', WhatsApp::link($waNumber, $message));
    }
}
