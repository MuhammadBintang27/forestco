<header class="border-b border-olive-100 bg-cream-50">
    <nav class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 sm:px-6 py-4">
        <a href="{{ route('home') }}" class="flex items-center gap-2 font-semibold text-olive-900">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-olive-700 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 11l9-7 9 7v9a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9z" />
                </svg>
            </span>
            ForestCo
        </a>

        <div class="hidden md:flex items-center gap-6 text-sm text-olive-800">
            <a href="{{ route('katalog.index') }}" class="hover:text-olive-900 {{ request()->routeIs('katalog.*') ? 'font-semibold text-olive-900' : '' }}">Katalog</a>
            <a href="{{ route('cara-kerja') }}" class="hover:text-olive-900 {{ request()->routeIs('cara-kerja') ? 'font-semibold text-olive-900' : '' }}">Cara Kerja</a>
            <a href="{{ route('tentang') }}" class="hover:text-olive-900 {{ request()->routeIs('tentang') ? 'font-semibold text-olive-900' : '' }}">Tentang</a>
        </div>

        @auth
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.outside="open = false"
                        class="rounded-full bg-olive-700 px-4 py-2 text-sm font-medium text-white hover:bg-olive-800">
                    {{ Auth::user()->nama }}
                </button>
                <div x-show="open" x-cloak x-transition
                     class="absolute right-0 mt-2 w-48 rounded-xl border border-olive-100 bg-white py-1 shadow-lg z-20">
                    @if (Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-olive-800 hover:bg-cream-100">Dashboard Admin</a>
                    @else
                        <a href="{{ route('reservasi.index') }}" class="block px-4 py-2 text-sm text-olive-800 hover:bg-cream-100">Akun Saya</a>
                        <a href="{{ route('sewa.index') }}" class="block px-4 py-2 text-sm text-olive-800 hover:bg-cream-100">Sewa Aktif</a>
                        <a href="{{ route('favorit.index') }}" class="block px-4 py-2 text-sm text-olive-800 hover:bg-cream-100">Favorit Saya</a>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-olive-800 hover:bg-cream-100">Profil</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-cream-100">Keluar</button>
                    </form>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}"
               class="rounded-full bg-olive-700 px-5 py-2 text-sm font-medium text-white hover:bg-olive-800">
                Login
            </a>
        @endauth
    </nav>
</header>
