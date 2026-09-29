<x-dashboard-layout :title="'Cetak Laporan'">
    <div class="rounded-2xl border border-olive-100 bg-white p-5 mb-6 print:hidden">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <label class="block text-sm text-olive-700">
                Dari Tanggal
                <input type="date" name="from" value="{{ $from }}">
            </label>
            <label class="block text-sm text-olive-700">
                Sampai Tanggal
                <input type="date" name="to" value="{{ $to }}">
            </label>
            <button type="submit" class="rounded-full bg-olive-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-olive-800">
                Terapkan
            </button>
            <a href="{{ route('admin.laporan.csv', ['from' => $from, 'to' => $to]) }}" class="rounded-full border border-olive-300 px-5 py-2.5 text-sm font-medium text-olive-800 hover:bg-cream-100">
                Unduh CSV
            </a>
            <button type="button" onclick="window.print()" class="rounded-full border border-olive-300 px-5 py-2.5 text-sm font-medium text-olive-800 hover:bg-cream-100">
                Cetak
            </button>
        </form>
    </div>

    <div class="rounded-2xl border border-olive-100 bg-white p-6">
        <div class="flex items-center justify-between mb-1">
            <h2 class="font-semibold text-olive-900">Laporan Reservasi &amp; Pembayaran</h2>
            <p class="text-sm text-olive-500">{{ \Carbon\Carbon::parse($from)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($to)->translatedFormat('d M Y') }}</p>
        </div>
        <p class="text-sm text-olive-600 mb-4">
            Total {{ $reservations->count() }} reservasi · Total pembayaran terverifikasi {{ \App\Support\Money::rupiah($totalPembayaran) }}
        </p>

        @if ($reservations->isEmpty())
            <p class="text-sm text-olive-600 py-6 text-center">Tidak ada data pada periode ini.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-olive-100 text-left text-xs uppercase tracking-wide text-olive-500">
                            <th class="py-2 pr-4">Nomor</th>
                            <th class="py-2 pr-4">Penyewa</th>
                            <th class="py-2 pr-4">Properti</th>
                            <th class="py-2 pr-4">Status</th>
                            <th class="py-2 pr-4">Durasi</th>
                            <th class="py-2 pr-4">Total</th>
                            <th class="py-2 pr-4">Diajukan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reservations as $r)
                            <tr class="border-b border-olive-50">
                                <td class="py-2.5 pr-4 text-olive-700">{{ $r->nomor() }}</td>
                                <td class="py-2.5 pr-4 font-medium text-olive-900">{{ $r->penyewa->nama }}</td>
                                <td class="py-2.5 pr-4 text-olive-700">{{ $r->property->nama }}</td>
                                <td class="py-2.5 pr-4 text-olive-700">{{ $r->statusLabel() }}</td>
                                <td class="py-2.5 pr-4 text-olive-700">{{ $r->durasi_bulan }} bln</td>
                                <td class="py-2.5 pr-4 text-olive-700 whitespace-nowrap">{{ \App\Support\Money::rupiah($r->totalAmount()) }}</td>
                                <td class="py-2.5 pr-4 text-olive-700 whitespace-nowrap">{{ $r->created_at->format('d-m-Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if ($riwayatLaporan->isNotEmpty())
        <div class="rounded-2xl border border-olive-100 bg-white p-6 mt-6 print:hidden">
            <h2 class="font-semibold text-olive-900 mb-3">Riwayat Unduhan Laporan Operasional</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-olive-100 text-left text-xs uppercase tracking-wide text-olive-500">
                            <th class="py-2 pr-4">Jenis</th>
                            <th class="py-2 pr-4">Format</th>
                            <th class="py-2 pr-4">Periode</th>
                            <th class="py-2 pr-4">Diunduh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($riwayatLaporan as $log)
                            <tr class="border-b border-olive-50">
                                <td class="py-2.5 pr-4 text-olive-700">{{ $log->jenisLabel() }}</td>
                                <td class="py-2.5 pr-4 text-olive-700 uppercase">{{ $log->format_berkas }}</td>
                                <td class="py-2.5 pr-4 text-olive-700">{{ $log->periode_mulai->format('d-m-Y') }} - {{ $log->periode_selesai->format('d-m-Y') }}</td>
                                <td class="py-2.5 pr-4 text-olive-700 whitespace-nowrap">{{ $log->tanggal_unduh?->translatedFormat('d M Y, H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <style>
        @media print {
            aside, header.md\:hidden, .print\:hidden { display: none !important; }
            main { padding: 0 !important; }
            body { background: white !important; }
        }
    </style>
</x-dashboard-layout>
