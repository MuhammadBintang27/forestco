<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalCek;
use App\Support\WhatsApp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class JadwalCekController extends Controller
{
    public function index(Request $request): View
    {
        // Default ke "Semua Status" - tidak difilter kalau parameter status kosong/absen.
        $status = $request->string('status')->toString();

        $inspections = JadwalCek::query()
            ->with(['reservation.penyewa', 'reservation.room.property'])
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->orderBy('tanggal_diminta')
            ->paginate(15)
            ->withQueryString();

        return view('admin.inspeksi.index', [
            'inspections' => $inspections,
            'status' => $status,
            'counts' => [
                'menunggu' => JadwalCek::query()->where('status', 'pending')->count(),
                'terjadwal_minggu_ini' => JadwalCek::query()->where('status', 'approved')->whereBetween('tanggal_diminta', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'selesai_bulan_ini' => JadwalCek::query()->where('status', 'done')->whereMonth('updated_at', now()->month)->whereYear('updated_at', now()->year)->count(),
            ],
        ]);
    }

    public function review(Request $request, JadwalCek $inspection): RedirectResponse
    {
        if ($inspection->status !== 'pending') {
            throw ValidationException::withMessages(['decision' => 'Jadwal cek ini sudah diproses sebelumnya.']);
        }

        $validated = $request->validate([
            'decision' => ['required', 'in:approve,reject'],
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ]);

        $inspection->update([
            'status' => $validated['decision'] === 'approve' ? 'approved' : 'rejected',
            'catatan_admin' => $validated['catatan_admin'] ?? null,
        ]);

        $reservation = $inspection->reservation()->with(['penyewa', 'room.property'])->first();
        $tanggal = $inspection->tanggal_diminta->translatedFormat('d F Y');
        $propertyName = $reservation->property->nama;

        $message = $validated['decision'] === 'approve'
            ? "Halo {$reservation->penyewa->nama}, jadwal cek unit *{$propertyName}* pada tanggal {$tanggal} telah kami setujui. Sampai jumpa!"
            : "Halo {$reservation->penyewa->nama}, mohon maaf jadwal cek unit *{$propertyName}* pada tanggal {$tanggal} belum bisa kami setujui".(($validated['catatan_admin'] ?? '') !== '' ? " ({$validated['catatan_admin']})" : '').". Silakan ajukan tanggal lain ya.";

        return redirect()->route('admin.inspeksi.index')
            ->with('success', 'Keputusan jadwal cek tersimpan.')
            ->with('wa_redirect', WhatsApp::link($reservation->penyewa->no_telepon, $message));
    }

    public function done(JadwalCek $inspection): RedirectResponse
    {
        if ($inspection->status !== 'approved') {
            throw ValidationException::withMessages(['status' => 'Jadwal cek ini belum disetujui.']);
        }

        $inspection->update(['status' => 'done']);

        return redirect()->route('admin.inspeksi.index')
            ->with('success', 'Jadwal cek ditandai selesai.');
    }
}
