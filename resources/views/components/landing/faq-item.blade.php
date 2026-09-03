@props([
    'index', 
    'pertanyaan' => '', 
    'jawaban' => ''
])

@php
    $safeIndex = is_numeric($index) ? (int)$index : e($index);
    $searchContent = strtolower(e($pertanyaan) . ' ' . e($jawaban));
@endphp

<div class="faq-item bg-white border-4 border-black neo-card-shadow transition-all duration-200"
     data-search="{{ $searchContent }}">
    <button type="button" 
            id="faq-accordion-button-{{ $safeIndex }}"
            @click="active === '{{ $safeIndex }}' || active === {{ $safeIndex }} ? active = null : active = '{{ $safeIndex }}'"
            class="w-full text-left p-6 flex items-center justify-between gap-4 font-black uppercase text-sm sm:text-base tracking-tight hover:bg-yellow-100 transition-colors focus:outline-none">
        <span>{{ $pertanyaan }}</span>
        <span class="text-xl sm:text-2xl font-black text-black shrink-0 transition-transform duration-200"
              :class="(active === '{{ $safeIndex }}' || active === {{ $safeIndex }}) ? 'rotate-45' : 'rotate-0'">
            ＋
        </span>
    </button>
    
    <div x-show="active === '{{ $safeIndex }}' || active === {{ $safeIndex }}" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 transform -translate-y-1"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-1"
         x-cloak
         class="border-t-4 border-black p-6 bg-yellow-50 text-slate-800 text-xs sm:text-sm font-semibold leading-relaxed">
        {!! nl2br(e($jawaban)) !!}
    </div>
</div>
