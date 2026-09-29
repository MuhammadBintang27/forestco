@php
    $cover = $property->photos->first();
    $typeLabel = ['kos' => 'Kos', 'rumah' => 'Rumah', 'ruko' => 'Ruko'][$property->tipe] ?? $property->tipe;
    $highlight = $property->highlightLine();
@endphp
<a href="{{ route('katalog.show', $property) }}"
   class="group block overflow-hidden rounded-2xl border border-olive-100 bg-white transition hover:shadow-md">
    <div class="relative aspect-[4/3] w-full overflow-hidden bg-cream-200">
        @if ($cover)
            <img src="{{ $cover->url() }}" alt="{{ $property->nama }}"
                 class="h-full w-full object-cover transition group-hover:scale-105">
        @else
            <div class="flex h-full w-full items-center justify-center text-olive-400 text-sm">Tidak ada foto</div>
        @endif

        <span class="absolute top-3 right-3 rounded-full bg-white/90 px-2.5 py-0.5 text-xs font-medium text-olive-800 shadow-sm">
            {{ strtoupper($typeLabel) }}
        </span>
    </div>
    <div class="p-4 space-y-1.5">
        <h3 class="font-semibold text-olive-900">{{ $property->nama }}</h3>
        <p class="text-sm text-olive-600 truncate">{{ $property->alamat }}</p>

        @if ($highlight !== '')
            <p class="text-xs text-olive-500 truncate">{{ $highlight }}</p>
        @endif

        <div class="flex items-center justify-between pt-1">
            <p class="font-semibold text-olive-900">
                {{ \App\Support\Money::compact($property->primaryPrice()) }}
                <span class="text-sm font-normal text-olive-600">/{{ $property->primaryPriceUnit() }}</span>
            </p>
            <span class="text-sm font-medium text-olive-700 group-hover:underline">Lihat detail →</span>
        </div>
    </div>
</a>
