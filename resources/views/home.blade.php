<x-app-layout>
    <section class="mx-auto max-w-6xl px-4 sm:px-6 pt-12 pb-10">
        <div class="max-w-2xl">
            <span class="inline-block rounded-full bg-olive-100 px-3 py-1 text-xs font-medium text-olive-700">Sewa Properti Terpercaya</span>
            <h1 class="mt-4 text-3xl sm:text-4xl font-semibold text-olive-900 leading-tight">
                Temukan kos, rumah, dan ruko impian Anda
            </h1>
            <p class="mt-4 text-olive-700">
                Jelajahi katalog properti sewa kami, ajukan reservasi, dan urus semuanya secara online - dari verifikasi hingga pembayaran.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('katalog.index') }}"
                   class="rounded-full bg-olive-700 px-6 py-3 text-sm font-medium text-white hover:bg-olive-800">
                    Lihat Katalog
                </a>
                <a href="{{ route('cara-kerja') }}"
                   class="rounded-full border border-olive-300 px-6 py-3 text-sm font-medium text-olive-800 hover:bg-cream-100">
                    Cara Kerja
                </a>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 sm:px-6 pb-16">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-olive-900">Properti Unggulan</h2>
            <a href="{{ route('katalog.index') }}" class="text-sm font-medium text-olive-700 hover:underline">Lihat semua</a>
        </div>

        @if ($featured->isEmpty())
            <p class="text-olive-600">Belum ada properti yang dipublikasikan.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($featured as $property)
                    @include('partials.property-card', ['property' => $property])
                @endforeach
            </div>
        @endif
    </section>
</x-app-layout>
