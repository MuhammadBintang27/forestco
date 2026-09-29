<x-account-layout :title="'Lapor Kerusakan'" :subtitle="'Laporkan dan pantau kerusakan unit Anda.'">
    <div class="grid grid-cols-1 lg:grid-cols-[260px_1fr] gap-6 items-start">
        @include('partials.account-menu', ['active' => 'kerusakan'])

        <div class="space-y-6">
            @if ($activeRentals->isNotEmpty())
                <div class="rounded-2xl border border-olive-100 bg-white p-5" x-data="{ open: false }">
                    <div class="flex items-center justify-between">
                        <h2 class="font-semibold text-olive-900">Lapor Kerusakan Baru</h2>
                        <button type="button" @click="open = !open" class="text-sm font-medium text-olive-700 hover:underline" x-text="open ? 'Tutup' : '+ Buat Laporan'"></button>
                    </div>

                    <div x-show="open" x-cloak class="mt-4 space-y-3">
                        @foreach ($activeRentals as $rental)
                            <form method="POST" action="{{ route('kerusakan.store', $rental) }}" enctype="multipart/form-data" class="space-y-3 {{ $activeRentals->count() > 1 ? 'border border-olive-100 rounded-xl p-4' : '' }}">
                                @csrf
                                @if ($activeRentals->count() > 1)
                                    <p class="text-sm font-medium text-olive-900">
                                        {{ $rental->reservation->property->nama }}
                                        @if ($rental->reservation->room->hasDisplayableKode()) - Kamar {{ $rental->reservation->room->kode }} @endif
                                    </p>
                                @endif
                                <label class="block text-sm text-olive-700">
                                    Judul Kerusakan
                                    <input type="text" name="judul" required placeholder="Contoh: AC tidak dingin">
                                </label>
                                <label class="block text-sm text-olive-700">
                                    Deskripsi
                                    <textarea name="deskripsi" rows="3" required placeholder="Jelaskan kerusakan secara detail"></textarea>
                                </label>
                                <label class="block text-sm text-olive-700">
                                    Foto (opsional)
                                    <input type="file" name="photo" accept=".jpg,.jpeg,.png"
                                           class="mt-1 text-sm text-olive-700 file:mr-3 file:rounded-full file:border-0 file:bg-olive-100 file:px-3 file:py-1.5 file:text-olive-800">
                                </label>
                                <button type="submit" class="rounded-full bg-olive-700 px-5 py-2 text-sm font-medium text-white hover:bg-olive-800">
                                    Kirim Laporan
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="rounded-2xl border border-olive-100 bg-white p-5">
                <h2 class="font-semibold text-olive-900 mb-4">Riwayat Laporan Kerusakan</h2>

                @if ($damageReports->isEmpty())
                    <p class="text-sm text-olive-600 py-6 text-center">Belum ada laporan kerusakan.</p>
                @else
                    <div class="space-y-2">
                        @foreach ($damageReports as $report)
                            <div class="rounded-xl border border-olive-100 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-medium text-olive-900">{{ $report->judul }}</p>
                                        <p class="text-xs text-olive-500">
                                            {{ $report->rental->reservation->property->nama }}
                                            @if ($report->rental->reservation->room->hasDisplayableKode()) - Kamar {{ $report->rental->reservation->room->kode }} @endif
                                            · {{ $report->created_at->translatedFormat('d M Y') }}
                                        </p>
                                    </div>
                                    @include('partials.status-badge', ['status' => $report->status, 'label' => $report->statusLabel()])
                                </div>
                                <p class="text-sm text-olive-600 mt-2">{{ $report->deskripsi }}</p>
                                @if ($report->foto_path)
                                    <a href="{{ $report->photoUrl() }}" target="_blank" class="mt-1 inline-block text-xs font-medium text-olive-700 hover:underline">Lihat foto laporan Anda</a>
                                @endif
                                @if ($report->catatan_admin)
                                    <p class="text-sm text-olive-500 mt-1 italic">Catatan admin: {{ $report->catatan_admin }}</p>
                                @endif
                                @if ($report->status === 'selesai' && $report->foto_selesai_path)
                                    <div class="mt-2 rounded-lg bg-cream-100 px-3 py-2">
                                        <p class="text-xs font-medium text-olive-700 mb-1">Bukti perbaikan dari admin:</p>
                                        <a href="{{ $report->photoSelesaiUrl() }}" target="_blank">
                                            <img src="{{ $report->photoSelesaiUrl() }}" alt="Bukti selesai" class="h-24 rounded-lg object-cover">
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-account-layout>
