@php
    $property = $property ?? null;
    $formId = $formId ?? null;
@endphp

{{-- Semua field di sini pakai atribut form="{{ $formId }}" (bukan nested di
     dalam tag <form>) supaya partial ini bisa dipakai persis sama di halaman
     create maupun edit, apa pun posisinya di grid 2 kolom. --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <label class="block text-sm text-olive-700">
        Tipe Properti
        <select name="tipe" form="{{ $formId }}" x-model="tipe" class="mt-1 w-full rounded-lg border-olive-200 text-sm focus:border-olive-500 focus:ring-olive-500">
            @foreach (['kos' => 'Kos', 'rumah' => 'Rumah', 'ruko' => 'Ruko'] as $value => $label)
                <option value="{{ $value }}" @selected(old('tipe', $property?->tipe) === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </label>

    <label class="block text-sm text-olive-700">
        Status
        <select name="status" form="{{ $formId }}" class="mt-1 w-full rounded-lg border-olive-200 text-sm focus:border-olive-500 focus:ring-olive-500">
            <option value="draft" @selected(old('status', $property?->status ?? 'draft') === 'draft')>Draft</option>
            <option value="published" @selected(old('status', $property?->status) === 'published')>Published</option>
        </select>
    </label>
</div>

<label class="block text-sm text-olive-700 mt-4">
    Nama Properti
    <input type="text" name="nama" form="{{ $formId }}" value="{{ old('nama', $property?->nama) }}" required
           class="mt-1 w-full rounded-lg border-olive-200 text-sm focus:border-olive-500 focus:ring-olive-500">
</label>

<label class="block text-sm text-olive-700 mt-4">
    Alamat
    <input type="text" name="alamat" form="{{ $formId }}" value="{{ old('alamat', $property?->alamat) }}" required
           class="mt-1 w-full rounded-lg border-olive-200 text-sm focus:border-olive-500 focus:ring-olive-500">
</label>

<label class="block text-sm text-olive-700 mt-4">
    Deskripsi
    <textarea name="deskripsi" form="{{ $formId }}" rows="4"
              class="mt-1 w-full rounded-lg border-olive-200 text-sm focus:border-olive-500 focus:ring-olive-500">{{ old('deskripsi', $property?->deskripsi) }}</textarea>
</label>

<div class="mt-4" x-data="{ facilities: {{ \Illuminate\Support\Js::from(old('facilities', $property?->facilities->pluck('nama')->all() ?: [''])) }} }">
    <p class="block text-sm text-olive-700 mb-2">Fasilitas</p>
    <template x-for="(facility, index) in facilities" :key="index">
        <div class="flex gap-2 mb-2">
            <input type="text" form="{{ $formId }}" :name="`facilities[${index}]`" x-model="facilities[index]" placeholder="Contoh: WiFi"
                   class="flex-1 rounded-lg border-olive-200 text-sm focus:border-olive-500 focus:ring-olive-500">
            <button type="button" @click="facilities.splice(index, 1)" class="text-red-500 text-sm px-2">Hapus</button>
        </div>
    </template>
    <button type="button" @click="facilities.push('')" class="text-sm font-medium text-olive-700 hover:underline">
        + Tambah Fasilitas
    </button>
</div>
