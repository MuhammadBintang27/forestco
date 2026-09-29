@php $active = $active ?? 'reservasi'; @endphp
<div class="rounded-2xl border border-olive-100 bg-white p-4">
    <div class="flex items-center gap-3 px-2 pb-4 mb-2 border-b border-olive-100">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-olive-700 text-sm font-semibold text-white">
            {{ auth()->user()->initials() }}
        </span>
        <div class="min-w-0">
            <p class="font-medium text-olive-900 truncate">{{ auth()->user()->nama }}</p>
            <p class="text-xs text-olive-500 truncate">{{ auth()->user()->email }}</p>
        </div>
    </div>

    <nav class="space-y-1 text-sm">
        <a href="{{ route('reservasi.index') }}"
           class="block rounded-lg px-3 py-2 {{ $active === 'reservasi' ? 'bg-olive-100 font-semibold text-olive-900' : 'text-olive-700 hover:bg-cream-100' }}">
            Riwayat Reservasi
        </a>
        <a href="{{ route('pembayaran.index') }}"
           class="block rounded-lg px-3 py-2 {{ $active === 'pembayaran' ? 'bg-olive-100 font-semibold text-olive-900' : 'text-olive-700 hover:bg-cream-100' }}">
            Status Pembayaran
        </a>
        <a href="{{ route('kerusakan.index') }}"
           class="block rounded-lg px-3 py-2 {{ $active === 'kerusakan' ? 'bg-olive-100 font-semibold text-olive-900' : 'text-olive-700 hover:bg-cream-100' }}">
            Lapor Kerusakan
        </a>
        <a href="{{ route('sewa.index') }}"
           class="block rounded-lg px-3 py-2 {{ $active === 'sewa' ? 'bg-olive-100 font-semibold text-olive-900' : 'text-olive-700 hover:bg-cream-100' }}">
            Sewa Aktif &amp; Perpanjangan
        </a>
        <a href="{{ route('favorit.index') }}"
           class="block rounded-lg px-3 py-2 {{ $active === 'favorit' ? 'bg-olive-100 font-semibold text-olive-900' : 'text-olive-700 hover:bg-cream-100' }}">
            Favorit Saya
        </a>
        <a href="{{ route('profile.edit') }}"
           class="block rounded-lg px-3 py-2 {{ $active === 'profil' ? 'bg-olive-100 font-semibold text-olive-900' : 'text-olive-700 hover:bg-cream-100' }}">
            Profil Saya
        </a>
    </nav>

    <form method="POST" action="{{ route('logout') }}" class="mt-2 pt-2 border-t border-olive-100">
        @csrf
        <button type="submit" class="w-full text-left rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50">
            Keluar
        </button>
    </form>
</div>
