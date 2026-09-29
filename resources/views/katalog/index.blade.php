@php
    $typeOptions = ['semua' => 'Semua', 'kos' => 'Kos', 'rumah' => 'Rumah', 'ruko' => 'Ruko'];
    $sortOptions = ['terbaru' => 'Urutkan: Terbaru', 'harga_asc' => 'Urutkan: Harga Terendah', 'harga_desc' => 'Urutkan: Harga Tertinggi'];
@endphp
<x-app-layout :title="'Katalog'">
    <section class="mx-auto max-w-6xl px-4 sm:px-6 py-8">
        <p class="text-sm text-olive-500">
            <a href="{{ route('home') }}" class="hover:underline">Beranda</a> / Katalog Properti
        </p>
        <h1 class="mt-2 text-2xl sm:text-3xl font-semibold text-olive-900">Cari properti sewa</h1>
        <p class="mt-1 text-olive-600">
            Semua unit kos, rumah, dan ruko yang dikelola ForestCo di Banda Aceh &amp; Aceh Besar.
        </p>

        <form method="GET" class="mt-8 grid grid-cols-1 lg:grid-cols-[260px_1fr] gap-8">
            {{-- Sidebar filter --}}
            <div class="space-y-6 lg:sticky lg:top-6 self-start">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-olive-500 mb-2">Jenis Properti</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($typeOptions as $value => $label)
                            <label class="cursor-pointer rounded-full border border-olive-200 px-4 py-1.5 text-sm text-olive-700 transition has-[:checked]:border-olive-700 has-[:checked]:bg-olive-700 has-[:checked]:text-white">
                                <input type="radio" name="type" value="{{ $value }}" class="sr-only" @checked($filters['type'] === $value)>
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-olive-500 mb-2">Lokasi</label>
                    <select name="lokasi">
                        <option value="">Semua Lokasi</option>
                        @foreach ($lokasiOptions as $lokasi)
                            <option value="{{ $lokasi }}" @selected($filters['lokasi'] === $lokasi)>{{ $lokasi }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-olive-500 mb-2">Kisaran Harga / Bulan</p>
                    <div class="flex items-center gap-2">
                        <input type="text" inputmode="numeric" name="harga_min" class="money-input" placeholder="Min" value="{{ \App\Support\Money::digits($filters['harga_min']) }}">
                        <span class="text-olive-400">-</span>
                        <input type="text" inputmode="numeric" name="harga_max" class="money-input" placeholder="Maks" value="{{ \App\Support\Money::digits($filters['harga_max']) }}">
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-olive-500 mb-2">Status</p>
                    <label class="flex items-center gap-2 text-sm text-olive-700">
                        <input type="checkbox" name="tersedia" value="1" @checked($filters['tersedia'])>
                        Hanya tampilkan yang tersedia
                    </label>
                </div>

                <button type="submit" class="w-full rounded-full bg-olive-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-olive-800">
                    Terapkan Filter
                </button>
            </div>

            {{-- Results --}}
            <div>
                <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                    <p class="text-sm text-olive-600">
                        Menampilkan <strong class="text-olive-900">{{ $properties->count() }}</strong>
                        dari <strong class="text-olive-900">{{ $totalPublished }}</strong> properti
                    </p>

                    <select name="sort" onchange="this.form.submit()" class="w-auto">
                        @foreach ($sortOptions as $value => $label)
                            <option value="{{ $value }}" @selected($filters['sort'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                @if ($properties->isEmpty())
                    <p class="text-olive-600">Tidak ada properti yang cocok dengan filter Anda.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                        @foreach ($properties as $property)
                            @include('partials.property-card', ['property' => $property])
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $properties->links() }}
                    </div>
                @endif
            </div>
        </form>
    </section>
</x-app-layout>
