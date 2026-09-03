@extends('layouts.landing')

@section('title', 'Tanya Jawab (FAQ) - ' . $logoText)

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
                    FAQ - TANYA JAWAB
                </span>
                
                <h1 id="page-heading-faq" class="text-4xl sm:text-6xl font-black uppercase tracking-tighter text-black leading-none mb-8">
                    <span class="bg-yellow-400 border-4 border-black px-6 py-3 inline-block -rotate-1 my-2 neo-card-shadow">
                        Pusat Bantuan FAQ ⚡
                    </span>
                </h1>
                
                <p class="text-base sm:text-xl font-bold border-4 border-black bg-white p-6 neo-card-shadow max-w-3xl mx-auto mb-10 leading-relaxed text-black">
                    Berikut adalah kumpulan pertanyaan yang paling sering diajukan mengenai fasilitas, proses booking, pembayaran, serta aturan tinggal di Asri Boarding House. Klik pada pertanyaan untuk membuka detail jawaban.
                </p>
            </div>
        </div>
    </section>

    <!-- CONTENT: FAQ List Section -->
    <section class="max-w-4xl mx-auto px-6 py-20">
        @if($faqs->isEmpty())
            <div class="text-center py-16 border-4 border-black bg-yellow-50 max-w-md mx-auto my-8 neo-card-shadow">
                <span class="text-5xl block mb-4">📭</span>
                <p class="text-black font-black uppercase text-sm">Saat ini belum ada data FAQ yang dipublikasikan.</p>
            </div>
        @else
            <!-- Search FAQ Bar using AlpineJS -->
            <div class="mb-12" x-data="{
                search: '',
                filterFaqs() {
                    let q = this.search.toLowerCase().trim();
                    let visibleCount = 0;
                    document.querySelectorAll('.faq-item').forEach(el => {
                        let text = el.getAttribute('data-search') || '';
                        if (!q || text.includes(q)) {
                            el.style.display = 'block';
                            visibleCount++;
                        } else {
                            el.style.display = 'none';
                        }
                    });
                    let notFound = document.getElementById('faq-not-found');
                    if (notFound) {
                        if (visibleCount === 0) {
                            notFound.classList.remove('hidden');
                        } else {
                            notFound.classList.add('hidden');
                        }
                    }
                }
            }">
                <div class="bg-white border-4 border-black p-4 neo-card-shadow flex items-center">
                    <span class="mr-3 text-xl">🔍</span>
                    <input type="text" 
                           x-model="search"
                           @input="filterFaqs()"
                           placeholder="Cari pertanyaan atau kata kunci bantuan..." 
                           class="w-full bg-transparent border-none focus:outline-none focus:ring-0 text-sm font-bold text-black placeholder-gray-500">
                </div>
            </div>

            <!-- Accordion List -->
            <div x-data="{ active: null }" class="space-y-6">
                @foreach($faqs as $index => $faq)
                    <x-landing.faq-item :index="$index" :pertanyaan="$faq->pertanyaan" :jawaban="$faq->jawaban" />
                @endforeach

                <!-- FAQ Not Found Banner -->
                <div id="faq-not-found" class="hidden text-center py-16 border-4 border-black bg-yellow-100 max-w-md mx-auto my-8 neo-card-shadow">
                    <span class="text-5xl block mb-4">🔎</span>
                    <h4 class="font-black text-black text-xl mb-2 uppercase tracking-tight">Pertanyaan Tidak Ditemukan</h4>
                    <p class="text-black font-semibold text-xs px-6 leading-relaxed">
                        Maaf, tidak ada FAQ yang cocok dengan pencarian kata kunci Anda. Silakan coba kata kunci lain atau klik tombol hubungi admin di bawah.
                    </p>
                </div>
            </div>
        @endif

        <!-- CTA Section -->
        <x-landing.cta-whatsapp 
            title="Masih Punya Pertanyaan Lain?"
            subtitle="Tim Customer Service kami siap melayani Anda 24 jam untuk menjawab segala pertanyaan seputar Kost Asri."
            message="Halo Admin Asri Boarding House, saya memiliki pertanyaan khusus mengenai ketentuan tinggal yang belum tercantum di halaman FAQ. Mohon informasinya. Terima kasih."
            buttonText="Hubungi Admin via WhatsApp 💬"
            :cleanWa="$waNumber"
        />
    </section>

</div>
@endsection
