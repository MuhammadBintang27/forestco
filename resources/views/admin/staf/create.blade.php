<x-dashboard-layout :title="'Tambah Akun Admin'">
    <div class="max-w-lg rounded-2xl border border-olive-100 bg-white p-6">
        <form method="POST" action="{{ route('admin.staf.store') }}" class="space-y-4">
            @csrf

            <label class="block text-sm text-olive-700">
                Nama Lengkap
                <input type="text" name="nama" value="{{ old('nama') }}" required autofocus
                       class="mt-1 w-full rounded-lg border-olive-200 focus:border-olive-500 focus:ring-olive-500">
            </label>

            <label class="block text-sm text-olive-700">
                Email
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="mt-1 w-full rounded-lg border-olive-200 focus:border-olive-500 focus:ring-olive-500">
            </label>

            <label class="block text-sm text-olive-700">
                No. WhatsApp
                <input type="text" name="no_telepon" value="{{ old('no_telepon') }}" required
                       class="mt-1 w-full rounded-lg border-olive-200 focus:border-olive-500 focus:ring-olive-500">
            </label>

            <label class="block text-sm text-olive-700">
                Kata Sandi
                <input type="password" name="password" required
                       class="mt-1 w-full rounded-lg border-olive-200 focus:border-olive-500 focus:ring-olive-500">
            </label>

            <label class="block text-sm text-olive-700">
                Konfirmasi Kata Sandi
                <input type="password" name="password_confirmation" required
                       class="mt-1 w-full rounded-lg border-olive-200 focus:border-olive-500 focus:ring-olive-500">
            </label>

            <div class="flex gap-3">
                <button type="submit" class="rounded-full bg-olive-700 px-6 py-2.5 text-sm font-medium text-white hover:bg-olive-800">
                    Simpan
                </button>
                <a href="{{ route('admin.staf.index') }}" class="rounded-full border border-olive-300 px-6 py-2.5 text-sm font-medium text-olive-800 hover:bg-cream-100">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-dashboard-layout>
