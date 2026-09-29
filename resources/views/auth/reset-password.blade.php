<x-app-layout :title="'Atur Ulang Sandi'">
    <section class="mx-auto max-w-md px-4 sm:px-6 py-16">
        <div class="rounded-2xl border border-olive-100 bg-white p-8">
            <h1 class="text-2xl font-semibold text-olive-900">Atur Ulang Kata Sandi</h1>

            <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-4">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <label class="block text-sm text-olive-700">
                    Email
                    <input type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus
                           class="mt-1 w-full rounded-lg border-olive-200 focus:border-olive-500 focus:ring-olive-500">
                </label>

                <label class="block text-sm text-olive-700">
                    Kata Sandi Baru
                    <input type="password" name="password" required
                           class="mt-1 w-full rounded-lg border-olive-200 focus:border-olive-500 focus:ring-olive-500">
                </label>

                <label class="block text-sm text-olive-700">
                    Konfirmasi Kata Sandi Baru
                    <input type="password" name="password_confirmation" required
                           class="mt-1 w-full rounded-lg border-olive-200 focus:border-olive-500 focus:ring-olive-500">
                </label>

                <button type="submit"
                        class="w-full rounded-full bg-olive-700 px-5 py-3 text-sm font-medium text-white hover:bg-olive-800">
                    Atur Ulang Kata Sandi
                </button>
            </form>
        </div>
    </section>
</x-app-layout>
