<?php

namespace App\Http\Controllers;

use App\Models\LaporanKerusakan;
use App\Models\Sewa;
use App\Models\Pengaturan;
use App\Support\WhatsApp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanKerusakanController extends Controller
{
    public function index(Request $request): View
    {
        $activeRentals = $request->user()->reservations()
            ->with('room.property')
            ->whereHas('rental', fn ($q) => $q->where('status', 'active'))
            ->get()
            ->pluck('rental');

        $damageReports = LaporanKerusakan::query()
            ->where('penyewa_id', $request->user()->id)
            ->with('rental.reservation.room.property')
            ->latest()
            ->get();

        return view('kerusakan.index', [
            'activeRentals' => $activeRentals,
            'damageReports' => $damageReports,
        ]);
    }

    public function store(Request $request, Sewa $rental): RedirectResponse
    {
        abort_unless($rental->reservation->penyewa_id === $request->user()->id, 403);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'deskripsi' => ['required', 'string', 'max:1000'],
            'photo' => ['nullable', 'file', 'image', 'max:4096'],
        ]);

        $path = $request->hasFile('photo') ? $request->file('photo')->store('kerusakan', 'public') : null;

        $rental->damageReports()->create([
            'penyewa_id' => $request->user()->id,
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'foto_path' => $path,
            'status' => 'baru',
        ]);

        $waNumber = Pengaturan::current()->no_wa_admin;
        $propertyName = $rental->reservation->property->nama;
        $message = "Halo Admin, saya {$request->user()->nama} ingin melaporkan kerusakan pada unit *{$propertyName}*: {$validated['judul']}. Mohon ditindaklanjuti ya, terima kasih.";

        return redirect()->route('kerusakan.index')
            ->with('success', 'Laporan kerusakan terkirim. Admin akan segera menindaklanjuti.')
            ->with('wa_redirect', WhatsApp::link($waNumber, $message));
    }
}
