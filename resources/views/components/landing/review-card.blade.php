@props(['review'])

@php
    $bintang = (int) data_get($review, 'bintang', 5);
    $bintang = max(1, min(5, $bintang)); // Clamp rating 1..5
    $ulasan = data_get($review, 'ulasan', '');
    $nama = data_get($review, 'nama', 'Penyewa');
    $pekerjaan = data_get($review, 'pekerjaan') ?? 'Penyewa Kost';
    $foto = data_get($review, 'foto');
    $initial = mb_strtoupper(mb_substr($nama ?: 'A', 0, 1, 'UTF-8'), 'UTF-8');
@endphp

<div class="bg-white p-6 border-4 border-black neo-card-shadow relative overflow-hidden transition-all duration-200 hover:translate-x-[2px] hover:translate-y-[2px]">
    <div class="text-yellow-400 text-7xl font-serif absolute -top-2 right-4 pointer-events-none select-none opacity-50 font-black">“</div>
    
    <!-- Stars -->
    <div class="flex items-center gap-1 mb-5">
        @for($i = 1; $i <= 5; $i++)
            <span class="{{ $i <= $bintang ? 'text-yellow-500' : 'text-gray-300' }} text-lg">★</span>
        @endfor
    </div>

    <!-- Review Text -->
    <p class="text-black text-sm leading-relaxed mb-6 font-bold italic relative z-10">
        "{{ $ulasan }}"
    </p>

    <!-- Reviewer Profile -->
    <div class="flex items-center gap-3.5 border-t-2 border-black pt-4 relative z-10">
        @if($foto)
            <img src="{{ asset('storage/' . $foto) }}" alt="Foto {{ $nama }}" width="48" height="48" class="w-12 h-12 border-2 border-black object-cover shadow-[2px_2px_0px_0px_#000000]">
        @else
            <div class="w-12 h-12 border-2 border-black bg-yellow-400 flex items-center justify-center text-black font-black text-sm shadow-[2px_2px_0px_0px_#000000]">
                {{ $initial }}
            </div>
        @endif
        <div>
            <h4 class="font-black text-black text-sm tracking-tight uppercase">{{ $nama }}</h4>
            <span class="text-[10px] font-bold text-gray-500 block leading-tight mt-0.5 uppercase">{{ $pekerjaan }}</span>
        </div>
    </div>
</div>
