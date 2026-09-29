@php
    $typeLabel = ['kos' => 'Kos', 'rumah' => 'Rumah', 'ruko' => 'Ruko'][$property->tipe] ?? $property->tipe;
    $photos = $property->photos;
    $soleUnit = $property->soleUnit();
    $durasiOptions = \App\Models\Reservasi::durasiOptionsFor($property->tipe);

    $infoCards = [];
    if ($property->isKos()) {
        $infoCards[] = ['value' => $property->availableRoomsCount(), 'label' => 'Kamar Tersedia'];
    } else {
        if ($soleUnit?->tipe_kamar_mandi) {
            $infoCards[] = ['value' => $soleUnit->tipe_kamar_mandi, 'label' => 'Kamar Mandi'];
        }
        if ($soleUnit?->ukuran_kamar) {
            $infoCards[] = ['value' => $soleUnit->ukuran_kamar, 'label' => 'Ukuran'];
        }
    }
    if (empty($infoCards)) {
        $infoCards[] = ['value' => implode('/', array_keys($durasiOptions)).' Bln', 'label' => 'Pilihan Durasi'];
    }

    $isFavorited = auth()->check() && $property->isFavoritedBy(auth()->user());
@endphp
<x-app-layout :title="$property->nama">
    <section class="mx-auto max-w-6xl px-4 sm:px-6 py-8">
        <p class="text-sm text-olive-500">
            <a href="{{ route('home') }}" class="hover:underline">Beranda</a> /
            <a href="{{ route('katalog.index') }}" class="hover:underline">Katalog</a> /
            {{ $property->nama }}
        </p>

        <div class="mt-3">
            <span class="inline-block rounded-full bg-mustard-100 px-2.5 py-1 text-xs font-medium text-olive-800">
                {{ strtoupper($typeLabel) }}
            </span>
            <h1 class="mt-2 text-2xl sm:text-3xl font-semibold text-olive-900">{{ $property->nama }}</h1>
            <p class="mt-1 text-olive-600">{{ $property->alamat }}</p>
        </div>

        {{-- Gallery: max 3 photos --}}
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-3">
            @forelse ($photos as $photo)
                <div class="aspect-[4/3] overflow-hidden rounded-2xl bg-cream-200 {{ $loop->first ? 'sm:col-span-2 sm:row-span-2' : '' }}">
                    <img src="{{ $photo->url() }}" alt="{{ $property->nama }}" class="h-full w-full object-cover">
                </div>
            @empty
                <div class="sm:col-span-3 aspect-[16/6] flex items-center justify-center rounded-2xl bg-cream-200 text-olive-400">
                    Belum ada foto
                </div>
            @endforelse
        </div>

        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8"
             x-data="{
                tab: 'deskripsi',
                selectedRoomId: {{ $soleUnit?->id ?? 'null' }},
                selectedRoomCode: {{ $soleUnit ? \Illuminate\Support\Js::from($soleUnit->kode) : 'null' }},
                selectedRoomPrice: {{ $soleUnit?->effectivePrice() ?? 0 }},
                selectedRoomYearlyPrice: {{ $soleUnit?->effectiveYearlyPrice() ?? 'null' }},
             }">
            {{-- Left: tabs --}}
            <div class="lg:col-span-2">
                <div class="flex gap-6 border-b border-olive-100 text-sm">
                    @foreach (['deskripsi' => 'Deskripsi', 'fasilitas' => 'Fasilitas', 'lokasi' => 'Lokasi'] as $key => $label)
                        <button @click="tab = '{{ $key }}'"
                                :class="tab === '{{ $key }}' ? 'border-olive-700 text-olive-900 font-semibold' : 'border-transparent text-olive-500'"
                                class="border-b-2 pb-3 -mb-px">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                <div class="py-6">
                    <div x-show="tab === 'deskripsi'" x-cloak class="space-y-5">
                        <div class="grid gap-3 text-center" style="grid-template-columns: repeat({{ count($infoCards) }}, minmax(0, 1fr));">
                            @foreach ($infoCards as $card)
                                <div class="rounded-xl border border-olive-100 p-4">
                                    <p class="text-lg font-semibold text-olive-900">{{ $card['value'] }}</p>
                                    <p class="text-xs text-olive-600">{{ $card['label'] }}</p>
                                </div>
                            @endforeach
                        </div>

                        <div>
                            <h3 class="font-semibold text-olive-900 mb-2">Deskripsi Unit</h3>
                            <p class="text-sm text-olive-700 leading-relaxed whitespace-pre-line">{{ $property->deskripsi ?: 'Belum ada deskripsi.' }}</p>
                        </div>

                        @if ($property->isKos())
                            <div>
                                <h3 class="font-semibold text-olive-900 mb-3">Pilih Kamar</h3>
                                <div class="flex items-center gap-4 text-xs text-olive-600 mb-3">
                                    <span class="flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-olive-700"></span> Dipilih</span>
                                    <span class="flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full border border-olive-300"></span> Tersedia</span>
                                    <span class="flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-olive-100"></span> Sudah Terisi</span>
                                </div>
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach ($property->rooms as $room)
                                        <button type="button"
                                                @if ($room->isAvailable())
                                                    @click="selectedRoomId = {{ $room->id }}; selectedRoomCode = '{{ $room->kode }}'; selectedRoomPrice = {{ $room->effectivePrice() }}; selectedRoomYearlyPrice = {{ $room->effectiveYearlyPrice() ?? 'null' }}"
                                                @else
                                                    disabled
                                                @endif
                                                :class="selectedRoomId === {{ $room->id }} ? 'bg-olive-700 text-white border-olive-700' : ''"
                                                class="room-btn rounded-lg border px-3 py-2 text-sm font-medium text-center
                                                       {{ $room->isAvailable() ? 'border-olive-300 text-olive-800 hover:border-olive-700' : 'border-olive-100 bg-olive-50 text-olive-300 cursor-not-allowed' }}">
                                            {{ $room->kode }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div x-show="tab === 'fasilitas'" x-cloak>
                        @if ($property->facilities->isEmpty())
                            <p class="text-sm text-olive-600">Belum ada fasilitas yang dicantumkan.</p>
                        @else
                            <div class="flex flex-wrap gap-2">
                                @foreach ($property->facilities as $facility)
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-olive-200 px-3 py-1.5 text-sm text-olive-700">
                                        @include('partials.facility-icon', ['name' => $facility->nama])
                                        {{ $facility->nama }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div x-show="tab === 'lokasi'" x-cloak>
                        <p class="text-sm text-olive-700">{{ $property->alamat }}</p>
                        <a target="_blank" rel="noopener"
                           href="https://www.google.com/maps/search/{{ urlencode($property->alamat) }}"
                           class="mt-3 inline-block text-sm font-medium text-olive-700 hover:underline">
                            Lihat di Google Maps →
                        </a>
                    </div>

                </div>
            </div>

            {{-- Right: booking card --}}
            <div>
                <div class="sticky top-6 rounded-2xl border border-olive-100 bg-white p-5">

                    @if ($property->isKos())
                        <p class="text-2xl font-semibold text-olive-900">
                            <span x-text="'Rp' + selectedRoomPrice.toLocaleString('id-ID')"></span>
                            <span class="text-sm font-normal text-olive-600">/bulan</span>
                        </p>
                        <p class="text-sm text-olive-600" x-show="selectedRoomYearlyPrice" x-cloak>
                            atau <span x-text="'Rp' + (selectedRoomYearlyPrice ?? 0).toLocaleString('id-ID')"></span> /tahun
                        </p>
                    @else
                        {{-- Rumah/ruko cuma punya harga tahunan (durasi selalu kelipatan tahun). --}}
                        <p class="text-2xl font-semibold text-olive-900">
                            {{ \App\Support\Money::rupiah($soleUnit?->effectiveYearlyPrice()) }}
                            <span class="text-sm font-normal text-olive-600">/tahun</span>
                        </p>
                    @endif

                    @if ($property->isKos())
                        <p class="mt-1 flex items-center gap-1.5 text-sm text-olive-600">
                            <span class="h-1.5 w-1.5 rounded-full bg-olive-400"></span>
                            {{ $property->availableRoomsCount() }} dari {{ $property->rooms->count() }} kamar tersedia
                        </p>
                    @endif

                    <dl class="mt-4 space-y-2 text-sm border-t border-olive-100 pt-4">
                        <div class="flex justify-between">
                            <dt class="text-olive-600">Jenis Properti</dt>
                            <dd class="font-medium text-olive-900">{{ ucfirst($typeLabel) }}</dd>
                        </div>
                        @if ($property->isKos())
                            <div class="flex justify-between">
                                <dt class="text-olive-600">Kamar Dipilih</dt>
                                <dd class="font-medium text-olive-900" x-text="selectedRoomCode ?? '-'"></dd>
                            </div>
                        @endif
                    </dl>

                    @if (! $property->isAvailable())
                        <div class="mt-5 rounded-lg bg-olive-100 px-4 py-3 text-center text-sm font-medium text-olive-800">
                            {{ $property->isKos() ? 'Semua kamar sedang terisi' : 'Properti ini sedang disewa' }}
                        </div>
                    @elseif (auth()->check())
                        @if (auth()->user()->isPenyewa())
                            <form method="POST" action="{{ route('reservasi.store') }}" class="mt-5 space-y-3">
                                @csrf
                                <input type="hidden" name="properti_id" value="{{ $property->id }}">
                                <input type="hidden" name="unit_id" x-bind:value="selectedRoomId">

                                <label class="block text-sm text-olive-700">
                                    Durasi Sewa
                                    <select name="durasi_bulan" class="mt-1 w-full rounded-lg border-olive-200 text-sm focus:border-olive-500 focus:ring-olive-500">
                                        @foreach ($durasiOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </label>

                                <label class="block text-sm text-olive-700">
                                    Catatan untuk Admin (opsional)
                                    <textarea name="catatan" rows="2" placeholder="Mis. rencana pindah tanggal berapa, dsb."
                                              class="mt-1 w-full rounded-lg border-olive-200 text-sm focus:border-olive-500 focus:ring-olive-500"></textarea>
                                </label>

                                <button type="submit"
                                        @if ($property->isKos()) x-bind:disabled="!selectedRoomId" @endif
                                        class="w-full rounded-full bg-olive-700 px-5 py-3 text-sm font-medium text-white hover:bg-olive-800 disabled:opacity-40 disabled:cursor-not-allowed">
                                    Ajukan Reservasi →
                                </button>
                            </form>

                            <form method="POST" action="{{ route('favorit.toggle', $property) }}" class="mt-2">
                                @csrf
                                <button type="submit"
                                        class="w-full rounded-full border border-olive-300 px-5 py-3 text-sm font-medium text-olive-800 hover:bg-cream-100">
                                    {{ $isFavorited ? '♥ Tersimpan di Favorit' : 'Simpan ke Favorit' }}
                                </button>
                            </form>
                        @else
                            <p class="mt-5 text-sm text-olive-600">Masuk sebagai akun Penyewa untuk mengajukan reservasi.</p>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                           class="mt-5 block w-full rounded-full bg-olive-700 px-5 py-3 text-center text-sm font-medium text-white hover:bg-olive-800">
                            Masuk untuk Reservasi →
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
