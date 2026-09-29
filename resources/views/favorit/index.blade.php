<x-account-layout :title="'Favorit Saya'">
    <div class="grid grid-cols-1 lg:grid-cols-[260px_1fr] gap-6 items-start">
        @include('partials.account-menu', ['active' => 'favorit'])

        <div>
            @if ($properties->isEmpty())
                <div class="rounded-2xl border border-olive-100 bg-white p-8 text-center text-olive-600">
                    Belum ada properti favorit. <a href="{{ route('katalog.index') }}" class="text-olive-800 font-medium hover:underline">Lihat katalog</a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @foreach ($properties as $property)
                        @include('partials.property-card', ['property' => $property])
                    @endforeach
                </div>

                <div class="mt-8">{{ $properties->links() }}</div>
            @endif
        </div>
    </div>
</x-account-layout>
