<x-app-layout :title="'Cara Kerja'">
    <section class="mx-auto max-w-4xl px-4 sm:px-6 py-14">
        <h1 class="text-3xl font-semibold text-olive-900">Cara Kerja</h1>
        <p class="mt-3 text-olive-700">Empat langkah mudah untuk menyewa properti melalui ForestCo.</p>

        <ol class="mt-10 space-y-8">
            @foreach ([
                ['title' => 'Ajukan Reservasi', 'desc' => 'Pilih properti (dan kamar jika kos) di katalog, tentukan durasi sewa, lalu ajukan reservasi. Anda akan diarahkan ke WhatsApp untuk konfirmasi dengan admin.'],
                ['title' => 'Verifikasi & Cek Unit', 'desc' => 'Setelah reservasi diverifikasi admin, Anda bisa mengajukan jadwal cek unit langsung, atau lanjut ke pembayaran.'],
                ['title' => 'Upload Bukti Transfer', 'desc' => 'Transfer sesuai nomor rekening yang tertera, lalu upload bukti transfer. Admin akan memverifikasi pembayaran Anda.'],
                ['title' => 'Resmi Menyewa', 'desc' => 'Setelah pembayaran terverifikasi, Anda resmi menyewa. H-7 sebelum masa sewa berakhir, kami akan mengirim email pengingat perpanjangan.'],
            ] as $i => $step)
                <li class="flex gap-4">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-olive-700 text-white font-semibold">
                        {{ $i + 1 }}
                    </span>
                    <div>
                        <h3 class="font-semibold text-olive-900">{{ $step['title'] }}</h3>
                        <p class="text-sm text-olive-700 mt-1">{{ $step['desc'] }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </section>
</x-app-layout>
