<!doctype html>
<html lang="id" x-data>
<head>
    @include('partials.head', ['title' => 'Daftar'])
</head>
<body class="min-h-screen bg-cream-50 font-sans text-olive-900 antialiased">
    <div class="grid min-h-screen lg:grid-cols-2">
        {{-- Left: brand panel --}}
        <div class="hidden lg:flex flex-col justify-between bg-olive-800 text-white p-10 xl:p-14">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-semibold text-lg">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 11l9-7 9 7v9a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9z" />
                    </svg>
                </span>
                ForestCo
            </a>

            <div class="max-w-md">
                <h1 class="text-4xl font-semibold leading-tight">Mulai cari kos, rumah, atau ruko hari ini.</h1>
                <p class="mt-4 text-white/70">
                    Daftar sebagai penyewa untuk menyimpan properti favorit, mengajukan reservasi, dan
                    memantau status pembayaran langsung dari akun Anda.
                </p>

                <div class="mt-8 rounded-2xl bg-white/10 border border-white/10 p-5">
                    <div class="flex items-center gap-2 font-semibold">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-white text-olive-800">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="h-3 w-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                        Gratis, tanpa biaya pendaftaran
                    </div>
                    <p class="mt-1 text-sm text-white/70">
                        Cukup satu akun untuk semua jenis properti ForestCo di Banda Aceh & Aceh Besar.
                    </p>
                </div>
            </div>

            <p class="text-sm text-white/60">&copy; {{ now()->year }} ForestCo. Seluruh hak cipta dilindungi.</p>
        </div>

        {{-- Right: form --}}
        <div class="flex items-center justify-center px-6 py-12">
            <div class="w-full max-w-sm">
                <a href="{{ route('home') }}" class="lg:hidden mb-8 flex items-center gap-2 font-semibold text-olive-900">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-olive-700 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 11l9-7 9 7v9a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9z" />
                        </svg>
                    </span>
                    ForestCo
                </a>

                <h2 class="text-2xl font-semibold text-olive-900">Buat akun penyewa</h2>
                <p class="mt-1 text-sm text-olive-600">Isi data diri untuk mulai mencari properti</p>

                @if ($errors->any())
                    <div class="mt-4 rounded-lg bg-red-50 border border-red-200 px-3 py-2 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
                    @csrf

                    <label class="block text-sm font-medium text-olive-800">
                        Nama Lengkap
                        <input type="text" name="nama" value="{{ old('nama') }}" required autofocus
                               placeholder="Nama sesuai identitas"
                               class="mt-1 w-full rounded-lg border-olive-200 text-sm focus:border-olive-500 focus:ring-olive-500">
                    </label>

                    <label class="block text-sm font-medium text-olive-800">
                        Email
                        <input type="email" name="email" value="{{ old('email') }}" required
                               placeholder="nama@email.com"
                               class="mt-1 w-full rounded-lg border-olive-200 text-sm focus:border-olive-500 focus:ring-olive-500">
                    </label>

                    <label class="block text-sm font-medium text-olive-800">
                        No. Telepon
                        <input type="text" name="no_telepon" value="{{ old('no_telepon') }}" required
                               placeholder="08xx-xxxx-xxxx"
                               class="mt-1 w-full rounded-lg border-olive-200 text-sm focus:border-olive-500 focus:ring-olive-500">
                    </label>

                    <div class="grid grid-cols-2 gap-3">
                        <label class="block text-sm font-medium text-olive-800">
                            Kata Sandi
                            <input type="password" name="password" required
                                   placeholder="Min. 8 karakter"
                                   class="mt-1 w-full rounded-lg border-olive-200 text-sm focus:border-olive-500 focus:ring-olive-500">
                        </label>
                        <label class="block text-sm font-medium text-olive-800">
                            Konfirmasi Sandi
                            <input type="password" name="password_confirmation" required
                                   placeholder="Ulangi sandi"
                                   class="mt-1 w-full rounded-lg border-olive-200 text-sm focus:border-olive-500 focus:ring-olive-500">
                        </label>
                    </div>

                    <label class="flex items-start gap-2 text-sm text-olive-700">
                        <input type="checkbox" name="terms" required
                               class="mt-0.5 rounded border-olive-300 text-olive-700 focus:ring-olive-500">
                        Saya menyetujui Syarat & Ketentuan ForestCo
                    </label>

                    <button type="submit"
                            class="w-full rounded-full bg-olive-700 px-5 py-3 text-sm font-medium text-white hover:bg-olive-800">
                        Daftar Sekarang →
                    </button>
                </form>

                <p class="mt-6 text-sm text-olive-600 text-center">
                    Sudah punya akun? <a href="{{ route('login') }}" class="font-medium text-olive-800 hover:underline">Masuk di sini</a>
                </p>

                <p class="lg:hidden mt-10 text-xs text-olive-500 text-center">
                    &copy; {{ now()->year }} ForestCo. Seluruh hak cipta dilindungi.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
