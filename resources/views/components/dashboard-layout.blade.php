@props(['title' => null])
<!doctype html>
<html lang="id" x-data>
<head>
    @include('partials.head', ['title' => $title])
</head>
<body class="min-h-screen bg-cream-50 font-sans text-olive-900 antialiased">
    <div x-data="{ sidebar: false }" class="flex min-h-screen">
        <div x-show="sidebar" x-cloak @click="sidebar = false" class="fixed inset-0 z-30 bg-black/40 md:hidden"></div>
        <aside :class="sidebar ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-olive-800 text-white transition-transform md:static md:z-auto md:translate-x-0 md:shrink-0">
            <a href="{{ route('home') }}" class="flex items-center gap-2 px-6 py-5 font-semibold">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 11l9-7 9 7v9a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9z" />
                    </svg>
                </span>
                ForestCo
            </a>

            <nav class="flex-1 space-y-1 px-3 py-2 text-sm overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-2.5 rounded-lg px-3 py-2 {{ request()->routeIs('admin.dashboard') ? 'bg-white text-olive-900 font-semibold' : 'text-white/80 hover:bg-white/10' }}">
                    @include('partials.nav-icon', ['name' => 'dashboard'])
                    Dashboard
                </a>

                <p class="px-3 pt-4 pb-1 text-[11px] font-semibold uppercase tracking-wider text-white/40">Manajemen</p>
                @php
                    $manajemen = [
                        ['route' => 'admin.properti.index', 'pattern' => 'admin.properti.*', 'label' => 'Kelola Properti', 'icon' => 'properti'],
                        ['route' => 'admin.penyewa.index', 'pattern' => 'admin.penyewa.*', 'label' => 'Kelola Penyewa', 'icon' => 'penyewa'],
                        ['route' => 'admin.reservasi.index', 'pattern' => 'admin.reservasi.*', 'label' => 'Verifikasi Reservasi', 'icon' => 'reservasi'],
                        ['route' => 'admin.inspeksi.index', 'pattern' => 'admin.inspeksi.*', 'label' => 'Jadwal Kunjungan', 'icon' => 'inspeksi'],
                        ['route' => 'admin.pembayaran.index', 'pattern' => 'admin.pembayaran.*', 'label' => 'Kelola Pembayaran', 'icon' => 'pembayaran'],
                        ['route' => 'admin.perpanjangan.index', 'pattern' => 'admin.perpanjangan.*', 'label' => 'Perpanjangan Sewa', 'icon' => 'perpanjangan'],
                        ['route' => 'admin.kerusakan.index', 'pattern' => 'admin.kerusakan.*', 'label' => 'Laporan Kerusakan', 'icon' => 'kerusakan'],
                    ];
                @endphp
                @foreach ($manajemen as $item)
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-2.5 rounded-lg px-3 py-2 {{ request()->routeIs($item['pattern']) ? 'bg-white text-olive-900 font-semibold' : 'text-white/80 hover:bg-white/10' }}">
                        @include('partials.nav-icon', ['name' => $item['icon']])
                        {{ $item['label'] }}
                    </a>
                @endforeach

                <p class="px-3 pt-4 pb-1 text-[11px] font-semibold uppercase tracking-wider text-white/40">Lainnya</p>
                @php
                    $lainnya = [
                        ['route' => 'admin.laporan.index', 'pattern' => 'admin.laporan.*', 'label' => 'Cetak Laporan', 'icon' => 'laporan'],
                        ['route' => 'admin.staf.index', 'pattern' => 'admin.staf.*', 'label' => 'Kelola Akun Admin', 'icon' => 'staf'],
                        ['route' => 'admin.pengaturan.edit', 'pattern' => 'admin.pengaturan.*', 'label' => 'Pengaturan', 'icon' => 'pengaturan'],
                    ];
                @endphp
                @foreach ($lainnya as $item)
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-2.5 rounded-lg px-3 py-2 {{ request()->routeIs($item['pattern']) ? 'bg-white text-olive-900 font-semibold' : 'text-white/80 hover:bg-white/10' }}">
                        @include('partials.nav-icon', ['name' => $item['icon']])
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-white/10 px-3 py-4">
                <div class="flex items-center gap-2.5 px-2">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/15 text-xs font-semibold">
                        {{ auth()->user()->initials() }}
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-medium truncate">{{ auth()->user()->nama }}</p>
                        <p class="text-xs text-white/60">Operator</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="px-2 pt-2">
                    @csrf
                    <button type="submit" class="text-sm text-white/70 hover:text-white hover:underline">Keluar</button>
                </form>
            </div>
        </aside>

        <div class="flex-1 min-w-0">
            <header class="flex items-center justify-between border-b border-olive-100 bg-white px-4 sm:px-6 py-4 md:hidden">
                <button type="button" @click="sidebar = true" class="flex items-center gap-2 font-semibold text-olive-900" aria-label="Buka menu">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-6 w-6"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" /></svg>
                    ForestCo
                </button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-red-600">Keluar</button>
                </form>
            </header>

            @include('partials.flash')

            <main class="px-4 sm:px-6 py-6">
                @if ($title)
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-6">
                        <h1 class="text-xl font-semibold text-olive-900">{{ $title }}</h1>
                        <p class="text-sm text-olive-500">{{ now()->translatedFormat('l, j F Y') }}</p>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
