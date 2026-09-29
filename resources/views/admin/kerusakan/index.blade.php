@php
    $statuses = ['' => 'Semua Status', 'baru' => 'Laporan Baru', 'diproses' => 'Sedang Ditindaklanjuti', 'selesai' => 'Selesai'];
@endphp
<x-dashboard-layout :title="'Laporan Kerusakan'">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        @include('partials.stat-card', ['label' => 'Belum Ditindaklanjuti', 'value' => $counts['baru']])
        @include('partials.stat-card', ['label' => 'Sedang Ditindaklanjuti', 'value' => $counts['diproses']])
        @include('partials.stat-card', ['label' => 'Selesai Bulan Ini', 'value' => $counts['selesai_bulan_ini']])
    </div>

    <div class="rounded-2xl border border-olive-100 bg-white p-5">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <h2 class="font-semibold text-olive-900">Daftar Laporan Kerusakan</h2>
            <form method="GET">
                <select name="status" onchange="this.form.submit()" class="w-auto">
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        @if ($reports->isEmpty())
            <p class="text-sm text-olive-600 py-6 text-center">Tidak ada laporan kerusakan.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-olive-100 text-left text-xs uppercase tracking-wide text-olive-500">
                            <th class="py-2 pr-4">Unit</th>
                            <th class="py-2 pr-4">Dilaporkan Oleh</th>
                            <th class="py-2 pr-4">Judul</th>
                            <th class="py-2 pr-4">Tanggal Lapor</th>
                            <th class="py-2 pr-4">Status</th>
                            <th class="py-2 pr-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                            @php $reservation = $report->rental->reservation; @endphp
                            <tr class="border-b border-olive-50" x-data="{ open: false }">
                                <td class="py-3 pr-4 font-medium text-olive-900">
                                    {{ $reservation->property->nama }}
                                    @if ($reservation->room->hasDisplayableKode()) - Kamar {{ $reservation->room->kode }} @endif
                                </td>
                                <td class="py-3 pr-4 text-olive-700">{{ $reservation->penyewa->nama }}</td>
                                <td class="py-3 pr-4 text-olive-700 max-w-xs truncate">{{ $report->judul }}</td>
                                <td class="py-3 pr-4 text-olive-700 whitespace-nowrap">{{ $report->created_at->translatedFormat('d M Y') }}</td>
                                <td class="py-3 pr-4">
                                    @include('partials.status-badge', ['status' => $report->status, 'label' => $report->statusLabel()])
                                </td>
                                <td class="py-3 pr-4">
                                    <button type="button" @click="open = true"
                                            class="rounded-full border border-olive-300 px-3 py-1.5 text-xs font-medium text-olive-800 hover:bg-cream-100">
                                        Detail
                                    </button>
                                </td>

                                {{-- Modal detail --}}
                                <template x-if="open">
                                    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="open = false" @keydown.escape.window="open = false">
                                        <div class="relative w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl bg-white p-6">
                                            <button type="button" @click="open = false" class="absolute top-4 right-4 text-olive-400 hover:text-olive-700">✕</button>

                                            <div class="flex items-start justify-between gap-3 pr-6 mb-1">
                                                <h3 class="font-semibold text-olive-900">{{ $report->judul }}</h3>
                                                @include('partials.status-badge', ['status' => $report->status, 'label' => $report->statusLabel()])
                                            </div>
                                            <dl class="space-y-1.5 text-sm border-t border-b border-olive-100 py-3 my-3">
                                                <div class="flex justify-between gap-3">
                                                    <dt class="text-olive-500">Pelapor</dt>
                                                    <dd class="font-medium text-olive-900 text-right">{{ $reservation->penyewa->nama }}</dd>
                                                </div>
                                                <div class="flex justify-between gap-3">
                                                    <dt class="text-olive-500">Unit</dt>
                                                    <dd class="font-medium text-olive-900 text-right">
                                                        {{ $reservation->property->nama }}
                                                        @if ($reservation->room->hasDisplayableKode()) — Kamar {{ $reservation->room->kode }} @endif
                                                    </dd>
                                                </div>
                                                <div class="flex justify-between gap-3">
                                                    <dt class="text-olive-500">Tanggal Lapor</dt>
                                                    <dd class="font-medium text-olive-900 text-right">{{ $report->created_at->translatedFormat('d M Y, H:i') }}</dd>
                                                </div>
                                            </dl>

                                            <p class="text-sm text-olive-700 mb-4">{{ $report->deskripsi }}</p>

                                            <div class="grid grid-cols-2 gap-3 mb-4">
                                                <div>
                                                    <p class="text-xs font-medium text-olive-500 mb-1">Foto Laporan</p>
                                                    @if ($report->foto_path)
                                                        <a href="{{ $report->photoUrl() }}" target="_blank">
                                                            <img src="{{ $report->photoUrl() }}" alt="Foto laporan" class="h-28 w-full rounded-lg object-cover">
                                                        </a>
                                                    @else
                                                        <p class="text-xs text-olive-400">Tidak ada foto</p>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="text-xs font-medium text-olive-500 mb-1">Bukti Selesai</p>
                                                    @if ($report->foto_selesai_path)
                                                        <a href="{{ $report->photoSelesaiUrl() }}" target="_blank">
                                                            <img src="{{ $report->photoSelesaiUrl() }}" alt="Bukti selesai" class="h-28 w-full rounded-lg object-cover">
                                                        </a>
                                                    @else
                                                        <p class="text-xs text-olive-400">Belum ada</p>
                                                    @endif
                                                </div>
                                            </div>

                                            @if ($report->catatan_admin)
                                                <p class="text-sm text-olive-500 italic mb-4">Catatan admin: {{ $report->catatan_admin }}</p>
                                            @endif

                                            @if ($report->status !== 'selesai')
                                                <div class="border-t border-olive-100 pt-4">
                                                    @if ($report->status === 'baru')
                                                        <form method="POST" action="{{ route('admin.kerusakan.status', $report) }}">
                                                            @csrf
                                                            <input type="hidden" name="status" value="diproses">
                                                            <button type="submit" class="rounded-full bg-olive-700 px-4 py-2 text-sm font-medium text-white hover:bg-olive-800">
                                                                Tindak Lanjuti
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form method="POST" action="{{ route('admin.kerusakan.status', $report) }}" enctype="multipart/form-data" class="space-y-2">
                                                            @csrf
                                                            <input type="hidden" name="status" value="selesai">
                                                            <label class="block text-sm text-olive-700">
                                                                Bukti foto selesai
                                                                <input type="file" name="bukti_selesai" required accept=".jpg,.jpeg,.png"
                                                                       class="mt-1 w-full text-sm text-olive-700 file:mr-3 file:rounded-full file:border-0 file:bg-olive-100 file:px-3 file:py-1.5 file:text-olive-800">
                                                            </label>
                                                            <button type="submit" class="rounded-full bg-olive-700 px-4 py-2 text-sm font-medium text-white hover:bg-olive-800">
                                                                Tandai Selesai
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </template>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $reports->links() }}</div>
        @endif
    </div>
</x-dashboard-layout>
