<x-app-layout>
    <section class="bg-cream-100">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 pt-14 pb-12 text-center">
            <span class="inline-block rounded-full bg-olive-100 px-3 py-1 text-xs font-medium text-olive-700">Sewa Properti Terpercaya</span>
            <h1 class="mx-auto mt-4 max-w-3xl text-3xl sm:text-5xl font-semibold text-olive-900 leading-tight">
                Temukan kos, rumah, dan ruko impian Anda
            </h1>
            <p class="mx-auto mt-4 max-w-2xl text-olive-700">
                Jelajahi katalog properti sewa kami, ajukan reservasi, dan urus semuanya secara online - dari verifikasi hingga pembayaran.
            </p>

            <a href="{{ route('katalog.index') }}"
               class="mt-8 inline-block rounded-full bg-olive-700 px-8 py-3 text-sm font-medium text-white hover:bg-olive-800">
                Lihat Katalog
            </a>

            <div class="mx-auto mt-8 grid max-w-2xl grid-cols-3 gap-4 text-center">
                <div><p class="text-xl font-semibold text-olive-900">3 tipe</p><p class="text-xs text-olive-600">Kos, rumah, ruko</p></div>
                <div><p class="text-xl font-semibold text-olive-900">100% online</p><p class="text-xs text-olive-600">Reservasi sampai bayar</p></div>
                <div><p class="text-xl font-semibold text-olive-900">WhatsApp</p><p class="text-xs text-olive-600">Respons cepat & personal</p></div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 sm:px-6 pt-12 pb-16">
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
