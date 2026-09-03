@if(session('success') || session('error'))
@php
    $isSuccess = (bool) session('success');
    $message = session('success') ?? session('error');
@endphp

<div x-data="{ show: false }"
     x-init="setTimeout(() => show = true, 50); setTimeout(() => show = false, 4500)"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-[-20px]"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-[-20px]"
     class="fixed top-5 right-5 z-50 pointer-events-auto"
     style="display: none;">
    <div class="flex items-center gap-3 p-4 border-4 border-black text-black shadow-[6px_6px_0px_0px_#000000] font-black {{ $isSuccess ? 'bg-emerald-400' : 'bg-red-400' }}">
        <div class="text-xl shrink-0">
            {{ $isSuccess ? '✅' : '❌' }}
        </div>
        <div class="flex-1 pr-2">
            <p class="text-[10px] uppercase tracking-widest opacity-80 leading-none mb-1">
                {{ $isSuccess ? 'Sukses' : 'Gagal' }}
            </p>
            <p class="text-xs uppercase tracking-wider font-bold">
                {{ $message }}
            </p>
        </div>
        <button @click="show = false" class="text-black hover:opacity-75 focus:outline-none font-black text-lg cursor-pointer">
            ✕
        </button>
    </div>
</div>
@endif

