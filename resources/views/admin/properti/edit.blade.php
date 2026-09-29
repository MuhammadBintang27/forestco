@php $remainingPhotoSlots = 3 - $property->photos->count(); @endphp
<x-dashboard-layout :title="'Edit Properti'">
    <form id="properti-form" method="POST" action="{{ route('admin.properti.update', $property) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
    </form>

    {{-- Kamar baru (tipe Kos) dikirim lewat request terpisah dari update
         properti, jadi punya form sendiri di sini. --}}
    <form id="kamar-form" method="POST" action="{{ route('admin.properti.kamar.store', $property) }}">
        @csrf
    </form>

    <div class="max-w-6xl" x-data="{ tipe: '{{ old('tipe', $property->tipe) }}' }">
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_420px] gap-6 items-start">
            <div class="space-y-6">
                <div class="rounded-2xl border border-olive-100 bg-white p-6">
                    @include('admin.properti._fields', ['property' => $property, 'formId' => 'properti-form'])
                </div>

                <div class="rounded-2xl border border-olive-100 bg-white p-6">
                    <p class="block text-sm text-olive-700 mb-2">Foto Properti ({{ $property->photos->count() }}/3)</p>
                    <div class="grid grid-cols-3 gap-3 mb-3">
                        @foreach ($property->photos as $photo)
                            <div class="relative group">
                                <img src="{{ $photo->url() }}" class="aspect-square w-full rounded-lg object-cover">
                                <button type="submit" form="delete-photo-{{ $photo->id }}"
                                        class="absolute top-1 right-1 rounded-full bg-white/90 px-2 py-0.5 text-xs text-red-600 shadow">
                                    Hapus
                                </button>
                            </div>
                        @endforeach
                    </div>

                    @if ($remainingPhotoSlots > 0)
                        <label class="block text-sm text-olive-700">
                            Tambah Foto (sisa {{ $remainingPhotoSlots }} slot)
                            <input type="file" name="photos[]" form="properti-form" multiple accept=".jpg,.jpeg,.png"
                                   class="mt-1 w-full text-sm text-olive-700 file:mr-3 file:rounded-full file:border-0 file:bg-olive-100 file:px-3 file:py-1.5 file:text-olive-800">
                        </label>
                    @else
                        <p class="text-xs text-olive-500">Foto sudah mencapai batas maksimal (3). Hapus salah satu untuk menambah foto baru.</p>
                    @endif

                    @foreach ($property->photos as $photo)
                        <form id="delete-photo-{{ $photo->id }}" method="POST" action="{{ route('admin.properti.foto.destroy', $photo) }}" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endforeach
                </div>
            </div>

            <div class="space-y-6 lg:sticky lg:top-6">
                @include('admin.properti._harga_unit', ['property' => $property, 'formId' => 'properti-form'])
                @include('admin.properti._kamar_manager', ['property' => $property, 'formId' => 'kamar-form'])
            </div>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" form="properti-form" class="rounded-full bg-olive-700 px-6 py-2.5 text-sm font-medium text-white hover:bg-olive-800">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.properti.index') }}" class="rounded-full border border-olive-300 px-6 py-2.5 text-sm font-medium text-olive-800 hover:bg-cream-100">
                Kembali
            </a>
        </div>
    </div>
</x-dashboard-layout>
