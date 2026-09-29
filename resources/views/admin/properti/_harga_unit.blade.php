@php $soleUnit = ($property ?? null)?->soleUnit(); @endphp

<div x-show="tipe !== 'kos'" x-cloak class="rounded-2xl border border-olive-100 bg-white p-6">
    <h2 class="font-semibold text-olive-900 mb-1">Harga &amp; Detail Unit</h2>
    <p class="text-xs text-olive-500 mb-4">Rumah/ruko disewa sebagai satu unit utuh. Durasi yang ditawarkan otomatis kelipatan tahun (1 atau 2 tahun) - tidak ada opsi 6 bulan.</p>

    @if ($property)
        <div class="mb-4 flex items-center justify-between rounded-lg px-3 py-2 text-sm
                    {{ $soleUnit?->status === 'terisi' ? 'bg-red-50 text-red-700' : 'bg-olive-100 text-olive-800' }}">
            <span class="font-medium">
                {{ $soleUnit?->status === 'terisi' ? '● Sedang Disewa' : '● Tersedia' }}
            </span>
            <label class="text-xs">
                <select name="status_unit" form="{{ $formId }}" class="rounded-lg border-olive-200 text-xs focus:border-olive-500 focus:ring-olive-500">
                    <option value="tersedia" @selected(old('status_unit', $soleUnit?->status) === 'tersedia')>Tersedia</option>
                    <option value="terisi" @selected(old('status_unit', $soleUnit?->status) === 'terisi')>Terisi</option>
                </select>
            </label>
        </div>
        <p class="text-xs text-olive-500 -mt-2 mb-4">
            Status ini otomatis berubah jadi "Terisi" saat pembayaran reservasi diverifikasi. Kalau masa sewa sudah berakhir dan penyewa pindah, ubah manual ke "Tersedia" lagi di sini supaya bisa disewakan ke penyewa baru.
        </p>
    @endif

    <div class="space-y-4">
        <label class="block text-sm text-olive-700">
            Harga per Tahun (Rp)
            <input type="text" inputmode="numeric" name="harga_tahunan" form="{{ $formId }}" value="{{ old('harga_tahunan', \App\Support\Money::digits($soleUnit?->harga_tahunan)) }}"
                   class="money-input mt-1 w-full rounded-lg border-olive-200 text-sm focus:border-olive-500 focus:ring-olive-500">
            <span class="mt-1 block text-xs text-olive-500">Harga untuk sewa 1 tahun. Paket 2 tahun otomatis dihitung 2x harga ini - tidak ada lagi harga per bulan untuk rumah/ruko.</span>
        </label>

        <label class="block text-sm text-olive-700">
            Ukuran (opsional)
            <input type="text" name="ukuran_kamar" form="{{ $formId }}" value="{{ old('ukuran_kamar', $soleUnit?->ukuran_kamar) }}"
                   placeholder="Mis. 90/120 m² atau 6x12 m"
                   class="mt-1 w-full rounded-lg border-olive-200 text-sm focus:border-olive-500 focus:ring-olive-500">
        </label>

        <label class="block text-sm text-olive-700">
            Kamar Mandi (opsional)
            <input type="text" name="tipe_kamar_mandi" form="{{ $formId }}" value="{{ old('tipe_kamar_mandi', $soleUnit?->tipe_kamar_mandi) }}"
                   placeholder="Mis. 2 Kamar Mandi"
                   class="mt-1 w-full rounded-lg border-olive-200 text-sm focus:border-olive-500 focus:ring-olive-500">
        </label>
    </div>
</div>
