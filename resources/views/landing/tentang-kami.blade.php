@extends('layouts.landing')

@section('title', 'Tentang Kami - ' . $logoText)

@section('content')
<div class="bg-white min-h-screen antialiased text-black">
    

    <!-- CONTENT: Hero Section -->
    <section class="relative bg-white border-b-4 border-black py-20 px-6 overflow-hidden">
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-yellow-400 border-4 border-black rotate-12 pointer-events-none opacity-40"></div>
        
        <div class="max-w-6xl mx-auto relative z-10">
            <!-- Back Button -->
            <a href="{{ route('landing.index') }}" class="inline-flex items-center gap-2 bg-white text-black border-4 border-black px-4 py-3 min-h-[44px] font-black text-xs uppercase tracking-wider neo-btn-shadow neo-btn-interactive mb-10">
                ← Kembali ke Beranda
            </a>

            <div class="text-center">
                <span class="inline-block py-2 px-5 bg-black text-yellow-400 border-4 border-black text-xs font-black uppercase tracking-widest mb-8 neo-btn-shadow">
                    PROFIL KOST ASRI
                </span>
                
                <h1 id="page-heading-tentang-kami" class="text-4xl sm:text-6xl font-black uppercase tracking-tighter text-black leading-none mb-8">
                    <span class="bg-yellow-400 border-4 border-black px-6 py-3 inline-block -rotate-1 my-2 neo-card-shadow">
                        {{ strtoupper($aboutTitle) }} ⚡
                    </span>
                </h1>
                
                <p class="text-base sm:text-xl font-bold border-4 border-black bg-white p-6 neo-card-shadow max-w-3xl mx-auto mb-10 leading-relaxed text-black text-left">
                    {{ $aboutDescription }}
                </p>
            </div>
        </div>
    </section>

    <!-- CONTENT: Visi & Misi Section -->
    <section class="max-w-6xl mx-auto px-6 py-20 border-b-4 border-black">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <!-- Visi Card -->
            <div class="bg-white border-4 border-black p-8 neo-card-shadow relative overflow-hidden transition-all duration-200 hover:translate-x-[2px] hover:translate-y-[2px]">
                <div class="w-12 h-12 border-4 border-black bg-yellow-400 flex items-center justify-center mb-6 text-2xl shadow-[3px_3px_0px_0px_#000000] font-black">👁️</div>
                <h2 class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-black mb-4">VISI KAMI</h2>
                <p class="text-gray-800 text-sm sm:text-base leading-relaxed font-semibold">
                    {{ $aboutVisi }}
                </p>
            </div>

            <!-- Misi Card -->
            <div class="bg-white border-4 border-black p-8 neo-card-shadow relative overflow-hidden transition-all duration-200 hover:translate-x-[2px] hover:translate-y-[2px]">
                <div class="w-12 h-12 border-4 border-black bg-yellow-400 flex items-center justify-center mb-6 text-2xl shadow-[3px_3px_0px_0px_#000000] font-black">🚀</div>
                <h2 class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-black mb-4">MISI KAMI</h2>
                <ul class="space-y-3.5 text-gray-800 text-sm sm:text-base font-semibold">
                    <li class="flex items-start gap-2.5">
                        <span class="text-yellow-500 font-black">⚡</span>
                        <span>{{ $aboutMisi1 }}</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="text-yellow-500 font-black">⚡</span>
                        <span>{{ $aboutMisi2 }}</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="text-yellow-500 font-black">⚡</span>
                        <span>{{ $aboutMisi3 }}</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="text-yellow-500 font-black">⚡</span>
                        <span>{{ $aboutMisi4 }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <!-- CONTENT: Keunggulan Section -->
    <section class="max-w-6xl mx-auto px-6 py-20 border-b-4 border-black">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-5xl font-black uppercase tracking-tight inline-block border-b-4 border-black pb-2 bg-yellow-400 px-6 py-2 border-4 border-black neo-btn-shadow">
                MENGAPA MEMILIH KAMI? 🌟
            </h2>
            <p class="text-black font-bold mt-6 text-sm sm:text-base">Keunggulan utama Asri Boarding House dibanding kos-kosan konvensional lainnya.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Item 1 -->
            <div class="bg-white p-6 border-4 border-black neo-card-shadow flex flex-col items-center text-center transition-all duration-200 hover:translate-x-[2px] hover:translate-y-[2px]">
                <div class="w-14 h-14 border-4 border-black bg-yellow-400 flex items-center justify-center mb-6 text-2xl shadow-[3px_3px_0px_0px_#000000]">📍</div>
                <h3 class="font-black text-black uppercase tracking-tight text-lg mb-3">Lokasi Strategis</h3>
                <p class="text-gray-800 text-xs leading-relaxed font-bold">Terletak sangat dekat dengan kawasan kampus Tembalang, pusat kuliner, dan fasilitas umum.</p>
            </div>

            <!-- Item 2 -->
            <div class="bg-white p-6 border-4 border-black neo-card-shadow flex flex-col items-center text-center transition-all duration-200 hover:translate-x-[2px] hover:translate-y-[2px]">
                <div class="w-14 h-14 border-4 border-black bg-yellow-400 flex items-center justify-center mb-6 text-2xl shadow-[3px_3px_0px_0px_#000000]">📶</div>
                <h3 class="font-black text-black uppercase tracking-tight text-lg mb-3">Koneksi Wi-Fi Cepat</h3>
                <p class="text-gray-800 text-xs leading-relaxed font-bold">Dilengkapi Wi-Fi serat optik kecepatan tinggi untuk menunjang aktivitas belajar & bekerja jarak jauh.</p>
            </div>

            <!-- Item 3 -->
            <div class="bg-white p-6 border-4 border-black neo-card-shadow flex flex-col items-center text-center transition-all duration-200 hover:translate-x-[2px] hover:translate-y-[2px]">
                <div class="w-14 h-14 border-4 border-black bg-yellow-400 flex items-center justify-center mb-6 text-2xl shadow-[3px_3px_0px_0px_#000000]">🔒</div>
                <h3 class="font-black text-black uppercase tracking-tight text-lg mb-3">Keamanan CCTV 24/7</h3>
                <p class="text-gray-800 text-xs leading-relaxed font-bold">Keamanan maksimal dengan pantauan CCTV di area publik kost dan pintu gerbang otomatis.</p>
            </div>

            <!-- Item 4 -->
            <div class="bg-white p-6 border-4 border-black neo-card-shadow flex flex-col items-center text-center transition-all duration-200 hover:translate-x-[2px] hover:translate-y-[2px]">
                <div class="w-14 h-14 border-4 border-black bg-yellow-400 flex items-center justify-center mb-6 text-2xl shadow-[3px_3px_0px_0px_#000000]">💳</div>
                <h3 class="font-black text-black uppercase tracking-tight text-lg mb-3">Pembayaran Digital</h3>
                <p class="text-gray-800 text-xs leading-relaxed font-bold">Pembayaran mudah, aman, dan instan menggunakan kartu kredit, e-wallet, atau bank transfer via Midtrans.</p>
            </div>
        </div>
    </section>

    <!-- CONTENT: Pemilik Kost Section -->
    <section class="max-w-6xl mx-auto px-6 py-20 border-b-4 border-black">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-5xl font-black uppercase tracking-tight inline-block border-b-4 border-black pb-2">
                PEMILIK KOST 🤝
            </h2>
            <p class="text-black font-bold mt-4 text-sm sm:text-base">Siap melayani kebutuhan dan memberikan kenyamanan tinggal terbaik bagi Anda.</p>
        </div>

        <div class="flex justify-center">
            <!-- Member 1 -->
            <div class="bg-white p-6 border-4 border-black neo-card-shadow flex flex-col items-center text-center transition-all duration-200 hover:translate-x-[2px] hover:translate-y-[2px] max-w-md w-full">
                <div class="w-24 h-24 border-4 border-black bg-yellow-400 flex items-center justify-center mb-6 text-4xl shadow-[4px_4px_0px_0px_#000000] font-black">👨‍💼</div>
                <h3 class="font-black text-black uppercase tracking-tight text-lg mb-1">Pak Asep (48 Tahun)</h3>
                <span class="text-[10px] font-black uppercase bg-black text-white px-2 py-0.5 border border-black mb-3">Owner & Founder</span>
                <p class="text-gray-800 text-xs leading-relaxed font-bold mb-4">Menggagas konsep Kost Asri dengan visi hunian yang mengutamakan ketenteraman dan kenyamanan kekeluargaan.</p>
                <div class="border-t-2 border-black pt-3 w-full text-left text-xs font-bold text-gray-700">
                    <span class="block text-black font-black uppercase mb-1">📍 Lokasi Kost:</span>
                    {{ $contactAddress }}
                </div>
            </div>
        </div>
    </section>

    <!-- CONTENT: Gallery Section -->
    <section class="max-w-6xl mx-auto px-6 py-20">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-5xl font-black uppercase tracking-tight inline-block border-b-4 border-black pb-2 bg-yellow-400 px-6 py-2 border-4 border-black neo-btn-shadow">
                GALERI HUNIAN KAMI 📸
            </h2>
            <p class="text-black font-bold mt-6 text-sm sm:text-base">Melihat lebih dekat lingkungan dan suasana tipe kamar di Asri Boarding House.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Galeri 1: VIP -->
            <div class="border-4 border-black neo-card-shadow overflow-hidden bg-white group hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[4px_4px_0px_0px_#000000] transition-all duration-200">
                <div class="h-64 bg-yellow-50 relative overflow-hidden">
                    <div class="absolute inset-0 bg-yellow-100 animate-pulse pointer-events-none shimmer-placeholder"></div>
                    <img src="https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=600&q=80" alt="Kamar VIP" loading="lazy" onload="removeShimmer(this)" onerror="removeShimmer(this)" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                    <span class="absolute top-4 left-4 bg-black text-white text-[10px] font-black px-3 py-1.5 border-2 border-white uppercase tracking-widest">Kamar VIP</span>
                </div>
                <div class="p-5 border-t-4 border-black">
                    <h4 class="font-black text-lg text-black uppercase mb-1">Hunian Tipe VIP</h4>
                    <p class="text-xs text-gray-800 font-semibold leading-relaxed">Kamar terluas dengan fasilitas termewah, kamar mandi dalam premium, smart TV, AC, kulkas, dan spring bed berukuran besar.</p>
                </div>
            </div>

            <!-- Galeri 2: Deluxe -->
            <div class="border-4 border-black neo-card-shadow overflow-hidden bg-white group hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[4px_4px_0px_0px_#000000] transition-all duration-200">
                <div class="h-64 bg-yellow-50 relative overflow-hidden">
                    <div class="absolute inset-0 bg-yellow-100 animate-pulse pointer-events-none shimmer-placeholder"></div>
                    <img src="https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=600&q=80" alt="Kamar Deluxe" loading="lazy" onload="removeShimmer(this)" onerror="removeShimmer(this)" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                    <span class="absolute top-4 left-4 bg-black text-white text-[10px] font-black px-3 py-1.5 border-2 border-white uppercase tracking-widest">Kamar Deluxe</span>
                </div>
                <div class="p-5 border-t-4 border-black">
                    <h4 class="font-black text-lg text-black uppercase mb-1">Hunian Tipe Deluxe</h4>
                    <p class="text-xs text-gray-800 font-semibold leading-relaxed">Kamar premium yang seimbang dan nyaman dilengkapi AC, kamar mandi dalam modern, meja kerja rapi, serta lemari pakaian fungsional.</p>
                </div>
            </div>

            <!-- Galeri 3: Standar -->
            <div class="border-4 border-black neo-card-shadow overflow-hidden bg-white group hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[4px_4px_0px_0px_#000000] transition-all duration-200">
                <div class="h-64 bg-yellow-50 relative overflow-hidden">
                    <div class="absolute inset-0 bg-yellow-100 animate-pulse pointer-events-none shimmer-placeholder"></div>
                    <img src="https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=600&q=80" alt="Kamar Standar" loading="lazy" onload="removeShimmer(this)" onerror="removeShimmer(this)" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                    <span class="absolute top-4 left-4 bg-black text-white text-[10px] font-black px-3 py-1.5 border-2 border-white uppercase tracking-widest">Kamar Standar</span>
                </div>
                <div class="p-5 border-t-4 border-black">
                    <h4 class="font-black text-lg text-black uppercase mb-1">Hunian Tipe Standar</h4>
                    <p class="text-xs text-gray-800 font-semibold leading-relaxed">Pilihan ideal ekonomis yang bersih, asri, dan rapi. Dilengkapi perabotan dasar esensial lengkap, kipas angin, serta kamar mandi yang terawat.</p>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <x-landing.cta-whatsapp 
            title="Tunggu Apa Lagi? Gabung Bersama Kami!"
            subtitle="Dapatkan pengalaman tinggal terbaik yang mendukung fokus belajar dan kerja Anda dengan proses pemesanan digital cepat."
            message="Halo Admin Asri Boarding House, saya ingin menanyakan informasi lebih lanjut mengenai profil dan layanan hunian di Kost Asri. Terima kasih."
            buttonText="Tanya via WhatsApp 💬"
            :cleanWa="$waNumber"
        />
    </section>

</div>

<script>
    function removeShimmer(img) {
        const placeholder = img.previousElementSibling;
        if (placeholder && placeholder.classList.contains('shimmer-placeholder')) {
            placeholder.remove();
        }
    }
</script>
@endsection
