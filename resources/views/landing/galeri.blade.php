@extends('layouts.landing')

@section('title', 'Galeri - ' . $logoText)

@section('content')
<div class="bg-white min-h-screen antialiased text-black">

    <!-- CONTENT -->
    <section class="max-w-6xl mx-auto px-6 py-20">
        <!-- Back Button -->
        <a href="{{ route('landing.index') }}" class="inline-flex items-center gap-2 bg-white text-black border-4 border-black px-4 py-3 min-h-[44px] font-black text-xs uppercase tracking-wider neo-btn-shadow neo-btn-interactive mb-10">
            ← Kembali ke Beranda
        </a>

        <div class="text-center mb-16">
            <h1 id="page-heading-galeri" class="text-4xl sm:text-6xl font-black uppercase tracking-tighter text-black inline-block border-b-4 border-black pb-2 bg-yellow-400 px-6 py-2 border-4 border-black neo-btn-shadow animate-bounce-slow">
                GALERI FOTO KOST ASRI 🖼️
            </h1>
            <p class="text-black font-bold mt-6 text-sm sm:text-base max-w-xl mx-auto">Jelajahi keindahan dan kenyamanan fasilitas serta lingkungan Kost Asri secara detail melalui kumpulan foto kami.</p>
        </div>

        <!-- Search Panel -->
        <div class="bg-white border-4 border-black p-6 neo-card-shadow mb-16 max-w-2xl mx-auto">
            <div class="flex flex-col gap-2">
                <label for="gallery-search" class="block text-xs font-black uppercase tracking-wider text-black">Cari Foto / Dokumentasi</label>
                <input type="text" id="gallery-search" oninput="filterGallery()" placeholder="Ketik kata kunci (misal: kamar, dapur, wifi, vip)..." 
                       class="w-full border-4 border-black bg-white px-4 py-3 font-bold text-sm focus:outline-none focus:ring-4 focus:ring-yellow-400 focus:bg-yellow-50 text-black placeholder-gray-500">
            </div>
        </div>

        <!-- Grid of items -->
        <div id="gallery-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($galleries as $gallery)
                <div class="bg-white border-4 border-black neo-card-shadow overflow-hidden flex flex-col justify-between hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[4px_4px_0px_0px_#000000] transition-all duration-200 gallery-item group"
                     data-title="{{ strtolower($gallery->judul) }}"
                     data-desc="{{ strtolower($gallery->deskripsi ?? '') }}">
                    <!-- Foto -->
                    <button type="button" class="w-full text-left p-0 h-64 bg-white border-b-4 border-black relative overflow-hidden cursor-pointer focus:outline-none focus:ring-4 focus:ring-yellow-400 block" onclick="openLightbox({{ $loop->index }})" aria-label="Perbesar foto {{ $gallery->judul }}">
                        <div class="absolute inset-0 bg-yellow-100 animate-pulse pointer-events-none shimmer-placeholder"></div>
                        <img src="{{ ($gallery->foto && str_starts_with($gallery->foto, 'http')) ? $gallery->foto : asset('storage/' . $gallery->foto) }}" alt="{{ $gallery->judul }}" loading="lazy" onload="removeShimmer(this)" onerror="removeShimmer(this)" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                        <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity duration-200">
                            <span class="bg-yellow-400 text-black border-2 border-black font-black px-4 py-2 text-xs uppercase tracking-wider neo-shadow-sm transform group-hover:scale-110 transition-transform">
                                🔍 Perbesar Foto
                            </span>
                        </div>
                    </button>
                    <!-- Info -->
                    <div class="p-6 bg-white flex-1 flex flex-col justify-between">
                        <div>
                            <h4 class="text-xl font-black uppercase tracking-tight text-black border-b-2 border-black pb-2 mb-3">
                                {{ $gallery->judul }}
                            </h4>
                            <p class="text-gray-700 text-xs font-semibold leading-relaxed mb-4">
                                {{ $gallery->deskripsi ?? 'Dokumentasi visual lingkungan Kost Asri Boarding House.' }}
                            </p>
                        </div>
                        <div class="mt-auto pt-4 border-t-2 border-dashed border-gray-300 flex items-center justify-between">
                            <button onclick="openLightbox({{ $loop->index }})" class="text-xs font-black uppercase text-black hover:text-yellow-600 transition flex items-center gap-1">
                                Perbesar 🔍
                            </button>
                            @php
                                $galleryWaText = rawurlencode("Halo Admin Asri Boarding House, saya melihat foto " . $gallery->judul . " di galeri dan tertarik untuk menanyakan ketersediaan unit tersebut. Terima kasih.");
                            @endphp
                            <a href="https://wa.me/{{ $waNumber }}?text={{ $galleryWaText }}" target="_blank" rel="noopener noreferrer" class="text-xs font-black uppercase text-green-600 hover:text-black transition flex items-center gap-1">
                                Tanya WA 💬
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Fallback 3 static boxes if no data in database -->
                @php
                    $staticItems = [
                        ['judul' => 'Kamar VIP Eksklusif', 'desc' => 'Desain interior mewah dengan tempat tidur queen size, AC, smart TV, kamar mandi dalam, dan meja kerja modern.', 'foto' => 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=800&q=80'],
                        ['judul' => 'Kamar Deluxe Nyaman', 'desc' => 'Kamar luas dengan pencahayaan alami yang baik, dilengkapi dengan AC, kamar mandi dalam, lemari pakaian, dan meja belajar.', 'foto' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80'],
                        ['judul' => 'Kamar Standar Fungsional', 'desc' => 'Hunian kost yang praktis dan efisien dengan kasur single, AC, meja belajar, lemari, sirkulasi udara optimal, dan kamar mandi dalam.', 'foto' => 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=800&q=80']
                    ];
                @endphp
                @foreach($staticItems as $idx => $sItem)
                    <div class="bg-white border-4 border-black neo-card-shadow overflow-hidden flex flex-col justify-between hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[4px_4px_0px_0px_#000000] transition-all duration-200 gallery-item group"
                         data-title="{{ strtolower($sItem['judul']) }}"
                         data-desc="{{ strtolower($sItem['desc']) }}">
                        <button type="button" class="w-full text-left p-0 h-64 bg-white border-b-4 border-black relative overflow-hidden cursor-pointer focus:outline-none focus:ring-4 focus:ring-yellow-400 block" onclick="openLightbox({{ $idx }})" aria-label="Perbesar foto {{ $sItem['judul'] }}">
                            <div class="absolute inset-0 bg-yellow-100 animate-pulse pointer-events-none shimmer-placeholder"></div>
                            <img src="{{ $sItem['foto'] }}" alt="{{ $sItem['judul'] }}" loading="lazy" onload="removeShimmer(this)" onerror="removeShimmer(this)" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                            <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity duration-200">
                                <span class="bg-yellow-400 text-black border-2 border-black font-black px-4 py-2 text-xs uppercase tracking-wider neo-shadow-sm transform group-hover:scale-110 transition-transform">
                                    🔍 Perbesar Foto
                                </span>
                            </div>
                        </button>
                        <div class="p-6 bg-white flex-1 flex flex-col justify-between">
                            <div>
                                <h4 class="text-xl font-black uppercase tracking-tight text-black border-b-2 border-black pb-2 mb-3">
                                    {{ $sItem['judul'] }}
                                </h4>
                                <p class="text-gray-700 text-xs font-semibold leading-relaxed mb-4">
                                    {{ $sItem['desc'] }}
                                </p>
                            </div>
                            <div class="mt-auto pt-4 border-t-2 border-dashed border-gray-300 flex items-center justify-between">
                                <button onclick="openLightbox({{ $idx }})" class="text-xs font-black uppercase text-black hover:text-yellow-600 transition flex items-center gap-1">
                                    Perbesar 🔍
                                </button>
                                <a href="https://wa.me/{{ $waNumber }}?text={{ rawurlencode('Halo Admin Asri Boarding House, saya melihat foto ' . $sItem['judul'] . ' di galeri dan tertarik untuk menanyakan ketersediaan unit tersebut. Terima kasih.') }}" target="_blank" rel="noopener noreferrer" class="text-xs font-black uppercase text-green-600 hover:text-black transition flex items-center gap-1">
                                    Tanya WA 💬
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforelse
        </div>

        <!-- No Results Banner -->
        <div id="gallery-not-found" class="hidden text-center py-16 border-4 border-black bg-yellow-100 max-w-md mx-auto my-8 neo-card-shadow">
            <span class="text-5xl block mb-4">🔎</span>
            <h4 class="font-black text-black text-xl mb-2 uppercase tracking-tight">Foto Tidak Ditemukan</h4>
            <p class="text-black font-semibold text-xs px-6 leading-relaxed">
                Maaf, tidak ada dokumentasi foto yang cocok dengan pencarian Anda. Silakan gunakan kata kunci lain.
            </p>
        </div>

        <!-- CTA Section -->
        <x-landing.cta-whatsapp 
            title="Tertarik Menginap di Kost Asri? 🏡"
            subtitle="Semua fasilitas dan tipe kamar yang Anda lihat di atas siap memberikan kenyamanan terbaik. Jangan sampai kehabisan unit!"
            message="Halo Admin Asri Boarding House, setelah melihat-lihat galeri foto kost, saya tertarik untuk menanyakan ketersediaan unit kamar saat ini. Terima kasih."
            buttonText="Hubungi via WhatsApp 💬"
            :cleanWa="$waNumber"
        />
    </section>

</div>

<!-- Neo-Brutalism Lightbox Modal Container -->
<div id="gallery-lightbox" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-80 p-4 backdrop-blur-sm transition-opacity duration-300">
    <div class="relative bg-white border-4 border-black neo-card-shadow max-w-4xl w-full max-h-[95vh] flex flex-col justify-between overflow-hidden">
        
        <!-- Modal Header / Close Button -->
        <div class="absolute top-4 right-4 z-50">
            <button onclick="closeLightbox()" class="bg-yellow-400 hover:bg-black hover:text-yellow-400 text-black border-4 border-black w-10 h-10 flex items-center justify-center font-black text-xl neo-shadow-sm transition-all duration-150">
                ✕
            </button>
        </div>

        <!-- Lightbox Content -->
        <div class="flex flex-col md:flex-row h-full overflow-y-auto">
            <!-- Image Section -->
            <div class="w-full md:w-2/3 bg-gray-100 flex items-center justify-center border-b-4 md:border-b-0 md:border-r-4 border-black min-h-[300px] md:min-h-[450px] relative">
                <img id="lightbox-img" src="" alt="" class="w-full h-full object-contain max-h-[60vh] md:max-h-[80vh]">
                
                <!-- Navigation Arrows inside Image Box -->
                <button id="lightbox-prev-btn" onclick="prevLightbox()" class="absolute left-4 top-1/2 -translate-y-1/2 bg-white hover:bg-yellow-400 text-black border-4 border-black w-12 h-12 flex items-center justify-center font-black text-xl neo-shadow-sm transition-all duration-150">
                    ←
                </button>
                <button id="lightbox-next-btn" onclick="nextLightbox()" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white hover:bg-yellow-400 text-black border-4 border-black w-12 h-12 flex items-center justify-center font-black text-xl neo-shadow-sm transition-all duration-150">
                    →
                </button>
            </div>

            <!-- Details Section -->
            <div class="w-full md:w-1/3 p-6 flex flex-col justify-between bg-white">
                <div>
                    <span class="inline-block bg-yellow-400 text-black text-[10px] font-black px-2.5 py-1 border-2 border-black uppercase tracking-wider mb-4">
                        DOKUMENTASI FOTO 📸
                    </span>
                    <h3 id="lightbox-title" class="text-2xl font-black uppercase tracking-tight text-black border-b-4 border-black pb-2 mb-4">
                        Judul Foto
                    </h3>
                    <p id="lightbox-desc" class="text-gray-700 text-xs font-semibold leading-relaxed mb-6">
                        Deskripsi foto.
                    </p>
                </div>

                <div class="space-y-3 pt-6 border-t-4 border-black">
                    <a id="lightbox-wa-btn" href="#" target="_blank" rel="noopener noreferrer" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3.5 min-h-[44px] bg-green-500 hover:bg-black hover:text-green-500 text-white font-black uppercase tracking-wider text-xs border-4 border-black neo-btn-shadow neo-btn-interactive">
                        Tanya via WhatsApp 💬
                    </a>
                    <a id="lightbox-booking-btn" href="{{ route('landing.kamar') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3.5 min-h-[44px] bg-black hover:bg-yellow-400 hover:text-black text-white font-black uppercase tracking-wider text-xs border-4 border-black neo-btn-shadow neo-btn-interactive">
                        Pesan Kamar Sekarang →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function removeShimmer(img) {
        const placeholder = img.previousElementSibling;
        if (placeholder && placeholder.classList.contains('shimmer-placeholder')) {
            placeholder.remove();
        }
    }

    const galleryData = [
        @forelse($galleries as $gallery)
            {
                judul: @json($gallery->judul),
                deskripsi: @json($gallery->deskripsi ?? 'Dokumentasi visual lingkungan Kost Asri Boarding House.'),
                foto: @json(($gallery->foto && str_starts_with($gallery->foto, 'http')) ? $gallery->foto : asset('storage/' . $gallery->foto))
            },
        @empty
            {
                judul: "Kamar VIP Eksklusif",
                deskripsi: "Desain interior mewah dengan tempat tidur queen size, AC, smart TV, kamar mandi dalam, dan meja kerja modern.",
                foto: "https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=800&q=80"
            },
            {
                judul: "Kamar Deluxe Nyaman",
                deskripsi: "Kamar luas dengan pencahayaan alami yang baik, dilengkapi dengan AC, kamar mandi dalam, lemari pakaian, dan meja belajar.",
                foto: "https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80"
            },
            {
                judul: "Kamar Standar Fungsional",
                deskripsi: "Hunian kost yang praktis dan efisien dengan kasur single, AC, meja belajar, lemari, sirkulasi udara optimal, dan kamar mandi dalam.",
                foto: "https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=800&q=80"
            }
        @endforelse
    ];

    let currentActiveIndex = -1;
    let visibleIndices = [];

    function updateVisibleIndices() {
        visibleIndices = [];
        const items = document.querySelectorAll('.gallery-item');
        items.forEach((item, index) => {
            if (item.style.display !== 'none') {
                visibleIndices.push(index);
            }
        });
    }

    function filterGallery() {
        const query = document.getElementById('gallery-search').value.trim().toLowerCase();
        let totalVisible = 0;
        const items = document.querySelectorAll('.gallery-item');

        items.forEach(item => {
            const title = item.getAttribute('data-title') || '';
            const desc = item.getAttribute('data-desc') || '';

            if (!query || title.includes(query) || desc.includes(query)) {
                item.style.display = 'flex';
                totalVisible++;
            } else {
                item.style.display = 'none';
            }
        });

        const notFoundBanner = document.getElementById('gallery-not-found');
        if (totalVisible === 0) {
            notFoundBanner.classList.remove('hidden');
        } else {
            notFoundBanner.classList.add('hidden');
        }

        updateVisibleIndices();
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateVisibleIndices();
    });

    const adminWhatsappNumber = @json($waNumber);

    function openLightbox(index) {
        currentActiveIndex = index;
        const data = galleryData[index];
        if (!data) return;

        document.getElementById('lightbox-img').src = data.foto;
        document.getElementById('lightbox-img').alt = data.judul;
        document.getElementById('lightbox-title').innerText = data.judul;
        document.getElementById('lightbox-desc').innerText = data.deskripsi;

        const customWaMessage = `Halo Admin Asri Boarding House, saya melihat foto ${data.judul} di galeri dan tertarik untuk menanyakan ketersediaan unit atau detail fasilitas tersebut. Terima kasih.`;
        document.getElementById('lightbox-wa-btn').href = `https://wa.me/${adminWhatsappNumber}?text=${encodeURIComponent(customWaMessage)}`;

        const modal = document.getElementById('gallery-lightbox');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        const modal = document.getElementById('gallery-lightbox');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function nextLightbox() {
        if (visibleIndices.length <= 1) return;
        let position = visibleIndices.indexOf(currentActiveIndex);
        if (position === -1) {
            position = 0;
        } else {
            position = (position + 1) % visibleIndices.length;
        }
        openLightbox(visibleIndices[position]);
    }

    function prevLightbox() {
        if (visibleIndices.length <= 1) return;
        let position = visibleIndices.indexOf(currentActiveIndex);
        if (position === -1) {
            position = visibleIndices.length - 1;
        } else {
            position = (position - 1 + visibleIndices.length) % visibleIndices.length;
        }
        openLightbox(visibleIndices[position]);
    }

    document.getElementById('gallery-lightbox').addEventListener('click', function(e) {
        if (e.target === this) {
            closeLightbox();
        }
    });

    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('gallery-lightbox');
        if (modal && !modal.classList.contains('hidden')) {
            if (e.key === 'ArrowRight') {
                nextLightbox();
            } else if (e.key === 'ArrowLeft') {
                prevLightbox();
            } else if (e.key === 'Escape') {
                closeLightbox();
            }
        }
    });
</script>

<style>
    @keyframes bounce-slow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }
    .animate-bounce-slow {
        animation: bounce-slow 4s ease-in-out infinite;
    }
</style>
@endsection
