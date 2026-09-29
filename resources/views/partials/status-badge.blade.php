@php
    $palette = [
        'pending' => ['bg-mustard-100 text-olive-800', 'bg-mustard-500'],
        'requested' => ['bg-mustard-100 text-olive-800', 'bg-mustard-500'],
        'review' => ['bg-mustard-100 text-olive-800', 'bg-mustard-500'],
        'baru' => ['bg-red-100 text-red-700', 'bg-red-500'],
        'diproses' => ['bg-mustard-100 text-olive-800', 'bg-mustard-500'],
        'verified' => ['bg-olive-100 text-olive-800', 'bg-olive-600'],
        'approved' => ['bg-olive-100 text-olive-800', 'bg-olive-600'],
        'awaiting_payment' => ['bg-mustard-100 text-olive-800', 'bg-mustard-500'],
        'active' => ['bg-olive-700 text-white', 'bg-white'],
        'done' => ['bg-olive-200 text-olive-700', 'bg-olive-600'],
        'selesai' => ['bg-olive-200 text-olive-700', 'bg-olive-600'],
        'ended' => ['bg-olive-200 text-olive-700', 'bg-olive-600'],
        'rejected' => ['bg-red-100 text-red-700', 'bg-red-500'],
        'dibatalkan' => ['bg-olive-200 text-olive-700', 'bg-olive-500'],
    ];
    [$classes, $dot] = $palette[$status] ?? ['bg-olive-100 text-olive-800', 'bg-olive-600'];
@endphp
<span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium whitespace-nowrap {{ $classes }}">
    <span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>
    {{ $label }}
</span>
