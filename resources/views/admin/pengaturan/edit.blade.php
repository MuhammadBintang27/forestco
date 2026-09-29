<x-dashboard-layout :title="'Pengaturan'">
    <div class="max-w-lg rounded-2xl border border-olive-100 bg-white p-6">
        <form method="POST" action="{{ route('admin.pengaturan.update') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <label class="block text-sm text-olive-700">
                Nama Bank
                <input type="text" name="nama_bank" value="{{ old('nama_bank', $setting->nama_bank) }}" required
                       class="mt-1 w-full rounded-lg border-olive-200 focus:border-olive-500 focus:ring-olive-500">
            </label>

            <label class="block text-sm text-olive-700">
                Nomor Rekening
                <input type="text" name="nomor_rekening" value="{{ old('nomor_rekening', $setting->nomor_rekening) }}" required
                       class="mt-1 w-full rounded-lg border-olive-200 focus:border-olive-500 focus:ring-olive-500">
            </label>

            <label class="block text-sm text-olive-700">
                Atas Nama
                <input type="text" name="nama_pemilik_rekening" value="{{ old('nama_pemilik_rekening', $setting->nama_pemilik_rekening) }}" required
                       class="mt-1 w-full rounded-lg border-olive-200 focus:border-olive-500 focus:ring-olive-500">
            </label>

            <label class="block text-sm text-olive-700">
                No. WhatsApp Admin
                <input type="text" name="no_wa_admin" value="{{ old('no_wa_admin', $setting->no_wa_admin) }}" required placeholder="08xxxxxxxxxx"
                       class="mt-1 w-full rounded-lg border-olive-200 focus:border-olive-500 focus:ring-olive-500">
            </label>

            <button type="submit" class="rounded-full bg-olive-700 px-6 py-2.5 text-sm font-medium text-white hover:bg-olive-800">
                Simpan Pengaturan
            </button>
        </form>
    </div>
</x-dashboard-layout>
