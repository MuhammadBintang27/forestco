@props(['title' => null])
<!doctype html>
<html lang="id" x-data>
<head>
    @include('partials.head', ['title' => $title])
</head>
<body class="min-h-screen flex flex-col bg-cream-50 font-sans text-olive-900 antialiased">
    @include('partials.nav')

    @include('partials.flash')

    <main class="flex-1">
        {{ $slot }}
    </main>

    @php($waLink = \App\Support\WhatsApp::link(\App\Models\Pengaturan::current()->no_wa_admin, 'Halo ForestCo, saya ingin bertanya tentang sewa properti.'))
    <footer class="mt-16 border-t border-olive-100 bg-cream-100 text-sm text-olive-700">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 py-10 grid gap-8 sm:grid-cols-3">
            <div>
                <p class="font-semibold text-olive-900">ForestCo</p>
                <p class="mt-2 leading-relaxed">Sewa kos, rumah, dan ruko dengan mudah, jelas, dan transparan.</p>
            </div>
            <div>
                <p class="font-semibold text-olive-900">Jelajahi</p>
                <ul class="mt-2 space-y-1.5">
                    <li><a href="{{ route('katalog.index') }}" class="hover:text-olive-900 hover:underline">Katalog</a></li>
                    <li><a href="{{ route('cara-kerja') }}" class="hover:text-olive-900 hover:underline">Cara Kerja</a></li>
                    <li><a href="{{ route('tentang') }}" class="hover:text-olive-900 hover:underline">Tentang</a></li>
                </ul>
            </div>
            <div>
                <p class="font-semibold text-olive-900">Butuh bantuan?</p>
                @if ($waLink)
                    <a href="{{ $waLink }}" target="_blank" rel="noopener"
                       class="mt-2 inline-block rounded-full bg-olive-700 px-4 py-2 font-medium text-white hover:bg-olive-800">Hubungi via WhatsApp</a>
                @else
                    <p class="mt-2">Hubungi kami melalui proses reservasi.</p>
                @endif
            </div>
        </div>
        <div class="border-t border-olive-100 py-4 text-center text-xs">&copy; {{ now()->year }} ForestCo. Semua hak dilindungi.</div>
    </footer>
</body>
</html>
