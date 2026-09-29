@php
    $cards = [
        ['label' => 'Total Properti', 'value' => $counts['properti'], 'route' => 'admin.properti.index'],
        ['label' => 'Reservasi Menunggu', 'value' => $counts['reservasi_pending'], 'route' => 'admin.reservasi.index', 'params' => ['status' => 'pending']],
        ['label' => 'Jadwal Kunjungan Menunggu', 'value' => $counts['inspeksi_pending'], 'route' => 'admin.inspeksi.index'],
        ['label' => 'Pembayaran Menunggu', 'value' => $counts['pembayaran_review'], 'route' => 'admin.pembayaran.index'],
        ['label' => 'Perpanjangan Menunggu', 'value' => $counts['perpanjangan_pending'], 'route' => 'admin.perpanjangan.index'],
        ['label' => 'Laporan Kerusakan Baru', 'value' => $counts['kerusakan_baru'], 'route' => 'admin.kerusakan.index'],
    ];
@endphp
<x-dashboard-layout :title="'Dashboard'">
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($cards as $card)
            <a href="{{ route($card['route'], $card['params'] ?? []) }}" class="rounded-2xl border border-olive-100 bg-white p-5 hover:border-olive-300">
                <p class="text-2xl font-semibold text-olive-900">{{ $card['value'] }}</p>
                <p class="text-sm text-olive-600 mt-1">{{ $card['label'] }}</p>
            </a>
        @endforeach

        <a href="{{ route('admin.penyewa.index') }}" class="rounded-2xl border border-olive-100 bg-white p-5 hover:border-olive-300">
            <p class="text-2xl font-semibold text-olive-900">{{ $counts['sewa_aktif'] }}</p>
            <p class="text-sm text-olive-600 mt-1">Sewa Aktif</p>
        </a>
    </div>

    <div class="mt-8 rounded-2xl border border-olive-100 bg-white p-6">
        <h2 class="font-semibold text-olive-900 mb-4">Reservasi Terbaru</h2>
        @if ($reservasiTerbaru->isEmpty())
            <p class="text-sm text-olive-600">Belum ada reservasi.</p>
        @else
            <div class="space-y-2">
                @foreach ($reservasiTerbaru as $reservation)
                    <a href="{{ route('admin.reservasi.index', ['status' => $reservation->status, 'selected' => $reservation->id]) }}"
                       class="flex items-center justify-between rounded-lg px-3 py-2 hover:bg-cream-100">
                        <span class="text-sm text-olive-800">{{ $reservation->penyewa->nama }} · {{ $reservation->property->nama }}</span>
                        @include('partials.status-badge', ['status' => $reservation->displayStatus(), 'label' => $reservation->statusLabel()])
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-dashboard-layout>
