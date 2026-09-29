@props(['title' => null, 'subtitle' => null])
<!doctype html>
<html lang="id" x-data>
<head>
    @include('partials.head', ['title' => $title])
</head>
<body class="min-h-screen bg-cream-50 font-sans text-olive-900 antialiased">
    @include('partials.nav')

    @include('partials.flash')

    <main class="mx-auto max-w-6xl px-4 sm:px-6 py-8">
        @if ($title)
            <h1 class="text-2xl font-semibold text-olive-900">{{ $title }}</h1>
            @if ($subtitle)
                <p class="mt-1 text-olive-600">{{ $subtitle }}</p>
            @endif
        @endif

        <div class="{{ $title ? 'mt-6' : '' }}">
            {{ $slot }}
        </div>
    </main>
</body>
</html>
