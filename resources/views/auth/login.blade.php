<x-app-layout :title="'Masuk'">
    <section class="mx-auto max-w-md px-4 sm:px-6 py-16">
        <div class="rounded-2xl border border-olive-100 bg-white p-8">
            <h1 class="text-2xl font-semibold text-olive-900">Masuk ke Akun Anda</h1>
            <p class="mt-1 text-sm text-olive-600">Gunakan email dan kata sandi Anda.</p>

            @if (session('status'))
                <div class="mt-4 rounded-lg bg-olive-100 px-3 py-2 text-sm text-olive-800">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                @csrf

                <label class="block text-sm text-olive-700">
                    Email
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="mt-1 w-full rounded-lg border-olive-200 focus:border-olive-500 focus:ring-olive-500">
                </label>

                <label class="block text-sm text-olive-700">
                    Kata Sandi
                    <input type="password" name="password" required
                           class="mt-1 w-full rounded-lg border-olive-200 focus:border-olive-500 focus:ring-olive-500">
                </label>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 text-olive-700">
                        <input type="checkbox" name="remember" class="rounded border-olive-300 text-olive-700 focus:ring-olive-500">
                        Ingat saya
                    </label>
                    <a href="{{ route('password.request') }}" class="text-olive-700 hover:underline">Lupa sandi?</a>
                </div>

                <button type="submit"
                        class="w-full rounded-full bg-olive-700 px-5 py-3 text-sm font-medium text-white hover:bg-olive-800">
                    Masuk
                </button>
            </form>

            <p class="mt-6 text-sm text-olive-600 text-center">
                Belum punya akun? <a href="{{ route('register') }}" class="font-medium text-olive-800 hover:underline">Daftar</a>
            </p>
        </div>
    </section>
</x-app-layout>
