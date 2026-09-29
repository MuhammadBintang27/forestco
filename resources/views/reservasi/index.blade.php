<x-account-layout :title="'Riwayat Reservasi'" :subtitle="'Pantau status reservasi Anda.'">
    <div class="grid grid-cols-1 lg:grid-cols-[260px_1fr] gap-6 items-start">
        @include('partials.account-menu', ['active' => 'reservasi'])

        <div class="space-y-3">
            @if ($reservations->isEmpty())
                <div class="rounded-2xl border border-olive-100 bg-white p-8 text-center text-olive-600">
                    Anda belum memiliki reservasi. <a href="{{ route('katalog.index') }}" class="text-olive-800 font-medium hover:underline">Lihat katalog</a>
                </div>
            @else
                @foreach ($reservations as $reservation)
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-olive-100 bg-white p-4">
                        <div>
                            <p class="font-medium text-olive-900">
                                {{ $reservation->property->nama }}
                                @if ($reservation->room->hasDisplayableKode()) - Kamar {{ $reservation->room->kode }} @endif
                            </p>
                            <p class="text-sm text-olive-600">
                                Diajukan {{ $reservation->created_at->translatedFormat('d M Y') }}
                                @if ($reservation->rental)
                                    · {{ $reservation->rental->tanggal_mulai->translatedFormat('d M Y') }} – {{ $reservation->rental->tanggal_selesai->translatedFormat('d M Y') }}
                                @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            @include('partials.status-badge', ['status' => $reservation->displayStatus(), 'label' => $reservation->statusLabel()])
                            <a href="{{ route('reservasi.show', $reservation) }}" class="rounded-full border border-olive-300 px-4 py-1.5 text-sm font-medium text-olive-800 hover:bg-cream-100">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</x-account-layout>
