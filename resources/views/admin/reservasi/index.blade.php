@php
    // Cuma status yang benar-benar jadi urusan halaman ini: keputusan awal atas
    // reservasi. Setelah "Terverifikasi", lanjutannya dipantau di halaman lain
    // (Jadwal Kunjungan, Kelola Pembayaran) - bukan di sini lagi.
    $statuses = ['' => 'Semua Status', 'pending' => 'Menunggu Verifikasi', 'verified' => 'Terverifikasi', 'rejected' => 'Ditolak', 'dibatalkan' => 'Dibatalkan'];
@endphp
<x-dashboard-layout :title="'Verifikasi Reservasi'">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        @include('partials.stat-card', ['label' => 'Menunggu Verifikasi', 'value' => $counts['menunggu']])
        @include('partials.stat-card', ['label' => 'Disetujui Bulan Ini', 'value' => $counts['disetujui_bulan_ini']])
        @include('partials.stat-card', ['label' => 'Ditolak Bulan Ini', 'value' => $counts['ditolak_bulan_ini']])
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-6 items-start">
        {{-- List --}}
        <div class="rounded-2xl border border-olive-100 bg-white p-5">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <h2 class="font-semibold text-olive-900">Daftar Pengajuan Reservasi</h2>
                <form method="GET">
                    <select name="status" onchange="this.form.submit()" class="w-auto">
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            @if ($reservations->isEmpty())
                <p class="text-sm text-olive-600 py-6 text-center">Tidak ada reservasi dengan status ini.</p>
            @else
                <div class="space-y-1.5">
                    @foreach ($reservations as $reservation)
                        <a href="{{ route('admin.reservasi.index', ['status' => $status, 'selected' => $reservation->id]) }}"
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 {{ $selected?->id === $reservation->id ? 'bg-cream-100 ring-1 ring-olive-200' : 'hover:bg-cream-100' }}">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-olive-700 text-xs font-semibold text-white">
                                {{ $reservation->penyewa->initials() }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-olive-900 truncate">{{ $reservation->penyewa->nama }}</p>
                                <p class="text-xs text-olive-500 truncate">
                                    {{ $reservation->property->nama }}
                                    @if ($reservation->room->hasDisplayableKode()) · Kamar {{ $reservation->room->kode }} @endif
                                    · diajukan {{ $reservation->created_at->translatedFormat('d M Y') }}
                                </p>
                            </div>
                            @include('partials.status-badge', ['status' => $reservation->status, 'label' => $reservation->statusLabel()])
                        </a>
                    @endforeach
                </div>

                <div class="mt-6">{{ $reservations->links() }}</div>
            @endif
        </div>

        {{-- Detail --}}
        <div class="rounded-2xl border border-olive-100 bg-white p-5 lg:sticky lg:top-6">
            <h2 class="font-semibold text-olive-900 mb-4">Detail Pengajuan</h2>

            @if (! $selected)
                <p class="text-sm text-olive-600">Pilih salah satu pengajuan di daftar untuk melihat detailnya.</p>
            @else
                @php
                    $cover = $selected->property->photos->first();
                    $available = $selected->isUnitStillAvailable();
                @endphp

                <div class="flex items-start gap-3">
                    <div class="h-14 w-14 shrink-0 overflow-hidden rounded-lg bg-cream-200">
                        @if ($cover)
                            <img src="{{ $cover->url() }}" class="h-full w-full object-cover" alt="{{ $selected->property->nama }}">
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-olive-900 truncate">{{ $selected->property->nama }}</p>
                        <p class="text-xs text-olive-500 truncate">{{ $selected->property->alamat }}</p>
                        <p class="text-sm font-medium text-olive-900">{{ \App\Support\Money::rupiah($selected->room->primaryPrice()) }} / {{ $selected->room->primaryPriceUnit() }}</p>
                    </div>
                </div>

                @if ($selected->status === 'pending')
                    <div class="mt-4 rounded-lg px-3 py-2 text-sm {{ $available ? 'bg-olive-100 text-olive-800' : 'bg-red-50 text-red-700' }}">
                        {{ $available ? '✓ Unit tersedia untuk periode diajukan' : '✕ Unit sudah tidak tersedia' }}
                    </div>
                @endif

                <dl class="mt-4 space-y-2 text-sm border-t border-olive-100 pt-4">
                    <div class="flex justify-between gap-3">
                        <dt class="text-olive-500">Nama Penyewa</dt>
                        <dd class="font-medium text-olive-900 text-right">{{ $selected->penyewa->nama }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-olive-500">No. Telepon</dt>
                        <dd class="font-medium text-olive-900 text-right">{{ $selected->penyewa->no_telepon }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-olive-500">Tanggal Diajukan</dt>
                        <dd class="font-medium text-olive-900 text-right">{{ $selected->created_at->translatedFormat('d M Y, H:i') }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-olive-500">Durasi</dt>
                        <dd class="font-medium text-olive-900 text-right">{{ $selected->durasi_bulan }} Bulan</dd>
                    </div>
                    @if ($selected->rental)
                        <div class="flex justify-between gap-3">
                            <dt class="text-olive-500">Mulai Sewa</dt>
                            <dd class="font-medium text-olive-900 text-right">{{ $selected->rental->tanggal_mulai->translatedFormat('d M Y') }}</dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-olive-500">Selesai Sewa</dt>
                            <dd class="font-medium text-olive-900 text-right">{{ $selected->rental->tanggal_selesai->translatedFormat('d M Y') }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between gap-3">
                        <dt class="text-olive-500">Nomor Reservasi</dt>
                        <dd class="font-medium text-olive-900 text-right">{{ $selected->nomor() }}</dd>
                    </div>
                </dl>

                @if ($selected->catatan)
                    <div class="mt-4 rounded-lg bg-cream-100 px-3 py-2.5 text-sm text-olive-700">
                        <p class="text-xs font-medium text-olive-500 mb-1">Catatan dari penyewa</p>
                        "{{ $selected->catatan }}"
                    </div>
                @endif

                @if ($selected->status === 'rejected' && $selected->catatan_admin)
                    <div class="mt-4 rounded-lg bg-red-50 px-3 py-2.5 text-sm text-red-700">
                        Alasan penolakan: {{ $selected->catatan_admin }}
                    </div>
                @endif

                @if ($selected->status === 'pending')
                    <form method="POST" action="{{ route('admin.reservasi.review', $selected) }}" x-data="{ note: '' }" class="mt-5 space-y-3">
                        @csrf
                        <input type="text" name="catatan_admin" x-model="note" placeholder="Catatan (opsional, terutama jika menolak)">
                        <div class="flex gap-3">
                            <button type="submit" name="decision" value="reject"
                                    class="flex-1 rounded-full border border-red-300 px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50">
                                Tolak
                            </button>
                            <button type="submit" name="decision" value="verify"
                                    class="flex-1 rounded-full bg-olive-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-olive-800">
                                Setujui Reservasi
                            </button>
                        </div>
                    </form>
                @endif
            @endif
        </div>
    </div>
</x-dashboard-layout>
