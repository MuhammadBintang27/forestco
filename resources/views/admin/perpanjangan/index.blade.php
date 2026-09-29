@php
    $statuses = ['requested' => 'Menunggu Persetujuan', 'awaiting_payment' => 'Menunggu Pembayaran', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];
@endphp
<x-dashboard-layout :title="'Perpanjangan Sewa'">
    <div class="flex flex-wrap gap-2 mb-4">
        @foreach ($statuses as $value => $label)
            <a href="{{ route('admin.perpanjangan.index', ['status' => $value]) }}"
               class="rounded-full px-4 py-1.5 text-sm {{ $status === $value ? 'bg-olive-700 text-white' : 'bg-white border border-olive-200 text-olive-700 hover:bg-cream-100' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if ($extensions->isEmpty())
        <div class="rounded-2xl border border-olive-100 bg-white p-8 text-center text-olive-600">Tidak ada pengajuan perpanjangan.</div>
    @else
        <div class="space-y-3">
            @foreach ($extensions as $extension)
                @php $penyewa = $extension->rental->reservation->penyewa; @endphp
                <div class="rounded-xl border border-olive-100 bg-white p-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="font-medium text-olive-900">{{ $penyewa->nama }}</p>
                            <p class="text-sm text-olive-600">
                                {{ $extension->rental->reservation->property->nama }} · perpanjang {{ $extension->durasi_diminta_bulan }} bulan
                            </p>
                        </div>
                        @include('partials.status-badge', ['status' => $extension->status, 'label' => $extension->statusLabel()])
                    </div>

                    @if ($extension->status === 'requested')
                        <div class="flex gap-3 mt-3 pt-3 border-t border-olive-100">
                            <form method="POST" action="{{ route('admin.perpanjangan.review', $extension) }}">
                                @csrf
                                <input type="hidden" name="decision" value="approve">
                                <button type="submit" class="rounded-full bg-olive-700 px-4 py-1.5 text-sm font-medium text-white hover:bg-olive-800">
                                    Setujui
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.perpanjangan.review', $extension) }}">
                                @csrf
                                <input type="hidden" name="decision" value="reject">
                                <button type="submit" class="rounded-full border border-red-300 px-4 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50">
                                    Tolak
                                </button>
                            </form>
                        </div>
                    @elseif ($extension->status === 'awaiting_payment' && $extension->latestPayment())
                        <div class="mt-3 pt-3 border-t border-olive-100 text-sm">
                            <a href="{{ $extension->latestPayment()->proofUrl() }}" target="_blank" class="font-medium text-olive-700 hover:underline">
                                Lihat Bukti Transfer
                            </a>
                            <div class="flex gap-3 mt-2">
                                <form method="POST" action="{{ route('admin.pembayaran.review', $extension->latestPayment()) }}">
                                    @csrf
                                    <input type="hidden" name="decision" value="verified">
                                    <button type="submit" class="rounded-full bg-olive-700 px-4 py-1.5 text-sm font-medium text-white hover:bg-olive-800">
                                        Verifikasi
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.pembayaran.review', $extension->latestPayment()) }}">
                                    @csrf
                                    <input type="hidden" name="decision" value="rejected">
                                    <button type="submit" class="rounded-full border border-red-300 px-4 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50">
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $extensions->links() }}</div>
    @endif
</x-dashboard-layout>
