@php $layout = auth()->user()->isAdmin() ? 'dashboard-layout' : 'account-layout'; @endphp
<x-dynamic-component :component="$layout" :title="'Profil'">
    <div class="grid grid-cols-1 {{ auth()->user()->isAdmin() ? '' : 'lg:grid-cols-[260px_1fr]' }} gap-6 items-start">
        @unless (auth()->user()->isAdmin())
            @include('partials.account-menu', ['active' => 'profil'])
        @endunless

        <div class="max-w-lg rounded-2xl border border-olive-100 bg-white p-6">
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <label class="block text-sm text-olive-700">
                    Nama Lengkap
                    <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required>
                </label>

                <label class="block text-sm text-olive-700">
                    Email
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                </label>

                <label class="block text-sm text-olive-700">
                    No. WhatsApp
                    <input type="text" name="no_telepon" value="{{ old('no_telepon', $user->no_telepon) }}" required>
                </label>

                <div class="border-t border-olive-100 pt-4">
                    <p class="text-sm text-olive-600 mb-3">Kosongkan jika tidak ingin mengubah kata sandi.</p>
                    <label class="block text-sm text-olive-700">
                        Kata Sandi Baru
                        <input type="password" name="password">
                    </label>
                    <label class="block text-sm text-olive-700 mt-3">
                        Konfirmasi Kata Sandi Baru
                        <input type="password" name="password_confirmation">
                    </label>
                </div>

                <button type="submit" class="rounded-full bg-olive-700 px-6 py-2.5 text-sm font-medium text-white hover:bg-olive-800">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</x-dynamic-component>
