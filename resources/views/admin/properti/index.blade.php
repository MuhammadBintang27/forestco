@php
    $typeLabel = ['kos' => 'Kos', 'rumah' => 'Rumah', 'ruko' => 'Ruko'];
@endphp
<x-dashboard-layout :title="'Properti'">
    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.properti.create') }}" class="rounded-full bg-olive-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-olive-800">
            + Tambah Properti
        </a>
    </div>

    @if ($properties->isEmpty())
        <div class="rounded-2xl border border-olive-100 bg-white p-8 text-center text-olive-600">
            Belum ada properti. Tambahkan properti pertama Anda.
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($properties as $property)
                <div class="rounded-2xl border border-olive-100 bg-white p-5">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="inline-block rounded-full bg-mustard-100 px-2.5 py-0.5 text-xs font-medium text-olive-800">
                                {{ strtoupper($typeLabel[$property->tipe] ?? $property->tipe) }}
                            </span>
                            <h3 class="mt-2 font-semibold text-olive-900">{{ $property->nama }}</h3>
                        </div>
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $property->status === 'published' ? 'bg-olive-700 text-white' : 'bg-olive-100 text-olive-700' }}">
                            {{ ucfirst($property->status) }}
                        </span>
                    </div>
                    <p class="text-sm text-olive-600 mt-1 truncate">{{ $property->alamat }}</p>
                    <p class="text-sm text-olive-600 mt-2">
                        {{ $property->photos_count }} foto
                        @if ($property->tipe === 'kos') · {{ $property->rooms_count }} kamar @endif
                        · {{ $property->reservations_count }} reservasi
                    </p>

                    @if ($property->tipe === 'kos')
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium {{ $property->availableRoomsCount() > 0 ? 'text-olive-700' : 'text-red-600' }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $property->availableRoomsCount() > 0 ? 'bg-olive-500' : 'bg-red-500' }}"></span>
                            {{ $property->availableRoomsCount() }} dari {{ $property->rooms_count }} kamar tersedia
                        </p>
                    @else
                        @php $isDisewa = $property->soleUnit()?->status === 'terisi'; @endphp
                        <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium {{ $isDisewa ? 'text-red-600' : 'text-olive-700' }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $isDisewa ? 'bg-red-500' : 'bg-olive-500' }}"></span>
                            {{ $isDisewa ? 'Sedang Disewa' : 'Tersedia' }}
                        </p>
                    @endif
                    <p class="font-semibold text-olive-900 mt-2">
                        {{ \App\Support\Money::rupiah($property->primaryPrice()) }}/{{ $property->primaryPriceUnit() }}
                        @if ($property->isKos() && $property->displayYearlyPrice())
                            <span class="block text-xs font-normal text-olive-500">{{ \App\Support\Money::rupiah($property->displayYearlyPrice()) }}/tahun</span>
                        @endif
                    </p>

                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('admin.properti.edit', $property) }}" class="flex-1 text-center rounded-full border border-olive-300 px-3 py-2 text-xs font-medium text-olive-800 hover:bg-cream-100">
                            Kelola
                        </a>
                        <a href="{{ route('katalog.show', $property) }}" target="_blank" class="flex-1 text-center rounded-full border border-olive-300 px-3 py-2 text-xs font-medium text-olive-800 hover:bg-cream-100">
                            Lihat
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">{{ $properties->links() }}</div>
    @endif
</x-dashboard-layout>
