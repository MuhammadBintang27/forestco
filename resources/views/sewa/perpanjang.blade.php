<x-account-layout :title="'Ajukan Perpanjangan'">
    <div class="max-w-lg rounded-2xl border border-olive-100 bg-white p-6">
        <h2 class="font-semibold text-olive-900">{{ $rental->reservation->property->nama }}</h2>
        <p class="text-sm text-olive-600 mt-1">
            Masa sewa saat ini berakhir {{ $rental->tanggal_selesai->translatedFormat('d F Y') }}
        </p>

        <form method="POST" action="{{ route('perpanjangan.store', $rental) }}" class="mt-6 space-y-4">
            @csrf

            <label class="block text-sm text-olive-700">
                Perpanjang Selama
                <select name="durasi_diminta_bulan">
                    @foreach (\App\Models\Reservasi::durasiOptionsFor($rental->room->property->tipe) as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </label>

            <button type="submit" class="w-full rounded-full bg-olive-700 px-5 py-3 text-sm font-medium text-white hover:bg-olive-800">
                Ajukan Perpanjangan
            </button>
        </form>
    </div>
</x-account-layout>
