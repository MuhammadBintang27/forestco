@props(['title' => null])
<!doctype html>
<html lang="id" x-data>
<head>
    @include('partials.head', ['title' => $title])
</head>
<body class="min-h-screen bg-cream-50 font-sans text-olive-900 antialiased">
    @include('partials.nav')

    @include('partials.flash')

    <main>
        {{ $slot }}
    </main>

    <footer class="mt-16 border-t border-olive-100 bg-cream-100">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 py-8 text-sm text-olive-700 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; {{ now()->year }} ForestCo. Semua hak dilindungi.</p>
            <p>Sewa kos, rumah, dan ruko dengan mudah.</p>
        </div>
    </footer>
</body>
</html>
