<x-account-layout :title="'Status Pembayaran'" :subtitle="'Riwayat dan status pembayaran Anda.'">
    <div class="grid grid-cols-1 lg:grid-cols-[260px_1fr] gap-6 items-start">
        @include('partials.account-menu', ['active' => 'pembayaran'])

        <div class="rounded-2xl border border-olive-100 bg-white p-5">
            @if ($payments->isEmpty())
                <p class="text-sm text-olive-600 py-6 text-center">Belum ada riwayat pembayaran.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-olive-100 text-left text-xs uppercase tracking-wide text-olive-500">
                                <th class="py-2 pr-4">Unit</th>
                                <th class="py-2 pr-4">Periode</th>
                                <th class="py-2 pr-4">Jumlah</th>
                                <th class="py-2 pr-4">Status</th>
                                <th class="py-2 pr-4">Bukti</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                @php
                                    $isExtension = (bool) $payment->perpanjangan_sewa_id;
                                    $propertyName = $payment->sewa->reservation->property->nama;
                                    $room = $payment->sewa->room;
                                @endphp
                                <tr class="border-b border-olive-50">
                                    <td class="py-3 pr-4 font-medium text-olive-900">
                                        {{ $propertyName }}{{ $room->hasDisplayableKode() ? ' - '.$room->kode : '' }}
                                    </td>
                                    <td class="py-3 pr-4 text-olive-700">{{ $isExtension ? 'Perpanjangan Sewa' : 'Reservasi Baru' }}</td>
                                    <td class="py-3 pr-4 text-olive-700 whitespace-nowrap">{{ \App\Support\Money::rupiah($payment->jumlah) }}</td>
                                    <td class="py-3 pr-4">
                                        @include('partials.status-badge', ['status' => $payment->status, 'label' => ucfirst($payment->status)])
                                    </td>
                                    <td class="py-3 pr-4">
                                        <a href="{{ $payment->proofUrl() }}" target="_blank" class="text-xs font-medium text-olive-700 hover:underline">Lihat bukti</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-account-layout>
