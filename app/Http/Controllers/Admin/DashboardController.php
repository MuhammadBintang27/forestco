<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaporanKerusakan;
use App\Models\JadwalCek;
use App\Models\Pembayaran;
use App\Models\Properti;
use App\Models\Sewa;
use App\Models\PerpanjanganSewa;
use App\Models\Reservasi;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'counts' => [
                'properti' => Properti::query()->count(),
                'reservasi_pending' => Reservasi::query()->where('status', 'pending')->count(),
                'inspeksi_pending' => JadwalCek::query()->where('status', 'pending')->count(),
                'pembayaran_review' => Pembayaran::query()->where('status', 'review')->count(),
                'perpanjangan_pending' => PerpanjanganSewa::query()->where('status', 'requested')->count(),
                'kerusakan_baru' => LaporanKerusakan::query()->where('status', 'baru')->count(),
                'sewa_aktif' => Sewa::query()->where('status', 'active')->count(),
            ],
            'reservasiTerbaru' => Reservasi::query()->with(['penyewa', 'room.property', 'rental.payments'])->latest()->take(5)->get(),
        ]);
    }
}
