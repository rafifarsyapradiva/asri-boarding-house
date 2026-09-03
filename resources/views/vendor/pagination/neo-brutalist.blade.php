@php
    /**
     * NEO-BRUTALIST PAGINATION TEMPLATE (REFACTORED)
     * Pemisahan kelas Tailwind dan pemeriksaan tipe paginator untuk performa & skalabilitas.
     */
    
    // 1. Definisikan Token Desain & Utility Class Neo-Brutalist (DRY)
    $shadowBox = 'shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff]';
    $baseBtn = "relative inline-flex items-center text-xs font-black text-black bg-white dark:bg-slate-800 border-2 border-black dark:border-white dark:text-white rounded-none {$shadowBox} transition-all duration-100";
    $hoverStates = 'hover:bg-yellow-400 hover:text-black dark:hover:text-black active:translate-x-0.5 active:translate-y-0.5 active:shadow-none';

    // Variant Class Button
    $navBtnClass = "{$baseBtn} {$hoverStates} px-3 py-2 min-h-[44px] min-w-[44px] justify-center focus:outline-none focus-visible:outline-3 focus-visible:outline-blue-600 dark:focus-visible:outline-blue-400 focus-visible:outline-offset-2";
    $pageLinkClass = "{$baseBtn} {$hoverStates} px-3.5 py-2 min-h-[44px] min-w-[44px] justify-center focus:outline-none focus-visible:outline-3 focus-visible:outline-blue-600 dark:focus-visible:outline-blue-400 focus-visible:outline-offset-2";
    $disabledBtnClass = 'relative inline-flex items-center justify-center px-3 py-2 min-h-[44px] min-w-[44px] text-xs font-black text-gray-400 dark:text-gray-600 bg-gray-100 dark:bg-slate-900 border-2 border-black dark:border-slate-700 rounded-none cursor-not-allowed select-none';
    $activePageClass = "relative inline-flex items-center justify-center px-3.5 py-2 min-h-[44px] min-w-[44px] text-xs font-black text-black bg-yellow-400 border-2 border-black dark:border-white rounded-none {$shadowBox} select-none";
    $dotsClass = 'relative inline-flex items-center justify-center px-3.5 py-2 min-h-[44px] min-w-[44px] text-xs font-black text-gray-500 bg-white dark:bg-slate-800 border-2 border-black dark:border-white rounded-none cursor-default select-none';

    // 2. Deteksi Jenis Paginator
    $isLengthAware = $paginator instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;
@endphp

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col md:flex-row items-center justify-between gap-4 mt-8">
        
        <!-- Bagian 1: Informasi Jumlah Data -->
        <div class="text-sm font-extrabold text-black dark:text-white uppercase tracking-wider">
            @if ($isLengthAware)
                {{ __('Showing') }}
                <span class="px-1.5 py-0.5 border border-black dark:border-white bg-yellow-400 text-black shadow-[1px_1px_0px_0px_#000000]">{{ $paginator->firstItem() }}</span>
                {{ __('to') }}
                <span class="px-1.5 py-0.5 border border-black dark:border-white bg-yellow-400 text-black shadow-[1px_1px_0px_0px_#000000]">{{ $paginator->lastItem() }}</span>
                {{ __('of') }}
                <span class="px-1.5 py-0.5 border border-black dark:border-white bg-cyan-300 text-black shadow-[1px_1px_0px_0px_#000000]">{{ $paginator->total() }}</span>
                {{ __('results') }}
            @else
                {{ __('Page') }} {{ $paginator->currentPage() }}
            @endif
        </div>

        <!-- Bagian 2: Kontrol Tombol Navigasi Halaman -->
        <div class="flex items-center gap-2 flex-wrap">
            {{-- Tombol Halaman Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}" class="{{ $disabledBtnClass }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $navBtnClass }}" aria-label="{{ __('pagination.previous') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            @endif

            {{-- Elemen Navigasi Numerik & Dots (Hanya untuk LengthAwarePaginator) --}}
            @if ($isLengthAware && isset($elements))
                @foreach ($elements as $element)
                    {{-- Separator Titik Tiga (Dots) --}}
                    @if (is_string($element))
                        <span aria-hidden="true" class="{{ $dotsClass }}">
                            {{ $element }}
                        </span>
                    @endif

                    {{-- Link Angka Halaman --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page === $paginator->currentPage())
                                <span aria-current="page" class="{{ $activePageClass }}">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="{{ $pageLinkClass }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            @endif

            {{-- Tombol Halaman Berikutnya --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $navBtnClass }}" aria-label="{{ __('pagination.next') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @else
                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}" class="{{ $disabledBtnClass }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif

