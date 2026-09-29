<x-account-layout :title="'Sewa Aktif'" :subtitle="'Kelola perpanjangan masa sewa Anda.'">
    <div class="grid grid-cols-1 lg:grid-cols-[260px_1fr] gap-6 items-start">
        @include('partials.account-menu', ['active' => 'sewa'])

        <div>
            @if ($rentals->isEmpty())
                <div class="rounded-2xl border border-olive-100 bg-white p-8 text-center text-olive-600">
                    Anda belum memiliki sewa aktif.
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($rentals as $rental)
                        @php
                            $pendingExtension = $rental->extensions->firstWhere('status', 'requested')
                                ?? $rental->extensions->firstWhere('status', 'awaiting_payment');
                            $daysRemaining = $rental->daysRemaining();
                        @endphp
                        <div class="rounded-2xl border border-olive-100 bg-white p-6">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-semibold text-olive-900">{{ $rental->reservation->property->nama }}</h3>
                                    @if ($rental->reservation->room->hasDisplayableKode())
                                        <p class="text-sm text-olive-600">Kamar {{ $rental->reservation->room->kode }}</p>
                                    @endif
                                </div>
                                @include('partials.status-badge', ['status' => $rental->status, 'label' => $rental->status === 'active' ? 'Aktif' : 'Berakhir'])
                            </div>

                            <p class="mt-3 text-sm text-olive-700">
                                Masa sewa: {{ $rental->tanggal_mulai->translatedFormat('d M Y') }} – {{ $rental->tanggal_selesai->translatedFormat('d M Y') }}
                            </p>

                            @if ($rental->status === 'active')
                                <p class="text-sm mt-1 {{ $daysRemaining <= 7 ? 'text-red-600 font-medium' : 'text-olive-600' }}">
                                    @if ($daysRemaining >= 0)
                                        Sisa {{ $daysRemaining }} hari lagi
                                    @else
                                        Sudah melewati tanggal berakhir
                                    @endif
                                </p>

                                <div class="mt-4">
                                    @if ($pendingExtension)
                                        @if ($pendingExtension->status === 'requested')
                                            <p class="text-sm text-olive-600">Pengajuan perpanjangan {{ $pendingExtension->durasi_diminta_bulan }} bulan sedang menunggu persetujuan admin.</p>
                                        @else
                                            <div class="rounded-lg bg-cream-100 px-4 py-3 text-sm space-y-2">
                                                <p>Perpanjangan disetujui. Silakan lakukan pembayaran untuk memperpanjang masa sewa.</p>
                                                @php $setting = \App\Models\Pengaturan::current(); @endphp
                                                <p>{{ $setting->nama_bank }} - <strong>{{ $setting->nomor_rekening }}</strong> a.n. {{ $setting->nama_pemilik_rekening }}</p>
                                                <form method="POST" action="{{ route('perpanjangan.bayar', $pendingExtension) }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2 pt-1">
                                                    @csrf
                                                    <input type="file" name="proof" required accept=".jpg,.jpeg,.png,.pdf"
                                                           class="text-sm text-olive-700 file:mr-3 file:rounded-full file:border-0 file:bg-olive-100 file:px-3 file:py-1.5 file:text-olive-800">
                                                    <button type="submit" class="rounded-full bg-olive-700 px-4 py-2 text-sm font-medium text-white hover:bg-olive-800">
                                                        Upload Bukti Transfer
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    @else
                                        <a href="{{ route('perpanjangan.create', $rental) }}"
                                           class="inline-block rounded-full border border-olive-300 px-5 py-2 text-sm font-medium text-olive-800 hover:bg-cream-100">
                                            Ajukan Perpanjangan
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-account-layout>
