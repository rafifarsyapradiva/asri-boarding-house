@props([
    'title' => 'Tunggu Apa Lagi? Gabung Bersama Kami!',
    'subtitle' => 'Dapatkan pengalaman tinggal terbaik yang mendukung fokus belajar dan kerja Anda dengan proses pemesanan digital cepat.',
    'message' => 'Halo Admin Asri Boarding House, saya ingin menanyakan informasi lebih lanjut mengenai hunian di Kost Asri. Terima kasih.',
    'buttonText' => 'Hubungi via WhatsApp 💬',
    'waNumber' => null,
])

@php
    $targetWa = $waNumber ?? config('reservasi.admin_wa', '');
    $encodedMsg = rawurlencode($message);
@endphp

<div class="mt-20 border-4 border-black bg-yellow-400 p-8 sm:p-12 neo-card-shadow text-center max-w-4xl mx-auto">
    <h3 class="text-2xl sm:text-4xl font-black uppercase tracking-tight text-black mb-6">{{ $title }}</h3>
    <p class="text-black font-bold text-sm sm:text-base max-w-2xl mx-auto mb-8">{{ $subtitle }}</p>
    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
        @if(Route::has('landing.kamar'))
            <a href="{{ route('landing.kamar') }}" class="w-full sm:w-auto px-8 py-4 bg-black text-white border-4 border-black font-black uppercase tracking-wider text-sm neo-btn-shadow neo-btn-interactive text-center">
                Cari & Booking Kamar 🚀
            </a>
        @endif
        
        @if($targetWa)
            <a href="https://wa.me/{{ $targetWa }}?text={{ $encodedMsg }}" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto px-8 py-4 bg-white text-black border-4 border-black font-black uppercase tracking-wider text-sm neo-btn-shadow neo-btn-interactive text-center flex items-center justify-center gap-2">
                {{ $buttonText }}
            </a>
        @endif
    </div>
</div>
