@php
    $statuses = ['' => 'Semua Status', 'pending' => 'Menunggu Konfirmasi', 'approved' => 'Terkonfirmasi', 'done' => 'Selesai Dikunjungi', 'rejected' => 'Ditolak'];
@endphp
<x-dashboard-layout :title="'Jadwal Kunjungan'">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        @include('partials.stat-card', ['label' => 'Menunggu Konfirmasi', 'value' => $counts['menunggu']])
        @include('partials.stat-card', ['label' => 'Terjadwal Minggu Ini', 'value' => $counts['terjadwal_minggu_ini']])
        @include('partials.stat-card', ['label' => 'Selesai Bulan Ini', 'value' => $counts['selesai_bulan_ini']])
    </div>

    <div class="rounded-2xl border border-olive-100 bg-white p-5">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <h2 class="font-semibold text-olive-900">Permintaan Jadwal Kunjungan</h2>
            <form method="GET">
                <select name="status" onchange="this.form.submit()" class="w-auto">
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        @if ($inspections->isEmpty())
            <p class="text-sm text-olive-600 py-6 text-center">Tidak ada jadwal kunjungan.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-olive-100 text-left text-xs uppercase tracking-wide text-olive-500">
                            <th class="py-2 pr-4">Penyewa</th>
                            <th class="py-2 pr-4">Properti</th>
                            <th class="py-2 pr-4">Tanggal Diajukan</th>
                            <th class="py-2 pr-4">Status</th>
                            <th class="py-2 pr-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inspections as $inspection)
                            @php $reservation = $inspection->reservation; @endphp
                            <tr class="border-b border-olive-50">
                                <td class="py-3 pr-4 font-medium text-olive-900">{{ $reservation->penyewa->nama }}</td>
                                <td class="py-3 pr-4 text-olive-700">
                                    {{ $reservation->property->nama }}
                                    @if ($reservation->room->hasDisplayableKode()) - Kamar {{ $reservation->room->kode }} @endif
                                </td>
                                <td class="py-3 pr-4 text-olive-700">{{ $inspection->tanggal_diminta->translatedFormat('d M Y') }}</td>
                                <td class="py-3 pr-4">
                                    @include('partials.status-badge', ['status' => $inspection->status, 'label' => $inspection->statusLabel()])
                                </td>
                                <td class="py-3 pr-4">
                                    @if ($inspection->status === 'pending')
                                        <div class="flex gap-2">
                                            <form method="POST" action="{{ route('admin.inspeksi.review', $inspection) }}">
                                                @csrf
                                                <input type="hidden" name="decision" value="approve">
                                                <button type="submit" class="rounded-full bg-olive-700 px-3 py-1.5 text-xs font-medium text-white hover:bg-olive-800">Konfirmasi</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.inspeksi.review', $inspection) }}">
                                                @csrf
                                                <input type="hidden" name="decision" value="reject">
                                                <button type="submit" class="rounded-full border border-red-300 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">Tolak</button>
                                            </form>
                                        </div>
                                    @elseif ($inspection->status === 'approved')
                                        <form method="POST" action="{{ route('admin.inspeksi.done', $inspection) }}">
                                            @csrf
                                            <button type="submit" class="rounded-full bg-olive-100 px-3 py-1.5 text-xs font-medium text-olive-800 hover:bg-olive-200">Tandai Selesai</button>
                                        </form>
                                    @else
                                        <span class="text-olive-300">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $inspections->links() }}</div>
        @endif
    </div>
</x-dashboard-layout>
