@php
    $statuses = ['' => 'Semua Status', 'review' => 'Menunggu Verifikasi', 'verified' => 'Terverifikasi', 'rejected' => 'Ditolak'];
@endphp
<x-dashboard-layout :title="'Kelola Pembayaran'">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        @include('partials.stat-card', ['label' => 'Menunggu Verifikasi', 'value' => $counts['menunggu']])
        @include('partials.stat-card', ['label' => 'Terverifikasi Bulan Ini', 'value' => $counts['terverifikasi_bulan_ini']])
        @include('partials.stat-card', ['label' => 'Ditolak Bulan Ini', 'value' => $counts['ditolak_bulan_ini']])
    </div>

    <div class="rounded-2xl border border-olive-100 bg-white p-5">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <h2 class="font-semibold text-olive-900">Riwayat &amp; Verifikasi Pembayaran</h2>
            <div class="flex gap-2">
                @foreach ($statuses as $value => $label)
                    <a href="{{ route('admin.pembayaran.index', ['status' => $value]) }}"
                       class="rounded-full px-3 py-1.5 text-xs font-medium {{ $status === $value ? 'bg-olive-700 text-white' : 'bg-cream-100 text-olive-700 hover:bg-cream-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        @if ($payments->isEmpty())
            <p class="text-sm text-olive-600 py-6 text-center">Tidak ada pembayaran.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-olive-100 text-left text-xs uppercase tracking-wide text-olive-500">
                            <th class="py-2 pr-4">Penyewa</th>
                            <th class="py-2 pr-4">Unit</th>
                            <th class="py-2 pr-4">Jumlah</th>
                            <th class="py-2 pr-4">Status</th>
                            <th class="py-2 pr-4">Bukti TF</th>
                            <th class="py-2 pr-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            @php
                                $penyewa = $payment->penyewa();
                                $isExtension = (bool) $payment->perpanjangan_sewa_id;
                                $propertyName = $payment->sewa->reservation->property->nama;
                                $room = $payment->sewa->room;
                            @endphp
                            <tr class="border-b border-olive-50">
                                <td class="py-3 pr-4">
                                    <p class="font-medium text-olive-900">{{ $penyewa?->nama }}</p>
                                    @if ($isExtension)
                                        <span class="text-xs text-olive-500">Perpanjangan sewa</span>
                                    @else
                                        <span class="text-xs text-olive-500">Reservasi baru</span>
                                    @endif
                                </td>
                                <td class="py-3 pr-4 text-olive-700">
                                    {{ $propertyName }}
                                    @if ($room->hasDisplayableKode()) - {{ $room->kode }} @endif
                                </td>
                                <td class="py-3 pr-4 text-olive-700 whitespace-nowrap">{{ \App\Support\Money::rupiah($payment->jumlah) }}</td>
                                <td class="py-3 pr-4">
                                    @include('partials.status-badge', ['status' => $payment->status, 'label' => ucfirst($payment->status)])
                                </td>
                                <td class="py-3 pr-4">
                                    <a href="{{ $payment->proofUrl() }}" target="_blank" class="text-xs font-medium text-olive-700 hover:underline">Lihat bukti</a>
                                </td>
                                <td class="py-3 pr-4">
                                    @if ($payment->status === 'review')
                                        <div class="flex gap-2">
                                            <form method="POST" action="{{ route('admin.pembayaran.review', $payment) }}">
                                                @csrf
                                                <input type="hidden" name="decision" value="verified">
                                                <button type="submit" class="rounded-full bg-olive-700 px-3 py-1.5 text-xs font-medium text-white hover:bg-olive-800">Verifikasi</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.pembayaran.review', $payment) }}">
                                                @csrf
                                                <input type="hidden" name="decision" value="rejected">
                                                <button type="submit" class="rounded-full border border-red-300 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">Batalkan</button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-olive-300">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $payments->links() }}</div>
        @endif
    </div>
</x-dashboard-layout>
