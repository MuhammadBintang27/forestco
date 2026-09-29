@php
    $latestInspection = $reservation->latestInspection();
    $latestPayment = $reservation->latestPayment();
    $displayStatus = $reservation->displayStatus();

    $timeline = [
        [
            'title' => 'Reservasi Diajukan',
            'done' => true,
            'time' => $reservation->created_at->translatedFormat('d M Y, H:i'),
            'desc' => 'Pengajuan berhasil dikirim dan tersimpan di sistem.',
        ],
        [
            'title' => 'Menunggu Verifikasi Admin',
            'done' => $reservation->status !== 'pending',
            'active' => $reservation->status === 'pending',
            'time' => $reservation->status === 'pending' ? 'Sedang berjalan' : 'Selesai',
            'desc' => 'Admin ForestCo akan memeriksa ketersediaan unit dalam 1x24 jam.',
        ],
        [
            'title' => 'Reservasi Disetujui / Ditolak',
            'done' => in_array($displayStatus, ['verified', 'awaiting_payment', 'active', 'ended'], true),
            'active' => in_array($reservation->status, ['rejected', 'dibatalkan'], true),
            'time' => $reservation->status === 'pending' ? 'Belum berlangsung' : $reservation->statusLabel(),
            'desc' => 'Anda akan menerima notifikasi melalui WhatsApp.',
        ],
    ];

    if (in_array($displayStatus, ['awaiting_payment', 'active', 'ended'], true)) {
        $timeline[] = [
            'title' => 'Pembayaran',
            'done' => in_array($displayStatus, ['active', 'ended'], true),
            'active' => $displayStatus === 'awaiting_payment',
            'time' => in_array($displayStatus, ['active', 'ended'], true) ? 'Terverifikasi' : 'Menunggu verifikasi admin',
            'desc' => 'Bukti transfer diperiksa sebelum sewa resmi aktif.',
        ];
    }
@endphp
<x-account-layout>
    <p class="text-sm text-olive-500 mb-1">
        <a href="{{ route('home') }}" class="hover:underline">Beranda</a> /
        <a href="{{ route('reservasi.index') }}" class="hover:underline">Akun Saya</a> /
        Detail Reservasi
    </p>

    <div class="flex flex-wrap items-start justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-olive-900">Detail Reservasi</h1>
            <p class="text-olive-600 mt-1">
                {{ $reservation->property->nama }}
                @if ($reservation->room->hasDisplayableKode()) - Kamar {{ $reservation->room->kode }} @endif
                - diajukan {{ $reservation->created_at->translatedFormat('d M Y') }}
            </p>
        </div>
        @include('partials.status-badge', ['status' => $displayStatus, 'label' => $reservation->statusLabel()])
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6 items-start">
        <div class="space-y-6">
            {{-- Timeline --}}
            <div class="rounded-2xl border border-olive-100 bg-white p-6">
                <h2 class="font-semibold text-olive-900 mb-5">Timeline Proses</h2>
                <ol class="space-y-6">
                    @foreach ($timeline as $step)
                        <li class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <span class="h-3 w-3 rounded-full mt-1 {{ $step['done'] ? 'bg-olive-700' : (($step['active'] ?? false) ? 'border-2 border-olive-700 bg-white' : 'border-2 border-olive-200 bg-white') }}"></span>
                                @if (! $loop->last)
                                    <span class="w-px flex-1 bg-olive-100 mt-1"></span>
                                @endif
                            </div>
                            <div class="pb-1">
                                <p class="font-medium text-olive-900">{{ $step['title'] }}</p>
                                <p class="text-xs text-olive-500 mt-0.5">{{ $step['time'] }}</p>
                                <p class="text-sm text-olive-600 mt-1">{{ $step['desc'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>

            @if ($reservation->status === 'rejected' && $reservation->catatan_admin)
                <div class="rounded-2xl border border-red-100 bg-red-50 p-5 text-sm text-red-700">
                    Alasan penolakan: {{ $reservation->catatan_admin }}
                </div>
            @endif

            @if ($displayStatus === 'verified')
                {{-- Jadwalkan kunjungan --}}
                <div class="rounded-2xl border border-olive-100 bg-white p-6">
                    <h2 class="font-semibold text-olive-900">Jadwalkan Kunjungan / Cek Ulang Unit</h2>
                    <p class="text-sm text-olive-600 mt-1 mb-4">
                        Ingin melihat langsung kondisi unit sebelum reservasi difinalisasi? Ajukan jadwal kunjungan dan admin ForestCo akan mengonfirmasi waktu yang sesuai.
                    </p>

                    @if ($latestInspection && in_array($latestInspection->status, ['pending', 'approved'], true))
                        <div class="flex items-center justify-between rounded-lg bg-cream-100 px-3 py-2 text-sm">
                            <span>{{ $latestInspection->scheduleLabel() }}</span>
                            @include('partials.status-badge', ['status' => $latestInspection->status, 'label' => $latestInspection->statusLabel()])
                        </div>
                    @else
                        @if ($latestInspection && $latestInspection->status === 'rejected')
                            <p class="text-sm text-red-600 mb-3">
                                Jadwal sebelumnya ditolak{{ $latestInspection->catatan_admin ? ": {$latestInspection->catatan_admin}" : '' }}. Silakan ajukan waktu lain.
                            </p>
                        @endif
                        <form method="POST" action="{{ route('inspeksi.store', $reservation) }}" class="space-y-3">
                            @csrf
                            <div class="grid grid-cols-2 gap-3">
                                <label class="block text-sm text-olive-700">
                                    Tanggal Kunjungan
                                    <input type="date" name="tanggal_diminta" required min="{{ now()->addDay()->toDateString() }}">
                                </label>
                                <label class="block text-sm text-olive-700">
                                    Jam Kunjungan
                                    <input type="time" name="waktu_diminta">
                                </label>
                            </div>
                            <label class="block text-sm text-olive-700">
                                Catatan (opsional)
                                <textarea name="catatan" rows="2" placeholder="Contoh: mohon didampingi untuk cek kondisi kamar mandi."></textarea>
                            </label>
                            <button type="submit" class="rounded-full bg-olive-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-olive-800">
                                Ajukan Jadwal Kunjungan
                            </button>
                        </form>
                    @endif

                    @if ($latestInspection && $latestInspection->status === 'done')
                        <p class="text-sm text-olive-600">Unit sudah dikunjungi pada {{ $latestInspection->scheduleLabel() }}.</p>
                    @endif
                </div>

                {{-- Payment --}}
                <div class="rounded-2xl border border-olive-100 bg-white p-6">
                    <h2 class="font-semibold text-olive-900">Langsung Bayar</h2>
                    <p class="text-sm text-olive-600 mt-1 mb-4">
                        Sudah yakin tanpa perlu cek ulang unit? Transfer sebesar <strong>{{ \App\Support\Money::rupiah($reservation->totalAmount()) }}</strong> ke rekening berikut, lalu upload bukti transfer.
                    </p>

                    @php $setting = \App\Models\Pengaturan::current(); @endphp
                    <div class="rounded-lg bg-cream-100 px-4 py-3 text-sm mb-4">
                        <p>{{ $setting->nama_bank }}</p>
                        <p class="font-semibold text-olive-900">{{ $setting->nomor_rekening }}</p>
                        <p>a.n. {{ $setting->nama_pemilik_rekening }}</p>
                    </div>

                    @if ($latestPayment && $latestPayment->status === 'rejected')
                        <p class="text-sm text-red-600 mb-2">
                            Bukti transfer sebelumnya ditolak{{ $latestPayment->catatan_admin ? ": {$latestPayment->catatan_admin}" : '' }}. Silakan upload ulang.
                        </p>
                    @endif

                    <form method="POST" action="{{ route('pembayaran.store', $reservation) }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2">
                        @csrf
                        <input type="file" name="proof" required accept=".jpg,.jpeg,.png,.pdf"
                               class="text-sm text-olive-700 file:mr-3 file:rounded-full file:border-0 file:bg-olive-100 file:px-3 file:py-1.5 file:text-olive-800">
                        <button type="submit" class="rounded-full bg-olive-700 px-5 py-2 text-sm font-medium text-white hover:bg-olive-800">
                            Upload Bukti Transfer
                        </button>
                    </form>
                </div>
            @endif

            @if ($displayStatus === 'awaiting_payment')
                <div class="rounded-2xl border border-olive-100 bg-white p-6 text-sm text-olive-700">
                    Bukti transfer Anda sedang diverifikasi admin.
                    @include('partials.payment-proof-preview', ['payment' => $latestPayment])
                </div>
            @endif

            @if (in_array($displayStatus, ['active', 'ended'], true) && $reservation->rental)
                <div class="rounded-2xl border border-olive-100 bg-white p-6 text-sm text-olive-700">
                    <p>Selamat! Anda resmi menyewa properti ini.</p>
                    <a href="{{ route('sewa.index') }}" class="mt-3 inline-block font-medium text-olive-800 hover:underline">Lihat Sewa Aktif →</a>
                </div>
            @endif

            @if ($reservation->status === 'pending')
                <div class="rounded-2xl border border-red-100 bg-white p-6">
                    <h2 class="font-semibold text-red-700">Batalkan Reservasi</h2>
                    <p class="text-sm text-olive-600 mt-1 mb-4">
                        Anda dapat membatalkan reservasi selama status masih Menunggu Verifikasi. Pembatalan tidak dapat diurungkan.
                    </p>
                    <form method="POST" action="{{ route('reservasi.cancel', $reservation) }}" onsubmit="return confirm('Batalkan reservasi ini?')">
                        @csrf
                        <button type="submit" class="rounded-full border border-red-300 px-5 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50">
                            Batalkan Reservasi Ini
                        </button>
                    </form>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6 lg:sticky lg:top-6">
            <div class="rounded-2xl border border-olive-100 bg-white p-5">
                <h3 class="text-sm font-semibold text-olive-900 mb-3">Informasi Unit</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between gap-3">
                        <dt class="text-olive-500">Properti</dt>
                        <dd class="font-medium text-olive-900 text-right">{{ $reservation->property->nama }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-olive-500">Lokasi</dt>
                        <dd class="font-medium text-olive-900 text-right">{{ $reservation->property->alamat }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-olive-500">Jenis</dt>
                        <dd class="font-medium text-olive-900 text-right">{{ ucfirst($reservation->property->tipe) }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-olive-500">Harga /{{ $reservation->room->primaryPriceUnit() }}</dt>
                        <dd class="font-medium text-olive-900 text-right">{{ \App\Support\Money::rupiah($reservation->room->primaryPrice()) }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-2xl border border-olive-100 bg-white p-5">
                <h3 class="text-sm font-semibold text-olive-900 mb-3">Detail Reservasi</h3>
                <dl class="space-y-2 text-sm">
                    @if ($reservation->rental)
                        <div class="flex justify-between gap-3">
                            <dt class="text-olive-500">Mulai Sewa</dt>
                            <dd class="font-medium text-olive-900 text-right">{{ $reservation->rental->tanggal_mulai->translatedFormat('d M Y') }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between gap-3">
                        <dt class="text-olive-500">Durasi</dt>
                        <dd class="font-medium text-olive-900 text-right">{{ $reservation->durasi_bulan }} Bulan</dd>
                    </div>
                    @if ($reservation->rental)
                        <div class="flex justify-between gap-3">
                            <dt class="text-olive-500">Selesai Sewa</dt>
                            <dd class="font-medium text-olive-900 text-right">{{ $reservation->rental->tanggal_selesai->translatedFormat('d M Y') }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between gap-3">
                        <dt class="text-olive-500">Total</dt>
                        <dd class="font-medium text-olive-900 text-right">{{ \App\Support\Money::rupiah($reservation->totalAmount()) }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-olive-500">Nomor Reservasi</dt>
                        <dd class="font-medium text-olive-900 text-right">{{ $reservation->nomor() }}</dd>
                    </div>
                </dl>
            </div>

            @if ($reservation->catatan)
                <div class="rounded-2xl border border-olive-100 bg-white p-5">
                    <h3 class="text-sm font-semibold text-olive-900 mb-2">Catatan Anda</h3>
                    <p class="text-sm text-olive-600">"{{ $reservation->catatan }}"</p>
                </div>
            @endif
        </div>
    </div>
</x-account-layout>
