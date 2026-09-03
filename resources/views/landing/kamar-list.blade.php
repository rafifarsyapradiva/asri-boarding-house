@extends('layouts.landing')

@section('title', 'Katalog Kamar - ' . $logoText)

@section('content')
<div class="bg-white min-h-screen antialiased text-black">
    
    <!-- CONTENT -->
    <section class="max-w-6xl mx-auto px-6 py-20">
        <!-- Back Button -->
        <a href="{{ route('landing.index') }}" class="inline-flex items-center gap-2 bg-white text-black border-4 border-black px-4 py-2 font-black text-xs uppercase tracking-wider neo-btn-shadow neo-btn-interactive mb-10">
            ← Kembali ke Beranda
        </a>

        <div class="text-center mb-16">
            <h1 id="page-heading-kamar-list" class="text-4xl sm:text-6xl font-black uppercase tracking-tighter text-black inline-block border-b-4 border-black pb-2 bg-yellow-400 px-6 py-2 border-4 border-black neo-btn-shadow">
                KATALOG UNIT KAMAR 🏠
            </h1>
            <p class="text-black font-bold mt-6 text-sm sm:text-base max-w-xl mx-auto">Cari dan pilih tipe kamar kost impian Anda sesuai dengan keinginan dan kebutuhan harian.</p>
        </div>

        <!-- Interactive Category Quick Filter Bar -->
        <div class="bg-white border-4 border-black p-6 neo-card-shadow mb-16 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <button type="button" onclick="filterKategori('all')" id="btn-cat-all" class="filter-btn px-5 py-3 border-4 border-black {{ request('tipe_kamar', 'all') === 'all' ? 'bg-yellow-400 text-black neo-shadow-sm' : 'bg-white text-black hover:bg-yellow-100' }} font-black uppercase tracking-wider text-xs transition-all duration-200">
                    Semua Tipe
                </button>
                <button type="button" onclick="filterKategori('vip')" id="btn-cat-vip" class="filter-btn px-5 py-3 border-4 border-black {{ request('tipe_kamar') === 'vip' ? 'bg-yellow-400 text-black neo-shadow-sm' : 'bg-white text-black hover:bg-yellow-100' }} font-black uppercase tracking-wider text-xs transition-all duration-200">
                    👑 VIP
                </button>
                <button type="button" onclick="filterKategori('deluxe')" id="btn-cat-deluxe" class="filter-btn px-5 py-3 border-4 border-black {{ request('tipe_kamar') === 'deluxe' ? 'bg-yellow-400 text-black neo-shadow-sm' : 'bg-white text-black hover:bg-yellow-100' }} font-black uppercase tracking-wider text-xs transition-all duration-200">
                    ✨ Deluxe
                </button>
                <button type="button" onclick="filterKategori('standar')" id="btn-cat-standar" class="filter-btn px-5 py-3 border-4 border-black {{ request('tipe_kamar') === 'standar' ? 'bg-yellow-400 text-black neo-shadow-sm' : 'bg-white text-black hover:bg-yellow-100' }} font-black uppercase tracking-wider text-xs transition-all duration-200">
                    🏠 Standar
                </button>
            </div>
            
            <div class="relative w-full md:w-80">
                <input type="text" id="kamar-search-input" oninput="jalankanFilter()" value="{{ request('q') }}" placeholder="Cari nomor kamar..."
                       class="w-full border-4 border-black bg-white px-4 py-3 font-bold text-sm focus:outline-none focus:ring-4 focus:ring-yellow-400 focus:bg-yellow-50 text-black placeholder-gray-500">
            </div>
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

        <!-- Server-side Pagination Links -->
        @if($kamarList->hasPages())
            <div class="mt-16 bg-white border-4 border-black p-6 neo-card-shadow flex justify-center">
                {{ $kamarList->links() }}
            </div>
        @endif

        <!-- Room Not Found Banner -->
        <div id="kamar-not-found" class="{{ $kamarList->isEmpty() ? '' : 'hidden' }} text-center py-16 border-4 border-black bg-yellow-100 max-w-md mx-auto my-8 neo-card-shadow">
            <span class="text-5xl block mb-4">🔎</span>
            <h4 class="font-black text-black text-xl mb-2 uppercase tracking-tight">Kamar Tidak Ditemukan</h4>
            <p class="text-black font-semibold text-xs px-6 leading-relaxed">
                Maaf, tidak ada kamar kost yang cocok dengan pencarian atau filter kategori Anda. Silakan coba kata kunci lain.
            </p>
        </div>
    </section>

</div>

<script>
    function filterKategori(kategori) {
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('tipe_kamar', kategori);
        urlParams.set('page', 1);
        window.location.search = urlParams.toString();
    }

    let searchTimeout = null;
    function jalankanFilter() {
        if (searchTimeout) clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const query = document.getElementById('kamar-search-input').value.trim();
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('q', query);
            urlParams.set('page', 1);
            window.location.search = urlParams.toString();
        }, 400);
    }
</script>
@endsection
