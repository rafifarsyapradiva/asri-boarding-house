@extends('layouts.landing')

@section('title', $logoText . ' - Hunian Kost Eksklusif')

@php
    $heroImgRaw = $heroImage ?? config('settings.defaults.hero_image');
    if (empty($heroImgRaw)) {
        $heroImgUrl = asset('images/hero-dummy.png');
    } elseif (str_starts_with($heroImgRaw, 'http://') || str_starts_with($heroImgRaw, 'https://')) {
        $heroImgUrl = $heroImgRaw;
    } elseif (str_starts_with($heroImgRaw, 'images/')) {
        $heroImgUrl = asset($heroImgRaw);
    } else {
        $heroImgUrl = asset('storage/' . $heroImgRaw);
    }
@endphp

@section('content')
<div class="bg-white min-h-screen antialiased text-black">
    
    <!-- SECTION HERO -->
    <section class="relative bg-white border-b-4 border-black py-20 px-6 overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0 pointer-events-none opacity-15 mix-blend-multiply bg-cover bg-center" style="background-image: url('{{ $heroImgUrl }}'); filter: grayscale(40%);"></div>

        <!-- Asymmetric Background Shapes -->
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-yellow-400 border-4 border-black rotate-12 pointer-events-none opacity-40"></div>
        <div class="absolute -bottom-16 -left-16 w-80 h-80 bg-black border-4 border-white rotate-45 pointer-events-none opacity-5"></div>

        <div class="max-w-6xl mx-auto relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <!-- Sisi Kiri: Deskripsi & Call to Action -->
            <div class="lg:col-span-7 text-left flex flex-col items-start">
                <span class="hero-tagline gsap-reveal inline-block py-2 px-5 bg-black text-yellow-400 border-4 border-black text-xs font-black uppercase tracking-widest mb-6 neo-btn-shadow">
                    {{ $heroTagline }}
                </span>
                
                <h1 id="page-heading-home" class="text-4xl sm:text-6xl font-black uppercase tracking-tighter text-black leading-none mb-8">
                    <span class="bg-yellow-400 border-4 border-black px-6 py-3 inline-block -rotate-1 my-2 neo-card-shadow gsap-reveal hero-title-line">
                        {{ strtoupper($heroTitle) }} ⚡
                    </span>
                </h1>
                
                <p class="hero-description gsap-reveal text-sm sm:text-base font-bold border-4 border-black bg-white p-5 neo-card-shadow mb-8 leading-relaxed text-black">
                    {{ $heroDescription }}
                </p>

                <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">
                    <a href="#kamar-list" class="hero-cta-btn gsap-reveal w-full sm:w-auto px-6 py-3.5 bg-yellow-400 text-black border-4 border-black font-black uppercase tracking-wider text-sm neo-btn-shadow neo-btn-yellow-interactive text-center">
                        CARI & PESAN KAMAR SEKARANG
                    </a>
                    @php
                        $waPesan = rawurlencode("Halo Admin Asri Boarding House, saya sedang mengunjungi website dan ingin menanyakan informasi ketersediaan kamar. Terima kasih.");
                    @endphp
                    <a href="https://wa.me/{{ $waNumber }}?text={{ $waPesan }}" target="_blank" rel="noopener noreferrer" class="hero-cta-btn gsap-reveal w-full sm:w-auto px-6 py-3.5 min-h-[44px] bg-white text-black border-4 border-black font-black uppercase tracking-wider text-sm neo-btn-shadow neo-btn-interactive text-center inline-flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984a9.96 9.96 0 0 0 1.333 4.982L2 22l5.233-1.372a9.95 9.95 0 0 0 4.777 1.22c5.507 0 9.99-4.479 9.99-9.986 0-2.668-1.037-5.176-2.924-7.062C17.189 3.038 14.68 2 12.012 2zm5.795 13.918c-.254.717-1.468 1.385-2.018 1.47-.487.076-1.127.127-3.23-.746-2.69-1.12-4.409-3.856-4.544-4.037-.134-.18-1.09-1.45-1.09-2.766 0-1.316.69-1.96.938-2.222.25-.262.548-.328.73-.328.18 0 .363.003.52.01.164.007.387-.063.606.463.224.537.766 1.866.83 2 .066.134.11.292.02.472-.09.18-.135.292-.27.45l-.41.48c-.134.148-.28.307-.12.582.16.27.705 1.155 1.51 1.87.803.71 1.48.93 1.758 1.07.277.135.438.113.601-.073.16-.18.69-.803.876-1.08.188-.276.376-.232.633-.135.26.096 1.636.772 1.918.914.28.14.47.21.538.328.068.12.068.69-.186 1.407z"/>
                        </svg>
                        TANYA LENGKAP VIA WA
                    </a>
                </div>
            </div>

            <!-- Sisi Kanan: Foto Kost -->
            <div class="hero-image-container gsap-reveal lg:col-span-5 w-full flex items-center justify-center relative mt-8 lg:mt-0">
                <div class="absolute inset-0 bg-black border-4 border-black translate-x-3 translate-y-3 pointer-events-none"></div>
                <div class="relative w-full h-[320px] sm:h-[400px] border-4 border-black bg-yellow-400 overflow-hidden">
                    <img src="{{ $heroImgUrl }}" 
                         alt="Foto Kost Asri Boarding House" 
                         class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                </div>
            </div>
        </div>
    </section>

    <!-- BRUTALIST RUNNING MARQUEE -->
    <div class="brutalist-marquee-container bg-yellow-400 border-b-4 border-black py-4 font-black uppercase tracking-widest text-xs sm:text-sm text-black relative z-10 select-none pointer-events-none">
        <div class="brutalist-marquee-content">
            ⚡ WIFI CEPAT &bull; DEKAT KAMPUS &bull; PEMBAYARAN INSTAN &bull; BEBAS JAM MALAM &bull; KAMAR MANDI DALAM &bull; AC DINGIN &bull; LINGKUNGAN ASRI &bull; KASUR SPRINGBED &bull; PARKIR LUAS &bull; PENJAGA KOST &bull;&nbsp;
        </div>
        <div class="brutalist-marquee-content" aria-hidden="true">
            ⚡ WIFI CEPAT &bull; DEKAT KAMPUS &bull; PEMBAYARAN INSTAN &bull; BEBAS JAM MALAM &bull; KAMAR MANDI DALAM &bull; AC DINGIN &bull; LINGKUNGAN ASRI &bull; KASUR SPRINGBED &bull; PARKIR LUAS &bull; PENJAGA KOST &bull;&nbsp;
        </div>
    </div>

    <!-- SECTION LIVE SEARCH / FORM CEK KETERSEDIAAN -->
    <section class="max-w-4xl mx-auto px-6 -mt-10 relative z-20">
        <div class="bg-white border-4 border-black p-6 sm:p-8 neo-card-shadow-yellow bg-white">
            <h3 class="text-xl sm:text-2xl font-black uppercase tracking-tight mb-6 text-black border-b-4 border-black pb-2 inline-block">
                CEK KETERSEDIAAN KAMAR 🔍
            </h3>

            <!-- Error Alert Container -->
            <div id="search-error-container" class="hidden bg-red-100 border-4 border-black p-4 text-black font-black text-xs sm:text-sm mb-6 neo-card-shadow relative">
                <button type="button" class="absolute top-1 right-2 cursor-pointer font-black text-lg select-none" onclick="document.getElementById('search-error-container').classList.add('hidden')" aria-label="Tutup pesan error">×</button>
                <div class="flex items-center gap-2">
                    <span>⚠️</span>
                    <span id="search-error-message"></span>
                </div>
            </div>
            
            <form action="{{ route('landing.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6" id="form-cek-ketersediaan">
                <!-- Check-in Date -->
                <div class="space-y-2">
                    <label for="search-tanggal-masuk" class="block text-xs font-black uppercase tracking-wider text-black">Tanggal Masuk</label>
                    <input type="date" id="search-tanggal-masuk" name="tanggal_masuk" min="{{ date('Y-m-d') }}" value="{{ request('tanggal_masuk', date('Y-m-d')) }}"
                           class="w-full border-4 border-black bg-white px-4 py-3 font-bold text-sm focus:outline-none focus:ring-4 focus:ring-yellow-400 focus:bg-yellow-50 text-black placeholder-gray-500">
                </div>

                <!-- Lease Duration -->
                <div class="space-y-2">
                    <label for="search-durasi" class="block text-xs font-black uppercase tracking-wider text-black">Durasi Sewa (Bulan)</label>
                    <input type="number" id="search-durasi" name="durasi" min="1" value="{{ request('durasi', 1) }}"
                           class="w-full border-4 border-black bg-white px-4 py-3 font-bold text-sm focus:outline-none focus:ring-4 focus:ring-yellow-400 focus:bg-yellow-50 text-black placeholder-gray-500">
                </div>

                <!-- Room Type -->
                <div class="space-y-2">
                    <label for="search-tipe-select" class="block text-xs font-black uppercase tracking-wider text-black">Pilihan Tipe Kamar</label>
                    <select id="search-tipe-select" name="tipe_kamar" onchange="filterKategori(this.value)"
                            class="w-full border-4 border-black bg-white px-4 py-3 font-bold text-sm focus:outline-none focus:ring-4 focus:ring-yellow-400 focus:bg-yellow-50 text-black">
                        <option value="all" {{ request('tipe_kamar') === 'all' ? 'selected' : '' }}>Semua Tipe</option>
                        <option value="vip" {{ request('tipe_kamar') === 'vip' ? 'selected' : '' }}>👑 VIP</option>
                        <option value="deluxe" {{ request('tipe_kamar') === 'deluxe' ? 'selected' : '' }}>✨ Deluxe</option>
                        <option value="standar" {{ request('tipe_kamar') === 'standar' ? 'selected' : '' }}>🏠 Standar</option>
                    </select>
                </div>

                <!-- Live Keyword Filter -->
                <div class="space-y-2">
                    <label for="kamar-search-input" class="block text-xs font-black uppercase tracking-wider text-black">Cari Kamar</label>
                    <div class="relative">
                        <input type="text" id="kamar-search-input" name="q" oninput="jalankanFilter()" value="{{ request('q') }}" placeholder="Nomor kamar..."
                               class="w-full border-4 border-black bg-white px-4 py-3 font-bold text-sm focus:outline-none focus:ring-4 focus:ring-yellow-400 focus:bg-yellow-50 text-black placeholder-gray-500">
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- SECTION FASILITAS -->
    <section id="fasilitas" class="max-w-6xl mx-auto px-6 py-20">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-5xl font-black uppercase tracking-tight inline-block border-b-4 border-black pb-2">
                FASILITAS UTAMA KOST
            </h2>
            <p class="text-black font-bold mt-4 text-sm sm:text-base">Kenyamanan tinggal ekstra dengan fasilitas penunjang berstandar tinggi.</p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            @forelse($fasilitas as $item)
                <x-landing.facility-card :facility="$item" />
            @empty
                <div class="border-4 border-black bg-yellow-100 p-6 text-center col-span-full font-black uppercase text-sm">
                    Belum ada fasilitas aktif yang tersedia.
                </div>
            @endforelse
        </div>

        <div class="text-center">
            <a href="{{ route('landing.fasilitas') }}" class="inline-flex items-center gap-2 bg-black text-white border-4 border-black px-6 py-3 font-black text-sm uppercase tracking-wider neo-btn-shadow neo-btn-interactive">
                Lihat Halaman Fasilitas →
            </a>
        </div>
    </section>

    <!-- SECTION SHOWCASE TIPE KAMAR -->
    <section id="kamar-list" class="max-w-6xl mx-auto px-6 py-20 border-t-4 border-black scroll-mt-24">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-5xl font-black uppercase tracking-tight inline-block border-b-4 border-black pb-2">
                TIPE KAMAR KAMI
            </h2>
            <p class="text-black font-bold mt-4 text-sm sm:text-base">Temukan dan pesan unit kamar kost terbaik Anda dari tipe VIP, Deluxe, dan Standar.</p>
        </div>

        <!-- Category Quick Filter Bar -->
        <div class="bg-white border-4 border-black p-6 neo-card-shadow mb-16 flex flex-wrap items-center justify-between gap-6">
            <div class="flex flex-wrap items-center gap-3">
                <button type="button" onclick="filterKategori('all')" id="btn-cat-all" class="filter-btn px-5 py-3 border-4 border-black bg-yellow-400 text-black font-black uppercase tracking-wider text-xs neo-shadow-sm transition-all duration-200">
                    Semua Tipe
                </button>
                <button type="button" onclick="filterKategori('vip')" id="btn-cat-vip" class="filter-btn px-5 py-3 border-4 border-black bg-white text-black font-black uppercase tracking-wider text-xs transition-all duration-200 hover:bg-yellow-100">
                    👑 VIP
                </button>
                <button type="button" onclick="filterKategori('deluxe')" id="btn-cat-deluxe" class="filter-btn px-5 py-3 border-4 border-black bg-white text-black font-black uppercase tracking-wider text-xs transition-all duration-200 hover:bg-yellow-100">
                    ✨ Deluxe
                </button>
                <button type="button" onclick="filterKategori('standar')" id="btn-cat-standar" class="filter-btn px-5 py-3 border-4 border-black bg-white text-black font-black uppercase tracking-wider text-xs transition-all duration-200 hover:bg-yellow-100">
                    🏠 Standar
                </button>
            </div>
            
            <a href="{{ route('landing.kamar') }}" class="font-black text-sm uppercase tracking-wider bg-black text-white px-4 py-2 border-2 border-black hover:bg-yellow-400 hover:text-black transition">
                Buka Halaman Katalog Kamar →
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($kamarList as $room)
                <x-landing.kamar-card :room="$room" :cleanWa="$waNumber" />
            @empty
                <div class="text-center py-16 border-4 border-black bg-yellow-50 max-w-md mx-auto my-8 neo-card-shadow col-span-full">
                    <span class="text-5xl block mb-4">📭</span>
                    <p class="text-black font-black uppercase text-sm">Saat ini belum ada data kamar yang ditawarkan.</p>
                </div>
            @endforelse
        </div>

        <!-- Room Not Found Banner -->
        <div id="kamar-not-found" class="hidden text-center py-16 border-4 border-black bg-yellow-100 max-w-md mx-auto my-8 neo-card-shadow">
            <span class="text-5xl block mb-4">🔎</span>
            <h4 class="font-black text-black text-xl mb-2 uppercase tracking-tight">Kamar Tidak Ditemukan</h4>
            <p class="text-black font-semibold text-xs px-6 leading-relaxed">
                Maaf, tidak ada kamar kost yang cocok dengan pencarian atau filter kategori Anda. Silakan coba kata kunci lain.
            </p>
        </div>
    </section>

    <!-- SECTION ROOM TOUR VIDEO -->
    <section id="room-tour" class="bg-gray-50 py-16 px-4 border-t border-b border-gray-100">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center max-w-6xl mx-auto">
            <div class="flex flex-col items-start">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4">Lihat Langsung Suasana Asri Kost Kami</h2>
                <p class="text-gray-600 leading-relaxed mb-6">Tonton video room tour resmi untuk melihat langsung lingkungan Asri Boarding House yang sejuk, rindang dengan pepohonan, serta layout kamar yang luas (5x4 meter) lengkap dengan AC, kamar mandi dalam, dan fasilitas komunal modern.</p>
                <span class="inline-flex items-center bg-orange-100 text-orange-700 text-xs font-semibold px-3 py-1.5 rounded-full mb-6">⏱ Jumper: Review Asri Boarding House mulai menit 05:04</span>
                @php
                    $waTourText = rawurlencode("Halo Admin Asri Boarding House, saya telah menonton video room tour unit Anda dan tertarik untuk menanyakan ketersediaan unit kamar yang kosong. Terima kasih.");
                @endphp
                <a href="https://wa.me/{{ $waNumber }}?text={{ $waTourText }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 bg-[#25D366] hover:bg-[#20ba5a] text-black border-4 border-black px-6 py-3 font-black text-sm uppercase tracking-wider neo-btn-shadow neo-btn-interactive">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984a9.96 9.96 0 0 0 1.333 4.982L2 22l5.233-1.372a9.95 9.95 0 0 0 4.777 1.22c5.507 0 9.99-4.479 9.99-9.986 0-2.668-1.037-5.176-2.924-7.062C17.189 3.038 14.68 2 12.012 2zm5.795 13.918c-.254.717-1.468 1.385-2.018 1.47-.487.076-1.127.127-3.23-.746-2.69-1.12-4.409-3.856-4.544-4.037-.134-.18-1.09-1.45-1.09-2.766 0-1.316.69-1.96.938-2.222.25-.262.548-.328.73-.328.18 0 .363.003.52.01.164.007.387-.063.606.463.224.537.766 1.866.83 2 .066.134.11.292.02.472-.09.18-.135.292-.27.45l-.41.48c-.134.148-.28.307-.12.582.16.27.705 1.155 1.51 1.87.803.71 1.48.93 1.758 1.07.277.135.438.113.601-.073.16-.18.69-.803.876-1.08.188-.276.376-.232.633-.135.26.096 1.636.772 1.918.914.28.14.47.21.538.328.068.12.068.69-.186 1.407z"/>
                    </svg>
                    Tanya Ketersediaan via WhatsApp
                </a>
            </div>
            <div class="flex flex-col items-center w-full">
                <div class="aspect-video w-full border-4 border-black rounded-none shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] overflow-hidden bg-black mb-3">
                    <iframe class="w-full h-full" src="https://www.youtube.com/embed/VVWGOTl175w?start=304" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                </div>
                <a href="https://www.youtube.com/watch?v=VVWGOTl175w&t=304s" target="_blank" rel="noopener noreferrer" class="text-xs font-bold text-gray-500 hover:text-yellow-600 transition inline-flex items-center gap-1">
                    📺 Video tidak bisa diputar? Tonton langsung di YouTube &rarr;
                </a>
            </div>
        </div>
    </section>

    <!-- SECTION GALLERY -->
    <section id="gallery" class="max-w-6xl mx-auto px-6 py-20 border-b-4 border-black">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-5xl font-black uppercase tracking-tight inline-block border-b-4 border-black pb-2">
                GALERI ASLI KOST 🖼️
            </h2>
            <p class="text-black font-bold mt-4 text-sm sm:text-base">Melihat lebih dekat suasana asli, kebersihan, dan kenyamanan kamar Kost Asri.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse($galleries->take(3) as $gallery)
                <div class="bg-white border-4 border-black neo-card-shadow overflow-hidden flex flex-col justify-between hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[4px_4px_0px_0px_#000000] transition-all duration-200 group">
                    <div class="h-64 bg-white border-b-4 border-black relative overflow-hidden">
                        <img src="{{ ($gallery->foto && str_starts_with($gallery->foto, 'http')) ? $gallery->foto : asset('storage/' . $gallery->foto) }}" alt="{{ $gallery->judul }}" loading="lazy" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                    </div>
                    <div class="p-6 bg-white flex-1 flex flex-col justify-between">
                        <div>
                            <h4 class="text-xl font-black uppercase tracking-tight text-black border-b-2 border-black pb-2 mb-3">
                                {{ $gallery->judul }}
                            </h4>
                            <p class="text-gray-700 text-xs font-semibold leading-relaxed">
                                {{ $gallery->deskripsi ?? 'Dokumentasi visual lingkungan Kost Asri Boarding House.' }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white border-4 border-black neo-card-shadow overflow-hidden flex flex-col justify-between hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[4px_4px_0px_0px_#000000] transition-all duration-200 group">
                    <div class="h-64 bg-white border-b-4 border-black relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=800&q=80" alt="Kamar VIP" loading="lazy" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                    </div>
                    <div class="p-6 bg-white flex-1">
                        <h4 class="text-xl font-black uppercase tracking-tight text-black border-b-2 border-black pb-2 mb-3">
                            Kamar VIP Eksklusif
                        </h4>
                        <p class="text-gray-700 text-xs font-semibold leading-relaxed">
                            Desain interior mewah dengan tempat tidur queen size, AC, smart TV, kamar mandi dalam, dan meja kerja modern.
                        </p>
                    </div>
                </div>

                <div class="bg-white border-4 border-black neo-card-shadow overflow-hidden flex flex-col justify-between hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[4px_4px_0px_0px_#000000] transition-all duration-200 group">
                    <div class="h-64 bg-white border-b-4 border-black relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80" alt="Kamar Deluxe" loading="lazy" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                    </div>
                    <div class="p-6 bg-white flex-1">
                        <h4 class="text-xl font-black uppercase tracking-tight text-black border-b-2 border-black pb-2 mb-3">
                            Kamar Deluxe Nyaman
                        </h4>
                        <p class="text-gray-700 text-xs font-semibold leading-relaxed">
                            Kamar luas dengan pencahayaan alami yang baik, dilengkapi dengan AC, kamar mandi dalam, lemari pakaian, dan meja belajar.
                        </p>
                    </div>
                </div>

                <div class="bg-white border-4 border-black neo-card-shadow overflow-hidden flex flex-col justify-between hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[4px_4px_0px_0px_#000000] transition-all duration-200 group">
                    <div class="h-64 bg-white border-b-4 border-black relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=800&q=80" alt="Kamar Standar" loading="lazy" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                    </div>
                    <div class="p-6 bg-white flex-1">
                        <h4 class="text-xl font-black uppercase tracking-tight text-black border-b-2 border-black pb-2 mb-3">
                            Kamar Standar Fungsional
                        </h4>
                        <p class="text-gray-700 text-xs font-semibold leading-relaxed">
                            Hunian kost yang praktis dan efisien dengan kasur single, AC, meja belajar, lemari, sirkulasi udara optimal, dan kamar mandi dalam.
                        </p>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('landing.galeri') }}" class="inline-flex items-center gap-2 bg-black text-white border-4 border-black px-6 py-3 font-black text-sm uppercase tracking-wider neo-btn-shadow neo-btn-interactive">
                Lihat Semua Galeri Foto →
            </a>
        </div>
    </section>

    <!-- SECTION ALUR CARA RESERVASI -->
    <section id="cara-booking" class="bg-yellow-400 border-t-4 border-b-4 border-black py-20 px-6">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-5xl font-black uppercase tracking-tighter text-black inline-block border-b-4 border-black pb-2 bg-white px-6 py-2 border-4 border-black neo-btn-shadow">
                    ALUR CARA RESERVASI ⚡
                </h2>
                <p class="text-black font-bold mt-6 text-sm sm:text-base">Proses pemesanan kamar digital mudah, cepat, dan otomatis terintegrasi.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
                <div class="bg-white border-4 border-black p-6 neo-card-shadow flex flex-col items-start gap-4 transition-all hover:translate-x-[2px] hover:translate-y-[2px]">
                    <div class="w-12 h-12 flex items-center justify-center border-4 border-black bg-yellow-400 text-black text-xl font-black shadow-[3px_3px_0px_0px_#000000]">1</div>
                    <h4 class="text-lg font-black uppercase tracking-tight text-black">Pilih & Cek</h4>
                    <p class="text-xs font-semibold leading-relaxed text-black">Pilih kamar impian Anda dari katalog, lalu cek kuota & simulasi harga sewa sesuai durasi.</p>
                </div>
                <div class="bg-white border-4 border-black p-6 neo-card-shadow flex flex-col items-start gap-4 transition-all hover:translate-x-[2px] hover:translate-y-[2px]">
                    <div class="w-12 h-12 flex items-center justify-center border-4 border-black bg-yellow-400 text-black text-xl font-black shadow-[3px_3px_0px_0px_#000000]">2</div>
                    <h4 class="text-lg font-black uppercase tracking-tight text-black">Isi Data Diri</h4>
                    <p class="text-xs font-semibold leading-relaxed text-black">Lengkapi formulir penyewa beserta kontak wali terpercaya untuk verifikasi keamanan.</p>
                </div>
                <div class="bg-white border-4 border-black p-6 neo-card-shadow flex flex-col items-start gap-4 transition-all hover:translate-x-[2px] hover:translate-y-[2px]">
                    <div class="w-12 h-12 flex items-center justify-center border-4 border-black bg-yellow-400 text-black text-xl font-black shadow-[3px_3px_0px_0px_#000000]">3</div>
                    <h4 class="text-lg font-black uppercase tracking-tight text-black">Bayar Instan</h4>
                    <p class="text-xs font-semibold leading-relaxed text-black">Selesaikan transaksi secara realtime & aman menggunakan gateway pembayaran Midtrans Snap.</p>
                </div>
                <div class="bg-white border-4 border-black p-6 neo-card-shadow flex flex-col items-start gap-4 transition-all hover:translate-x-[2px] hover:translate-y-[2px]">
                    <div class="w-12 h-12 flex items-center justify-center border-4 border-black bg-yellow-400 text-black text-xl font-black shadow-[3px_3px_0px_0px_#000000]">4</div>
                    <h4 class="text-lg font-black uppercase tracking-tight text-black">Terima Nota</h4>
                    <p class="text-xs font-semibold leading-relaxed text-black">Terima Nota PDF pembayaran serta Kode Akses Kamar digital Anda untuk check-in.</p>
                </div>
            </div>

            <div class="text-center">
                <a href="{{ route('landing.caraBooking') }}" class="inline-flex items-center gap-2 bg-black text-white border-4 border-black px-6 py-3 font-black text-sm uppercase tracking-wider neo-btn-shadow neo-btn-interactive">
                    Baca Panduan Lengkap →
                </a>
            </div>
        </div>
    </section>

    <!-- SECTION CUSTOMER REVIEWS -->
    <section id="testimoni" class="max-w-6xl mx-auto px-6 py-20 border-b-4 border-black">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-5xl font-black uppercase tracking-tight inline-block border-b-4 border-black pb-2">
                TESTIMONI PENYEWA KOST
            </h2>
            <p class="text-black font-bold mt-4 text-sm sm:text-base">Ulasan asli dari rekan-rekan mahasiswa dan pekerja perantau yang tinggal di Asri Boarding House.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            @forelse($reviews->take(3) as $review)
                <x-landing.review-card :review="$review" />
            @empty
                <div class="text-center py-10 bg-yellow-50 border-4 border-black col-span-full max-w-md mx-auto my-4 neo-card-shadow">
                    <span class="text-4xl block mb-3">💬</span>
                    <p class="text-black font-black uppercase text-xs">Belum ada review pelanggan yang ditampilkan.</p>
                </div>
            @endforelse
        </div>

        <div class="text-center">
            <a href="{{ route('landing.testimoni') }}" class="inline-flex items-center gap-2 bg-black text-white border-4 border-black px-6 py-3 font-black text-sm uppercase tracking-wider neo-btn-shadow neo-btn-interactive">
                Lihat Semua Ulasan →
            </a>
        </div>
    </section>

    <!-- SECTION FAQ -->
    <section id="faq" class="max-w-6xl mx-auto px-6 py-20 border-b-4 border-black scroll-mt-24">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-5xl font-black uppercase tracking-tight inline-block border-b-4 border-black pb-2">
                PERTANYAAN UMUM (FAQ) ❓
            </h2>
            <p class="text-black font-bold mt-4 text-sm sm:text-base">Temukan jawaban cepat untuk pertanyaan-pertanyaan yang sering ditanyakan mengenai Kost Asri.</p>
        </div>

        <div class="max-w-4xl mx-auto mb-12">
            @if($faqs->isEmpty())
                <div class="text-center py-10 bg-yellow-50 border-4 border-black col-span-full max-w-md mx-auto my-4 neo-card-shadow">
                    <span class="text-4xl block mb-3">❓</span>
                    <p class="text-black font-black uppercase text-xs">Belum ada FAQ yang ditampilkan.</p>
                </div>
            @else
                <div x-data="{ active: null }" class="space-y-6">
                    @foreach($faqs as $index => $faq)
                        <x-landing.faq-item :index="$index" :pertanyaan="$faq->pertanyaan" :jawaban="$faq->jawaban" />
                    @endforeach
                </div>
            @endif
        </div>

        <div class="text-center">
            <a href="{{ route('landing.faq') }}" class="inline-flex items-center gap-2 bg-yellow-400 text-black border-4 border-black px-6 py-3.5 font-black text-xs sm:text-sm uppercase tracking-wider neo-btn-shadow neo-btn-yellow-interactive">
                Punya pertanyaan lain? Lihat Selengkapnya di Halaman FAQ Kami →
            </a>
        </div>
    </section>

    <!-- SECTION MAPS LOCATION -->
    <section class="max-w-6xl mx-auto px-6 py-20">
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-5xl font-black uppercase tracking-tight inline-block border-b-4 border-black pb-2">
                LOKASI KAMI 📍
            </h2>
            <p class="text-black font-bold mt-4 text-sm sm:text-base">Temukan kemudahan akses hunian strategis di Tembalang, Semarang.</p>
        </div>

        <div class="w-full border-4 border-black neo-card-shadow bg-yellow-50 h-[350px] sm:h-[450px] google-maps-container overflow-hidden relative">
            @php
                $mapsEmbed = trim($googleMapsEmbed ?? '');
                $hasValidMap = !empty($mapsEmbed) && str_starts_with(strtolower($mapsEmbed), '<iframe') && str_ends_with(strtolower($mapsEmbed), '</iframe>');
            @endphp
            @if($hasValidMap)
                {!! $mapsEmbed !!}
            @else
                <div class="w-full h-full flex flex-col items-center justify-center bg-yellow-50 p-6 text-center">
                    <div class="w-16 h-16 bg-red-400 border-4 border-black flex items-center justify-center text-black mb-4 shadow-[4px_4px_0px_0px_#000000]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25s-7.5-4.108-7.5-11.25A8.25 8.25 0 1119.5 10.5z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black uppercase text-black mb-2">Peta Lokasi Sedang Pemeliharaan</h3>
                    <p class="text-xs sm:text-sm font-bold text-black max-w-md mb-4 leading-relaxed">
                        Peta interaktif tidak dapat dimuat. Silakan lihat lokasi kami di Google Maps menggunakan alamat berikut atau klik tombol di bawah.
                    </p>
                    <div class="bg-white border-4 border-black p-3 text-xs font-black text-black max-w-lg mb-6 shadow-[4px_4px_0px_0px_#000000] select-all break-words">
                        {{ $contactAddress }}
                    </div>
                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($contactAddress) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 bg-yellow-400 text-black border-4 border-black px-5 py-2.5 font-black text-xs uppercase tracking-wider neo-btn-shadow hover:bg-yellow-300 transition-colors">
                        Buka Petunjuk Arah 🗺️
                    </a>
                </div>
            @endif
        </div>
    </section>

    <style>
        .google-maps-container iframe {
            width: 100% !important;
            height: 100% !important;
            border: 0 !important;
        }
    </style>

    <script>
        let kategoriAktif = 'all';
        let shouldSyncUrl = false;

        function syncUrlParams() {
            if (!shouldSyncUrl) return;

            const tanggalMasuk = document.getElementById('search-tanggal-masuk').value;
            const durasi = document.getElementById('search-durasi').value;
            const tipeKamar = document.getElementById('search-tipe-select').value;
            const q = document.getElementById('kamar-search-input').value.trim();

            const url = new URL(window.location.href);
            url.searchParams.set('tanggal_masuk', tanggalMasuk);
            url.searchParams.set('durasi', durasi);
            url.searchParams.set('tipe_kamar', tipeKamar);
            if (q) {
                url.searchParams.set('q', q);
            } else {
                url.searchParams.delete('q');
            }
            window.history.replaceState({}, '', url.toString());
        }

        function filterKategori(kategori) {
            kategoriAktif = kategori;
            shouldSyncUrl = true;

            const selectEl = document.getElementById('search-tipe-select');
            if (selectEl) { selectEl.value = kategori; }

            const buttons = document.querySelectorAll('.filter-btn');
            buttons.forEach(btn => {
                btn.classList.remove('bg-yellow-400', 'neo-shadow-sm');
                btn.classList.add('bg-white', 'hover:bg-yellow-100');
            });

            const activeBtn = document.getElementById('btn-cat-' + kategori);
            if (activeBtn) {
                activeBtn.classList.remove('bg-white', 'hover:bg-yellow-100');
                activeBtn.classList.add('bg-yellow-400', 'neo-shadow-sm');
            }

            jalankanFilter();
            syncUrlParams();
        }

        function jalankanFilter() {
            const query = document.getElementById('kamar-search-input').value.trim().toLowerCase();
            let totalVisible = 0;

            const cards = document.querySelectorAll('.kamar-card');
            cards.forEach(card => {
                const cardTipe = card.getAttribute('data-tipe');
                const searchData = card.getAttribute('data-search') || '';

                const matchesKategori = (kategoriAktif === 'all' || cardTipe === kategoriAktif);
                const matchesQuery = (!query || searchData.includes(query));

                if (matchesKategori && matchesQuery) {
                    card.style.display = 'flex';
                    totalVisible++;
                } else {
                    card.style.display = 'none';
                }
            });

            const notFoundBanner = document.getElementById('kamar-not-found');
            if (totalVisible === 0) {
                notFoundBanner.classList.remove('hidden');
            } else {
                notFoundBanner.classList.add('hidden');
            }

            syncUrlParams();
        }

        const waOwnerNum = @json($waNumber);

        function updatePesanLinks() {
            const tanggalMasukEl = document.getElementById('search-tanggal-masuk');
            const durasiEl = document.getElementById('search-durasi');
            if (!tanggalMasukEl || !durasiEl) return;

            const tanggalMasuk = tanggalMasukEl.value;
            const durasi = durasiEl.value;
            
            const links = document.querySelectorAll('.kamar-action-btn');
            links.forEach(link => {
                if (link.textContent.includes('Pesan Unit')) {
                    const card = link.closest('.kamar-card');
                    const roomHref = card ? card.getAttribute('data-href') : link.getAttribute('href').split('?')[0];
                    link.setAttribute('href', roomHref + '?tanggal_mulai=' + encodeURIComponent(tanggalMasuk) + '&durasi=' + encodeURIComponent(durasi));
                }
            });
        }

        let checkAvailabilityAbortController = null;

        function jalankanCekKetersediaan() {
            const tanggalMasukEl = document.getElementById('search-tanggal-masuk');
            const durasiEl = document.getElementById('search-durasi');
            if (!tanggalMasukEl || !durasiEl) return;

            const tanggalMasuk = tanggalMasukEl.value;
            const durasi = durasiEl.value;
            
            updatePesanLinks();
            syncUrlParams();

            if (checkAvailabilityAbortController) {
                checkAvailabilityAbortController.abort();
            }
            checkAvailabilityAbortController = new AbortController();

            const cards = document.querySelectorAll('.kamar-card');
            cards.forEach(card => {
                const badgeContainer = card.querySelector('.status-badge-container');
                const actionBtn = card.querySelector('.kamar-action-btn');

                if (badgeContainer) {
                    badgeContainer.innerHTML = `
                        <span class="absolute top-4 right-4 bg-gray-200 text-black text-[10px] font-black px-3 py-1.5 border-2 border-black tracking-wider shadow-[2px_2px_0px_0px_#000000] animate-pulse">
                            ⏳ Memeriksa...
                        </span>
                    `;
                }
                if (actionBtn) {
                    actionBtn.classList.add('opacity-50', 'pointer-events-none');
                    actionBtn.innerText = 'Checking...';
                }
            });

            let csrfToken = "";
            const tokenEl = document.querySelector('input[name="_token"]');
            if (tokenEl) {
                csrfToken = tokenEl.value;
            } else {
                const metaToken = document.querySelector('meta[name="csrf-token"]');
                if (metaToken) {
                    csrfToken = metaToken.getAttribute('content');
                }
            }

            fetch('{{ route('landing.cekKetersediaan') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    tanggal_masuk: tanggalMasuk,
                    durasi: durasi
                }),
                signal: checkAvailabilityAbortController.signal
            })
            .then(async response => {
                const data = await response.json().catch(() => null);
                if (!response.ok || !data) {
                    throw new Error((data && data.message) ? data.message : `Gagal mengecek ketersediaan kamar (HTTP ${response.status}).`);
                }
                return data;
            })
            .then(data => {
                const errorContainer = document.getElementById('search-error-container');
                if (errorContainer) {
                    errorContainer.classList.add('hidden');
                }
                if (data.success && data.availability) {
                    const cards = document.querySelectorAll('.kamar-card');
                    cards.forEach(card => {
                        const roomId = card.getAttribute('data-id');
                        const nomorKamar = card.getAttribute('data-nomor');
                        if (!roomId || !data.availability[roomId]) return;

                        const availData = data.availability[roomId];
                        const badgeContainer = card.querySelector('.status-badge-container');
                        const actionBtn = card.querySelector('.kamar-action-btn');

                        if (actionBtn) {
                            actionBtn.classList.remove('opacity-50', 'pointer-events-none');
                        }

                        if (availData.is_available) {
                            if (badgeContainer) {
                                badgeContainer.innerHTML = `
                                    <span class="absolute top-4 right-4 bg-yellow-400 text-black text-[10px] font-black px-3 py-1.5 border-2 border-black tracking-wider shadow-[2px_2px_0px_0px_#000000]">
                                        ✓ Tersedia
                                    </span>
                                `;
                            }
                            if (actionBtn) {
                                const roomHref = card.getAttribute('data-href');
                                actionBtn.href = `${roomHref}?tanggal_mulai=${encodeURIComponent(tanggalMasuk)}&durasi=${encodeURIComponent(durasi)}`;
                                actionBtn.className = "px-5 py-3.5 min-h-[44px] inline-flex items-center justify-center bg-black hover:bg-yellow-400 hover:text-black text-white text-xs font-black border-2 border-black uppercase tracking-wider text-center transition-colors neo-btn-shadow neo-btn-interactive kamar-action-btn";
                                actionBtn.innerHTML = "Pesan Unit →";
                                actionBtn.removeAttribute('target');
                                actionBtn.removeAttribute('rel');
                            }
                        } else {
                            if (badgeContainer) {
                                badgeContainer.innerHTML = `
                                    <span class="absolute top-4 right-4 bg-black text-white text-[10px] font-black px-3 py-1.5 border-2 border-white tracking-wider shadow-[2px_2px_0px_0px_rgba(255,255,255,0.2)]">
                                        TERISI
                                    </span>
                                `;
                            }
                            if (actionBtn) {
                                const waMsg = `Halo Admin Asri Boarding House, saya melihat Kamar ${nomorKamar} saat ini sedang terisi. Apakah saya bisa mendapatkan informasi mengenai perkiraan ketersediaan berikutnya atau masuk daftar tunggu? Terima kasih.`;
                                actionBtn.href = `https://wa.me/${encodeURIComponent(waOwnerNum)}?text=${encodeURIComponent(waMsg)}`;
                                actionBtn.className = "px-5 py-3.5 min-h-[44px] inline-flex items-center justify-center bg-[#25D366] hover:bg-black hover:text-[#25D366] text-black text-xs font-black border-2 border-black uppercase tracking-wider text-center transition-colors neo-btn-shadow neo-btn-interactive kamar-action-btn";
                                actionBtn.innerHTML = "Tanya Ketersediaan via WA 💬";
                                actionBtn.setAttribute('target', '_blank');
                                actionBtn.setAttribute('rel', 'noopener noreferrer');
                            }
                        }
                    });
                }
            })
            .catch(error => {
                if (error.name === 'AbortError') return;
                console.error('Error checking availability:', error);
                
                const cards = document.querySelectorAll('.kamar-card');
                cards.forEach(card => {
                    const actionBtn = card.querySelector('.kamar-action-btn');
                    if (actionBtn) {
                        actionBtn.classList.remove('opacity-50', 'pointer-events-none');
                        if (actionBtn.innerHTML === 'Checking...') {
                            actionBtn.innerHTML = 'Detail Kamar →';
                        }
                    }
                });

                const errorContainer = document.getElementById('search-error-container');
                const errorMessage = document.getElementById('search-error-message');
                if (errorContainer && errorMessage) {
                    errorContainer.classList.remove('hidden');
                    errorMessage.textContent = error.message || 'Gagal mengecek ketersediaan kamar.';
                }
            });
        }

        let debounceTimeout = null;
        function debouncedCekKetersediaan() {
            if (debounceTimeout) {
                clearTimeout(debounceTimeout);
            }
            debounceTimeout = setTimeout(jalankanCekKetersediaan, 300);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const tanggalInput = document.getElementById('search-tanggal-masuk');
            const durasiInput = document.getElementById('search-durasi');
            const selectEl = document.getElementById('search-tipe-select');
            const qInput = document.getElementById('kamar-search-input');

            if (window.location.search) {
                shouldSyncUrl = true;
            }

            if (tanggalInput) {
                tanggalInput.addEventListener('change', function() {
                    shouldSyncUrl = true;
                    debouncedCekKetersediaan();
                });
            }
            if (durasiInput) {
                durasiInput.addEventListener('input', function() {
                    shouldSyncUrl = true;
                    debouncedCekKetersediaan();
                });
            }
            if (selectEl) {
                selectEl.addEventListener('change', function() {
                    shouldSyncUrl = true;
                    filterKategori(selectEl.value);
                });
            }
            if (qInput) {
                qInput.addEventListener('input', function() {
                    shouldSyncUrl = true;
                    jalankanFilter();
                });
            }

            const urlParams = new URLSearchParams(window.location.search);
            const tipeKamar = urlParams.get('tipe_kamar');
            const q = urlParams.get('q');
            
            if (tipeKamar) {
                kategoriAktif = tipeKamar;
                if (selectEl) { selectEl.value = tipeKamar; }
                
                const buttons = document.querySelectorAll('.filter-btn');
                buttons.forEach(btn => {
                    btn.classList.remove('bg-yellow-400', 'neo-shadow-sm');
                    btn.classList.add('bg-white', 'hover:bg-yellow-100');
                });
                const activeBtn = document.getElementById('btn-cat-' + tipeKamar);
                if (activeBtn) {
                    activeBtn.classList.remove('bg-white', 'hover:bg-yellow-100');
                    activeBtn.classList.add('bg-yellow-400', 'neo-shadow-sm');
                }
            }
            if (q) {
                if (qInput) { qInput.value = q; }
            }

            jalankanFilter();
            jalankanCekKetersediaan();
        });
    </script>

</div>
@endsection
