@php $existingRooms = ($property ?? null)?->rooms ?? collect(); @endphp

<div x-show="tipe === 'kos'" x-cloak
     x-data="{
        selectedExisting: null,
        kamarDefault: { harga: null, harga_tahunan: null, ukuran_kamar: '', tipe_kamar_mandi: '' },
        kamarList: [],
        genPrefix: 'A',
        genJumlah: 4,
        addKamar() { this.kamarList.push({ kode: '', ...this.kamarDefault }); },
        removeKamar(i) { this.kamarList.splice(i, 1); },
        applyDefaultToAll() { this.kamarList.forEach(k => Object.assign(k, this.kamarDefault)); },
        generateKamar() {
            for (let i = 1; i <= this.genJumlah; i++) {
                this.kamarList.push({ kode: this.genPrefix + i, ...this.kamarDefault });
            }
        },
     }"
     class="space-y-6">

    @if ($existingRooms->isNotEmpty())
        <div class="rounded-2xl border border-olive-100 bg-white p-5">
            <h2 class="font-semibold text-olive-900 mb-3">Kamar Tersimpan ({{ $existingRooms->count() }})</h2>
            <p class="text-xs text-olive-500 mb-3">Klik kode kamar untuk lihat/edit detailnya.</p>

            <div class="flex gap-3">
                {{-- Kiri: daftar kode kamar saja --}}
                <div class="grid grid-cols-3 gap-1.5 w-28 shrink-0 max-h-96 overflow-y-auto content-start">
                    @foreach ($existingRooms as $room)
                        <button type="button" @click="selectedExisting = (selectedExisting === {{ $room->id }} ? null : {{ $room->id }})"
                                :class="selectedExisting === {{ $room->id }} ? 'bg-olive-700 text-white border-olive-700' : '{{ $room->status === 'tersedia' ? 'border-olive-300 text-olive-800 hover:border-olive-700' : 'border-olive-100 bg-olive-50 text-olive-400' }}'"
                                class="rounded-lg border px-2 py-2 text-xs font-medium text-center truncate">
                            {{ $room->kode }}
                        </button>
                    @endforeach
                </div>

                {{-- Kanan: detail kamar yang dipilih --}}
                <div class="flex-1 min-w-0">
                    <p x-show="selectedExisting === null" x-cloak class="text-xs text-olive-500 py-4 text-center">
                        ← Pilih kamar di kiri
                    </p>

                    @foreach ($existingRooms as $room)
                        <div x-show="selectedExisting === {{ $room->id }}" x-cloak class="rounded-lg border border-olive-100 p-2.5">
                            <form method="POST" action="{{ route('admin.kamar.update', $room) }}" class="space-y-1.5">
                                @csrf
                                @method('PATCH')
                                <div class="flex gap-1.5">
                                    <input type="text" name="kode" value="{{ $room->kode }}" required
                                           class="w-16 rounded-lg border-olive-200 text-xs focus:border-olive-500 focus:ring-olive-500">
                                    <select name="status" class="flex-1 rounded-lg border-olive-200 text-xs focus:border-olive-500 focus:ring-olive-500">
                                        <option value="tersedia" @selected($room->status === 'tersedia')>Tersedia</option>
                                        <option value="terisi" @selected($room->status === 'terisi')>Terisi</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-1.5">
                                    <input type="text" inputmode="numeric" name="harga" value="{{ \App\Support\Money::digits($room->harga) }}" placeholder="Harga/bln" required
                                           class="money-input rounded-lg border-olive-200 text-xs focus:border-olive-500 focus:ring-olive-500">
                                    <input type="text" inputmode="numeric" name="harga_tahunan" value="{{ \App\Support\Money::digits($room->harga_tahunan) }}" placeholder="Harga/thn"
                                           class="money-input rounded-lg border-olive-200 text-xs focus:border-olive-500 focus:ring-olive-500">
                                </div>
                                <div class="grid grid-cols-2 gap-1.5">
                                    <input type="text" name="ukuran_kamar" value="{{ $room->ukuran_kamar }}" placeholder="Ukuran"
                                           class="rounded-lg border-olive-200 text-xs focus:border-olive-500 focus:ring-olive-500">
                                    <input type="text" name="tipe_kamar_mandi" value="{{ $room->tipe_kamar_mandi }}" placeholder="KM"
                                           class="rounded-lg border-olive-200 text-xs focus:border-olive-500 focus:ring-olive-500">
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit" class="rounded-full bg-olive-100 px-2.5 py-1 text-xs font-medium text-olive-800 hover:bg-olive-200">
                                        Simpan
                                    </button>
                                </div>
                            </form>
                            <form method="POST" action="{{ route('admin.kamar.destroy', $room) }}" class="mt-1 text-right">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-red-600 hover:underline">Hapus kamar ini</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="rounded-2xl border border-olive-100 bg-white p-5">
        <h2 class="font-semibold text-olive-900 mb-1">{{ $property ? 'Tambah Kamar Baru' : 'Kamar' }}</h2>
        <p class="text-xs text-olive-500 mb-4">
            Kalau harganya sama semua, tidak perlu isi satu-satu: isi "Harga Default" sekali, lalu generate beberapa kamar sekaligus.
            Durasi sewa (6 bulan/1 tahun/2 tahun) otomatis mengikuti tipe Kos, tidak perlu diatur per kamar.
        </p>

        <div class="rounded-xl bg-cream-100 p-3 space-y-2 mb-4">
            <p class="text-xs font-medium text-olive-700">Harga Default (buat isi cepat)</p>
            <div class="grid grid-cols-2 gap-1.5">
                <div>
                    <input type="number" x-model.number="kamarDefault.harga" placeholder="Harga/bulan"
                           class="w-full rounded-lg border-olive-200 text-xs focus:border-olive-500 focus:ring-olive-500">
                    <p class="mt-0.5 text-[11px] text-olive-400" x-show="kamarDefault.harga" x-cloak x-text="'Rp ' + Number(kamarDefault.harga).toLocaleString('id-ID')"></p>
                </div>
                <div>
                    <input type="number" x-model.number="kamarDefault.harga_tahunan" placeholder="Harga/tahun"
                           class="w-full rounded-lg border-olive-200 text-xs focus:border-olive-500 focus:ring-olive-500">
                    <p class="mt-0.5 text-[11px] text-olive-400" x-show="kamarDefault.harga_tahunan" x-cloak x-text="'Rp ' + Number(kamarDefault.harga_tahunan).toLocaleString('id-ID')"></p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-1.5">
                <input type="text" x-model="kamarDefault.ukuran_kamar" placeholder="Ukuran"
                       class="rounded-lg border-olive-200 text-xs focus:border-olive-500 focus:ring-olive-500">
                <input type="text" x-model="kamarDefault.tipe_kamar_mandi" placeholder="Kamar mandi"
                       class="rounded-lg border-olive-200 text-xs focus:border-olive-500 focus:ring-olive-500">
            </div>
            <button type="button" @click="applyDefaultToAll()" class="text-xs font-medium text-olive-700 hover:underline">
                ↳ Terapkan ke semua baris kamar di bawah
            </button>
        </div>

        <div class="flex flex-wrap items-center gap-1.5 mb-4">
            <input type="text" x-model="genPrefix" placeholder="Awalan (mis. A)"
                   class="w-28 rounded-lg border-olive-200 text-xs focus:border-olive-500 focus:ring-olive-500">
            <input type="number" x-model.number="genJumlah" min="1" placeholder="Jumlah"
                   class="w-20 rounded-lg border-olive-200 text-xs focus:border-olive-500 focus:ring-olive-500">
            <button type="button" @click="generateKamar()" class="rounded-full bg-olive-700 px-3 py-1.5 text-xs font-medium text-white hover:bg-olive-800">
                Generate Kamar
            </button>
        </div>

        <p x-show="kamarList.length === 0" x-cloak class="text-xs text-olive-500 mb-3">
            Belum ada baris kamar. Generate otomatis di atas, atau tambah manual satu per satu.
        </p>

        <div class="space-y-2 mb-3 max-h-96 overflow-y-auto pr-1">
            <template x-for="(kamar, index) in kamarList" :key="index">
                <div class="rounded-lg border border-olive-100 p-2.5 space-y-1.5">
                    <div class="flex gap-1.5">
                        <input type="text" :name="`kamar[${index}][kode]`" form="{{ $formId }}" x-model="kamar.kode" placeholder="Kode (mis. A1)" required
                               class="flex-1 rounded-lg border-olive-200 text-xs focus:border-olive-500 focus:ring-olive-500">
                        <button type="button" @click="removeKamar(index)" class="text-red-500 text-xs px-1.5">✕</button>
                    </div>
                    <div class="grid grid-cols-2 gap-1.5">
                        <div>
                            <input type="number" :name="`kamar[${index}][harga]`" form="{{ $formId }}" x-model.number="kamar.harga" placeholder="Harga/bln" required
                                   class="w-full rounded-lg border-olive-200 text-xs focus:border-olive-500 focus:ring-olive-500">
                            <p class="mt-0.5 text-[11px] text-olive-400" x-show="kamar.harga" x-cloak x-text="'Rp ' + Number(kamar.harga).toLocaleString('id-ID')"></p>
                        </div>
                        <div>
                            <input type="number" :name="`kamar[${index}][harga_tahunan]`" form="{{ $formId }}" x-model.number="kamar.harga_tahunan" placeholder="Harga/thn"
                                   class="w-full rounded-lg border-olive-200 text-xs focus:border-olive-500 focus:ring-olive-500">
                            <p class="mt-0.5 text-[11px] text-olive-400" x-show="kamar.harga_tahunan" x-cloak x-text="'Rp ' + Number(kamar.harga_tahunan).toLocaleString('id-ID')"></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-1.5">
                        <input type="text" :name="`kamar[${index}][ukuran_kamar]`" form="{{ $formId }}" x-model="kamar.ukuran_kamar" placeholder="Ukuran"
                               class="rounded-lg border-olive-200 text-xs focus:border-olive-500 focus:ring-olive-500">
                        <input type="text" :name="`kamar[${index}][tipe_kamar_mandi]`" form="{{ $formId }}" x-model="kamar.tipe_kamar_mandi" placeholder="KM"
                               class="rounded-lg border-olive-200 text-xs focus:border-olive-500 focus:ring-olive-500">
                    </div>
                </div>
            </template>
        </div>

        <button type="button" @click="addKamar()" class="text-sm font-medium text-olive-700 hover:underline">
            + Tambah 1 Baris Kamar
        </button>

        @if ($property)
            <div class="mt-4 pt-4 border-t border-olive-100">
                <button type="submit" form="{{ $formId }}" class="w-full rounded-full bg-olive-700 px-4 py-2 text-sm font-medium text-white hover:bg-olive-800">
                    Simpan Kamar Baru
                </button>
            </div>
        @endif
    </div>
</div>
