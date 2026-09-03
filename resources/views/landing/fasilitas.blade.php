@extends('layouts.landing')

@section('title', 'Fasilitas - ' . $logoText)

@section('content')
<div class="bg-white min-h-screen antialiased text-black">
    
    <!-- CONTENT -->
    <section class="max-w-6xl mx-auto px-6 py-20">
        <!-- Back Button -->
        <a href="{{ route('landing.index') }}" class="inline-flex items-center gap-2 bg-white text-black border-4 border-black px-4 py-2 font-black text-xs uppercase tracking-wider neo-btn-shadow neo-btn-interactive mb-10">
            ← Kembali ke Beranda
        </a>

        <div class="text-center mb-16">
            <h1 id="page-heading-fasilitas" class="text-4xl sm:text-6xl font-black uppercase tracking-tighter text-black inline-block border-b-4 border-black pb-2 bg-yellow-400 px-6 py-2 border-4 border-black neo-btn-shadow">
                FASILITAS KOST ASRI ⚡
            </h1>
            <p class="text-black font-bold mt-6 text-sm sm:text-base max-w-xl mx-auto">Kami menyediakan berbagai macam fasilitas eksklusif untuk menunjang aktivitas dan produktivitas harian Anda.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($fasilitas as $item)
                <x-landing.facility-card :facility="$item" />
            @empty
                <div class="border-4 border-black bg-yellow-100 p-6 text-center col-span-full font-black uppercase text-sm">
                    Belum ada fasilitas aktif yang tersedia.
                </div>
            @endforelse
        </div>
    </section>

</div>
@endsection
