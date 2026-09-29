<x-dashboard-layout :title="'Tambah Properti'">
    {{-- Form kosong ini cuma pembawa CSRF token & action; field aslinya
         tersebar di kolom kiri & kanan, dihubungkan lewat atribut
         form="properti-form" supaya tetap kekirim jadi satu request. --}}
    <form id="properti-form" method="POST" action="{{ route('admin.properti.store') }}" enctype="multipart/form-data">
        @csrf
    </form>

    <div class="max-w-6xl" x-data="{ tipe: '{{ old('tipe', 'kos') }}' }">
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_420px] gap-6 items-start">
            <div class="space-y-6">
                <div class="rounded-2xl border border-olive-100 bg-white p-6">
                    @include('admin.properti._fields', ['formId' => 'properti-form'])
                </div>

                <div class="rounded-2xl border border-olive-100 bg-white p-6">
                    <label class="block text-sm text-olive-700">
                        Foto Properti (maksimal 3)
                        <input type="file" name="photos[]" form="properti-form" multiple accept=".jpg,.jpeg,.png" max="3"
                               class="mt-1 w-full text-sm text-olive-700 file:mr-3 file:rounded-full file:border-0 file:bg-olive-100 file:px-3 file:py-1.5 file:text-olive-800">
                    </label>
                </div>
            </div>

            <div class="space-y-6 lg:sticky lg:top-6">
                @include('admin.properti._harga_unit', ['property' => null, 'formId' => 'properti-form'])
                @include('admin.properti._kamar_manager', ['property' => null, 'formId' => 'properti-form'])
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" form="properti-form" class="rounded-full bg-olive-700 px-6 py-2.5 text-sm font-medium text-white hover:bg-olive-800">
                Simpan Properti
            </button>
            <a href="{{ route('admin.properti.index') }}" class="rounded-full border border-olive-300 px-6 py-2.5 text-sm font-medium text-olive-800 hover:bg-cream-100">
                Batal
            </a>
        </div>
    </div>
</x-dashboard-layout>
