<x-app-layout :title="'Lupa Sandi'">
    <section class="mx-auto max-w-md px-4 sm:px-6 py-16">
        <div class="rounded-2xl border border-olive-100 bg-white p-8">
            <h1 class="text-2xl font-semibold text-olive-900">Lupa Kata Sandi</h1>
            <p class="mt-1 text-sm text-olive-600">
                Masukkan email Anda, kami akan mengirimkan tautan untuk membuat kata sandi baru.
            </p>

            @if (session('status'))
                <div class="mt-4 rounded-lg bg-olive-100 px-3 py-2 text-sm text-olive-800">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
                @csrf

                <label class="block text-sm text-olive-700">
                    Email
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="mt-1 w-full rounded-lg border-olive-200 focus:border-olive-500 focus:ring-olive-500">
                </label>

                <button type="submit"
                        class="w-full rounded-full bg-olive-700 px-5 py-3 text-sm font-medium text-white hover:bg-olive-800">
                    Kirim Tautan Reset Sandi
                </button>
            </form>

            <p class="mt-6 text-sm text-olive-600 text-center">
                <a href="{{ route('login') }}" class="font-medium text-olive-800 hover:underline">Kembali ke halaman masuk</a>
            </p>
        </div>
    </section>
</x-app-layout>
