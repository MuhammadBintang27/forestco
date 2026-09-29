@php $payment = $payment ?? null; @endphp
@if ($payment)
    @if ($payment->proofIsImage())
        <a href="{{ $payment->proofUrl() }}" target="_blank" rel="noopener" class="block {{ $class ?? 'mt-3 max-w-xs' }}">
            <img src="{{ $payment->proofUrl() }}" alt="Bukti transfer" class="w-full rounded-lg border border-olive-100">
        </a>
    @else
        <a href="{{ $payment->proofUrl() }}" target="_blank" rel="noopener"
           class="mt-3 flex items-center gap-3 rounded-lg border border-olive-100 bg-cream-100 px-4 py-3 {{ $class ?? 'max-w-xs' }} hover:bg-cream-200">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-8 w-8 shrink-0 text-olive-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 2h9l5 5v13a2 2 0 01-2 2H6a2 2 0 01-2-2V4a2 2 0 012-2z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M14 2v6h6" />
            </svg>
            <span class="text-sm font-medium text-olive-800">Lihat Bukti Transfer (PDF)</span>
        </a>
    @endif
@endif
