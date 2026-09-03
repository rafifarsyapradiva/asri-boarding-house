@extends('layouts.landing')

@section('title', 'Testimoni - ' . $logoText)

@section('content')
<div class="bg-white min-h-screen antialiased text-black">
    
    <!-- CONTENT -->
    <section class="max-w-6xl mx-auto px-6 py-20">
        <!-- Back Button -->
        <a href="{{ route('landing.index') }}" class="inline-flex items-center gap-2 bg-white text-black border-4 border-black px-4 py-3 min-h-[44px] font-black text-xs uppercase tracking-wider neo-btn-shadow neo-btn-interactive mb-10">
            ← Kembali ke Beranda
        </a>

        <div class="text-center mb-16">
            <h1 id="page-heading-testimoni" class="text-4xl sm:text-6xl font-black uppercase tracking-tighter text-black inline-block border-b-4 border-black pb-2 bg-yellow-400 px-6 py-2 border-4 border-black neo-btn-shadow">
                TESTIMONI PENYEWA KOST 💬
            </h1>
            <p class="text-black font-bold mt-6 text-sm sm:text-base max-w-xl mx-auto">Ulasan asli dari rekan-rekan mahasiswa dan pekerja perantau yang tinggal di Asri Boarding House.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse($reviews as $review)
                <x-landing.review-card :review="$review" />
            @empty
                <div class="text-center py-10 bg-yellow-50 border-4 border-black col-span-full max-w-md mx-auto my-4 neo-card-shadow">
                    <span class="text-4xl block mb-3">💬</span>
                    <p class="text-black font-black uppercase text-xs">Belum ada review pelanggan yang ditampilkan.</p>
                </div>
            @endforelse
        </div>
    </section>

</div>
@endsection
