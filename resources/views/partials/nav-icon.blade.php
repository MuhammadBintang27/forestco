@php $name = $name ?? 'dashboard'; @endphp
<span class="h-4.5 w-4.5 shrink-0">
    @switch($name)
        @case('dashboard')
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-full w-full"><rect x="3" y="3" width="8" height="8" rx="1.5" /><rect x="13" y="3" width="8" height="5" rx="1.5" /><rect x="13" y="12" width="8" height="9" rx="1.5" /><rect x="3" y="15" width="8" height="6" rx="1.5" /></svg>
            @break
        @case('properti')
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M3 11l9-7 9 7v9a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9z" /></svg>
            @break
        @case('penyewa')
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-full w-full"><circle cx="9" cy="8" r="3.5" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.5 20a6.5 6.5 0 0113 0M16 8.5a3 3 0 110-6 3 3 0 010 6zm.5 2.5a5.5 5.5 0 015.5 5.5" /></svg>
            @break
        @case('reservasi')
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-5 9l2 2 4-4" /></svg>
            @break
        @case('inspeksi')
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-full w-full"><rect x="3" y="4" width="18" height="17" rx="2" /><path stroke-linecap="round" d="M3 9h18M8 2v4M16 2v4M8 14l2 2 4-4" /></svg>
            @break
        @case('pembayaran')
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-full w-full"><rect x="2" y="5" width="20" height="14" rx="2" /><path stroke-linecap="round" d="M2 10h20M6 15h4" /></svg>
            @break
        @case('perpanjangan')
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h5M20 20v-5h-5M4.5 9a8 8 0 0113.6-3.6L20 8M19.5 15a8 8 0 01-13.6 3.6L4 16" /></svg>
            @break
        @case('kerusakan')
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2L2 20h20L12 2zm0 7v5m0 3h.01" /></svg>
            @break
        @case('laporan')
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V3h9l3 3v3M6 9H4a1 1 0 00-1 1v6a1 1 0 001 1h2m0-8h14m-14 8v4h10v-4m0 0h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2" /></svg>
            @break
        @case('staf')
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-full w-full"><circle cx="10" cy="8" r="3.5" /><path stroke-linecap="round" stroke-linejoin="round" d="M3 20a7 7 0 0114 0M18 8v2m0 4v2m1.7-5.2l-1.73 1m0 4.4l1.73 1m-3.4-6.4l-1.73 1m0 4.4l1.73 1" /></svg>
            @break
        @case('pengaturan')
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-full w-full"><circle cx="12" cy="12" r="3" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1.7 1.7 0 00.34 1.87l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.7 1.7 0 00-1.87-.34 1.7 1.7 0 00-1 1.55V21a2 2 0 11-4 0v-.09A1.7 1.7 0 009 19.4a1.7 1.7 0 00-1.87.34l-.06.06a2 2 0 11-2.83-2.83l.06-.06A1.7 1.7 0 004.6 15a1.7 1.7 0 00-1.55-1H3a2 2 0 110-4h.09A1.7 1.7 0 004.6 9a1.7 1.7 0 00-.34-1.87l-.06-.06a2 2 0 112.83-2.83l.06.06A1.7 1.7 0 009 4.6a1.7 1.7 0 001-1.55V3a2 2 0 114 0v.09a1.7 1.7 0 001 1.55 1.7 1.7 0 001.87-.34l.06-.06a2 2 0 112.83 2.83l-.06.06A1.7 1.7 0 0019.4 9a1.7 1.7 0 001.55 1H21a2 2 0 110 4h-.09a1.7 1.7 0 00-1.55 1z" /></svg>
            @break
        @case('favorit')
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-7.5-4.6-10-9.3C.5 8.2 2.4 5 6 5c2 0 3.5 1 4 2.5C10.5 6 12 5 14 5c3.6 0 5.5 3.2 4 6.7-2.5 4.7-10 9.3-10 9.3z" /></svg>
            @break
        @case('sewa')
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-full w-full"><rect x="3" y="7" width="18" height="14" rx="2" /><path stroke-linecap="round" d="M8 3v6m8-6v6M3 12h18" /></svg>
            @break
        @case('profil')
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-full w-full"><circle cx="12" cy="8" r="4" /><path stroke-linecap="round" d="M4 20a8 8 0 0116 0" /></svg>
            @break
        @case('katalog')
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M11 19a8 8 0 100-16 8 8 0 000 16zm10 2l-4.35-4.35" /></svg>
            @break
        @default
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-full w-full"><circle cx="12" cy="12" r="9" /></svg>
    @endswitch
</span>
