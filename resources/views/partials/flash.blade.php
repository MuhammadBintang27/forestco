<div class="fixed top-4 inset-x-4 sm:inset-x-auto sm:right-4 z-50 flex flex-col gap-2 sm:w-96 pointer-events-none">
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="pointer-events-auto flex items-start gap-3 rounded-xl border border-olive-400/30 bg-olive-100 px-4 py-3 text-sm text-olive-800 shadow-lg">
            <span class="flex-1">{{ session('success') }}</span>
            <button type="button" @click="show = false" class="shrink-0 text-olive-500 hover:text-olive-800">✕</button>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="pointer-events-auto flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 shadow-lg">
            <span class="flex-1">{{ session('error') }}</span>
            <button type="button" @click="show = false" class="shrink-0 text-red-400 hover:text-red-700">✕</button>
        </div>
    @endif

    @if ($errors->any())
        <div x-data="{ show: true }" x-show="show"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="pointer-events-auto flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 shadow-lg">
            <ul class="flex-1 list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" @click="show = false" class="shrink-0 text-red-400 hover:text-red-700">✕</button>
        </div>
    @endif

    @if (session('wa_redirect'))
        <div x-data="{ show: true, url: @js(session('wa_redirect')) }" x-show="show"
             x-init="setTimeout(() => window.open(url, '_blank'), 400)"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="pointer-events-auto flex flex-col gap-2 rounded-xl border border-mustard-500/40 bg-mustard-100 px-4 py-3 text-sm text-olive-900 shadow-lg">
            <div class="flex items-start gap-3">
                <span class="flex-1">Anda akan diarahkan ke WhatsApp untuk konfirmasi. Jika tidak terbuka otomatis, klik tombol di bawah.</span>
                <button type="button" @click="show = false" class="shrink-0 text-olive-500 hover:text-olive-800">✕</button>
            </div>
            <a :href="url" target="_blank" rel="noopener"
               class="self-start rounded-lg bg-olive-700 px-3 py-1.5 font-medium text-white hover:bg-olive-800">
                Buka WhatsApp
            </a>
        </div>
    @endif
</div>
