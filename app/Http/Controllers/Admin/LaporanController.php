<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaporanOperasional;
use App\Models\Reservasi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $from = $request->filled('from') ? $request->date('from') : now()->startOfMonth();
        $to = $request->filled('to') ? $request->date('to') : now()->endOfDay();

        $reservations = $this->query($from, $to)->get();

        return view('admin.laporan.index', [
            'reservations' => $reservations,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'totalPembayaran' => $reservations->sum(fn (Reservasi $r) => $r->latestPayment()?->status === 'verified' ? $r->latestPayment()->jumlah : 0),
            'riwayatLaporan' => LaporanOperasional::query()->latest('tanggal_unduh')->take(10)->get(),
        ]);
    }   

    public function csv(Request $request): Response
    {
        $from = $request->filled('from') ? $request->date('from') : now()->startOfMonth();
        $to = $request->filled('to') ? $request->date('to') : now()->endOfDay();

        $reservations = $this->query($from, $to)->get();

        // Catat setiap unduhan sebagai laporan operasional (sesuai ERD).
        LaporanOperasional::create([
            'admin_id' => $request->user()->id,
            'jenis_laporan' => 'reservasi',
            'format_berkas' => 'csv',
            'periode_mulai' => $from->toDateString(),
            'periode_selesai' => $to->toDateString(),
            'tanggal_unduh' => now(),
        ]);

        $rows = "Nomor,Penyewa,Properti,Status,Durasi (bulan),Total,Diajukan\n";

        foreach ($reservations as $r) {
            $rows .= implode(',', [
                $r->nomor(),
                '"'.str_replace('"', '""', $r->penyewa->nama).'"',
                '"'.str_replace('"', '""', $r->property->nama).'"',
                $r->statusLabel(),
                $r->durasi_bulan,
                $r->totalAmount(),
                $r->created_at->format('Y-m-d'),
            ])."\n";
        }

        return response($rows, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="laporan-reservasi-'.$from->toDateString().'-sd-'.$to->toDateString().'.csv"',
        ]);
    }

    private function query($from, $to)
    {
        return Reservasi::query()
            ->with(['penyewa', 'room.property', 'rental.payments'])
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->latest();
    }
}
