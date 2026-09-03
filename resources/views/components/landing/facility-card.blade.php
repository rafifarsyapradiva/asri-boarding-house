@props(['facility'])

@php
    $emoji = data_get($facility, 'emoji', '✨');
    $nama = data_get($facility, 'nama', 'Fasilitas');
    $deskripsi = data_get($facility, 'deskripsi', '');
@endphp

<div class="bg-white p-6 border-4 border-black neo-card-shadow flex flex-col items-start text-left transition-all duration-200 hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[4px_4px_0px_0px_#000000]">
    <div class="w-16 h-16 border-4 border-black bg-yellow-400 flex items-center justify-center mb-6 text-3xl shadow-[3px_3px_0px_0px_#000000]">
        {{ $emoji }}
    </div>
    <h3 class="font-black text-black uppercase tracking-tight text-xl mb-3">{{ $nama }}</h3>
    <p class="text-gray-800 text-sm leading-relaxed font-semibold">{{ $deskripsi }}</p>
</div>
