@props(['room', 'waNumber' => null])

@php
    $targetWa = $waNumber ?? config('reservasi.admin_wa', '');
    
    $roomId = data_get($room, 'id');
    $nomorKamar = data_get($room, 'nomor_kamar', '-');
    $tipe = data_get($room, 'tipe', 'Standard');
    $luas = data_get($room, 'luas_m2', 0);
    $harga = data_get($room, 'harga_bulan', 0);
    $status = data_get($room, 'status', 'tersedia');
    $fotoUrl = data_get($room, 'foto_url', asset('images/default-room.jpg'));
    $deskripsi = data_get($room, 'deskripsi') ?? 'Pilihan kamar hunian yang nyaman dengan sirkulasi udara baik dan pencahayaan alami.';
    $lantai = data_get($room, 'lantai', 1);

    // Safely collect facilities without crashing if relation is null or array
    $fasilitasCollection = collect(data_get($room, 'fasilitas', []));
    $fasilitasNames = $fasilitasCollection->pluck('nama')->filter()->implode(' ');

    $searchKeywords = strtolower(implode(' ', [
        $nomorKamar,
        $tipe,
        $deskripsi,
        $luas . 'm2',
        $harga,
        $fasilitasNames
    ]));

    $waPesanKamar = rawurlencode("Halo Admin Asri Boarding House, saya melihat Kamar {$nomorKamar} saat ini sedang terisi. Apakah saya bisa mendapatkan informasi ketersediaan berikutnya? Terima kasih.");
    
    $detailRoute = $roomId && Route::has('landing.show') ? route('landing.show', $roomId) : '#';
@endphp

<div class="bg-white border-4 border-black neo-card-shadow overflow-hidden flex flex-col justify-between kamar-card group" 
     data-tipe="{{ strtolower($tipe) }}" 
     data-search="{{ $searchKeywords }}"
     data-id="{{ $roomId }}"
     data-nomor="{{ $nomorKamar }}"
     data-href="{{ $detailRoute }}"
     x-data="{ loaded: false }">
    
    <!-- Foto Kamar -->
    <div class="h-60 bg-white border-b-4 border-black relative overflow-hidden">
        <div x-show="!loaded" class="absolute inset-0 bg-yellow-100 animate-pulse pointer-events-none shimmer-placeholder"></div>
        <img src="{{ $fotoUrl }}" 
             alt="Foto Kamar {{ $nomorKamar }}" 
             loading="lazy" 
             x-on:load="loaded = true" 
             x-on:error="loaded = true"
             class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
        
        <span class="absolute top-4 left-4 bg-black text-white text-[10px] font-black px-3 py-1.5 border-2 border-white uppercase tracking-widest neo-shadow-sm">
            {{ $tipe }}
        </span>

        <div class="status-badge-container">
            @if($status === 'tersedia')
                <span class="absolute top-4 right-4 bg-yellow-400 text-black text-[10px] font-black px-3 py-1.5 border-2 border-black tracking-wider shadow-[2px_2px_0px_0px_#000000]">
                    ✓ Tersedia
                </span>
            @else
                <span class="absolute top-4 right-4 bg-black text-white text-[10px] font-black px-3 py-1.5 border-2 border-white tracking-wider shadow-[2px_2px_0px_0px_rgba(255,255,255,0.2)]">
                    TERISI
                </span>
            @endif
        </div>
    </div>

    <!-- Detail Kamar -->
    <div class="p-6 flex-1 flex flex-col justify-between bg-white">
        <div class="mb-6">
            <div class="flex items-center justify-between border-b-2 border-black pb-2 mb-4">
                <h4 class="text-2xl font-black uppercase tracking-tight text-black nomor-kamar-text">Kamar {{ $nomorKamar }}</h4>
                <span class="text-xs font-black uppercase bg-black text-white px-2.5 py-1 border border-black">Lantai {{ $lantai }}</span>
            </div>
            
            <p class="text-black font-extrabold text-xs uppercase tracking-wider mb-2">Luas Kamar: {{ $luas }} m²</p>
            
            <p class="text-gray-700 text-sm leading-relaxed mb-4 font-semibold line-clamp-2">
                {{ $deskripsi }}
            </p>
            
            <!-- Amenities List -->
            <div class="flex flex-wrap gap-1.5">
                @foreach($fasilitasCollection->take(3) as $f)
                    <span class="text-[10px] font-black bg-white text-black px-2.5 py-1 border-2 border-black uppercase tracking-wider">
                        {{ data_get($f, 'nama') }}
                    </span>
                @endforeach
                @if($fasilitasCollection->count() > 3)
                    <span class="text-[10px] font-black bg-yellow-400 text-black px-2.5 py-1 border-2 border-black uppercase tracking-wider">
                        +{{ $fasilitasCollection->count() - 3 }}
                    </span>
                @endif
            </div>
        </div>

        <div class="border-t-4 border-black pt-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-auto">
            <div>
                <span class="text-[9px] text-gray-500 block uppercase tracking-widest font-black">HARGA SEWA</span>
                <div class="bg-yellow-400 text-black border-2 border-black font-black text-lg py-1 px-3 inline-block neo-shadow-sm">
                    Rp {{ number_format((float)$harga, 0, ',', '.') }}<span class="text-[10px] uppercase font-bold text-black">/bln</span>
                </div>
            </div>
            
            <!-- Action Button -->
            @if($status === 'tersedia')
                <a href="{{ $detailRoute }}" class="px-5 py-3.5 min-h-[44px] inline-flex items-center justify-center bg-black hover:bg-yellow-400 hover:text-black text-white text-xs font-black border-2 border-black uppercase tracking-wider text-center transition-colors neo-btn-shadow neo-btn-interactive kamar-action-btn">
                    Pesan Unit →
                </a>
            @else
                <a href="https://wa.me/{{ $targetWa }}?text={{ $waPesanKamar }}" target="_blank" rel="noopener noreferrer" class="px-5 py-3.5 min-h-[44px] inline-flex items-center justify-center bg-[#25D366] hover:bg-black hover:text-[#25D366] text-black text-xs font-black border-2 border-black uppercase tracking-wider text-center transition-colors neo-btn-shadow neo-btn-interactive kamar-action-btn">
                    Tanya WA / Ketersediaan 💬
                </a>
            @endif
        </div>
    </div>
</div>
