<x-dashboard-layout :title="'Penyewa'">
    @if ($tenants->isEmpty())
        <div class="rounded-2xl border border-olive-100 bg-white p-8 text-center text-olive-600">Belum ada penyewa terdaftar.</div>
    @else
        <div class="overflow-x-auto rounded-2xl border border-olive-100 bg-white">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-olive-100 text-left text-olive-600">
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">WhatsApp</th>
                        <th class="px-4 py-3">Jumlah Reservasi</th>
                        <th class="px-4 py-3">Sewa Aktif</th>
                        <th class="px-4 py-3">Terdaftar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tenants as $tenant)
                        <tr class="border-b border-olive-50 align-top">
                            <td class="px-4 py-3 font-medium text-olive-900">{{ $tenant->nama }}</td>
                            <td class="px-4 py-3 text-olive-700">{{ $tenant->email }}</td>
                            <td class="px-4 py-3 text-olive-700">{{ $tenant->no_telepon }}</td>
                            <td class="px-4 py-3 text-olive-700">{{ $tenant->reservations_count }}</td>
                            <td class="px-4 py-3">
                                @if ($tenant->reservations->isEmpty())
                                    <span class="text-olive-400">Tidak ada</span>
                                @else
                                    <div class="space-y-1.5">
                                        @foreach ($tenant->reservations as $reservation)
                                            @php $rental = $reservation->rental; @endphp
                                            <div>
                                                <p class="font-medium text-olive-900">
                                                    {{ $reservation->property->nama }}
                                                    @if ($reservation->room->hasDisplayableKode()) - Kamar {{ $reservation->room->kode }} @endif
                                                </p>
                                                <p class="text-xs {{ $rental->daysRemaining() <= 7 ? 'text-red-600 font-medium' : 'text-olive-500' }}">
                                                    s.d. {{ $rental->tanggal_selesai->translatedFormat('d M Y') }}
                                                    ({{ $rental->daysRemaining() >= 0 ? 'sisa '.$rental->daysRemaining().' hari' : 'sudah lewat' }})
                                                </p>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-olive-700 whitespace-nowrap">{{ $tenant->created_at->translatedFormat('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $tenants->links() }}</div>
    @endif
</x-dashboard-layout>
