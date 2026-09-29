@php
    $needle = strtolower($name);
    $icon = match (true) {
        str_contains($needle, 'wifi') => 'wifi',
        str_contains($needle, 'mandi') || str_contains($needle, 'air') => 'droplet',
        str_contains($needle, 'parkir') => 'car',
        str_contains($needle, 'lemari') || str_contains($needle, 'wardrobe') => 'wardrobe',
        str_contains($needle, 'meja') || str_contains($needle, 'kursi') => 'desk',
        str_contains($needle, 'ac') || str_contains($needle, 'pendingin') => 'wind',
        str_contains($needle, 'dapur') || str_contains($needle, 'masak') => 'flame',
        str_contains($needle, 'kasur') || str_contains($needle, 'tidur') => 'bed',
        default => 'check',
    };
@endphp
<span class="inline-flex h-3.5 w-3.5 shrink-0">
    @switch($icon)
        @case('wifi')
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12.5a10 10 0 0114 0M8 15.5a6 6 0 018 0M12 19h.01" /></svg>
            @break
        @case('droplet')
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3s6 6.5 6 11a6 6 0 11-12 0c0-4.5 6-11 6-11z" /></svg>
            @break
        @case('car')
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13l1.5-5A2 2 0 016.4 6.5h11.2a2 2 0 011.9 1.5L21 13m-18 0v5a1 1 0 001 1h1a1 1 0 001-1v-1h12v1a1 1 0 001 1h1a1 1 0 001-1v-5m-18 0h18" /></svg>
            @break
        @case('wardrobe')
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-full w-full"><rect x="4" y="3" width="16" height="18" rx="1" /><path stroke-linecap="round" d="M12 3v18M8 12v.01M16 12v.01" /></svg>
            @break
        @case('desk')
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9h18M5 9v11m14-11v11M3 19h18M9 9V5a1 1 0 011-1h4a1 1 0 011 1v4" /></svg>
            @break
        @case('wind')
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8h9a2.5 2.5 0 100-5M3 12h13a2.5 2.5 0 110 5M3 16h7a2 2 0 110 4" /></svg>
            @break
        @case('flame')
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M12 22c4 0 7-2.7 7-6.5 0-3-2-5-3-7 0 2-1.5 3-2 3 .5-3-1-6-4-7 .5 2.5-1 4.5-2.5 6.5S6 14 6 16c0 3.3 2.5 6 6 6z" /></svg>
            @break
        @case('bed')
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M3 18v-6a2 2 0 012-2h14a2 2 0 012 2v6M3 18v2m18-2v2M3 12V8a1 1 0 011-1h6a1 1 0 011 1v2m2-2h4a2 2 0 012 2v2" /></svg>
            @break
        @default
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-full w-full"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
    @endswitch
</span>
