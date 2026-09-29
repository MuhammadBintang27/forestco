<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaporanKerusakan;
use App\Support\WhatsApp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LaporanKerusakanController extends Controller
{
    public function index(Request $request): View
    {
        // Default ke "Semua Status" - tidak difilter kalau parameter status kosong/absen.
        $status = $request->string('status')->toString();

        $reports = LaporanKerusakan::query()
            ->with(['penyewa', 'rental.reservation.room.property'])
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.kerusakan.index', [
            'reports' => $reports,
            'status' => $status,
            'counts' => [
                'baru' => LaporanKerusakan::query()->where('status', 'baru')->count(),
                'diproses' => LaporanKerusakan::query()->where('status', 'diproses')->count(),
                'selesai_bulan_ini' => LaporanKerusakan::query()->where('status', 'selesai')->whereMonth('updated_at', now()->month)->whereYear('updated_at', now()->year)->count(),
            ],
        ]);
    }

    public function updateStatus(Request $request, LaporanKerusakan $report): RedirectResponse
    {
        if ($report->status === 'selesai') {
            throw ValidationException::withMessages(['status' => 'Laporan ini sudah ditandai selesai.']);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:diproses,selesai'],
            'catatan_admin' => ['nullable', 'string', 'max:500'],
            // Bukti selesai wajib diupload begitu laporan ditandai selesai,
            // supaya penyewa bisa lihat sendiri hasil perbaikannya.
            'bukti_selesai' => [Rule::requiredIf($request->input('status') === 'selesai'), 'nullable', 'file', 'image', 'max:4096'],
        ]);

        $data = [
            'status' => $validated['status'],
            'catatan_admin' => $validated['catatan_admin'] ?? $report->catatan_admin,
        ];

        if ($request->hasFile('bukti_selesai')) {
            $data['foto_selesai_path'] = $request->file('bukti_selesai')->store('kerusakan-selesai', 'public');
        }

        $report->update($data);

        $penyewa = $report->penyewa;
        $propertyName = $report->rental->reservation->property->nama;

        $message = $validated['status'] === 'diproses'
            ? "Halo {$penyewa->nama}, laporan kerusakan \"{$report->judul}\" untuk *{$propertyName}* sedang kami tindaklanjuti."
            : "Halo {$penyewa->nama}, laporan kerusakan \"{$report->judul}\" untuk *{$propertyName}* sudah selesai ditangani. Terima kasih atas laporannya.";

        return redirect()->route('admin.kerusakan.index')
            ->with('success', 'Status laporan kerusakan diperbarui.')
            ->with('wa_redirect', WhatsApp::link($penyewa->no_telepon, $message));
    }
}
