@extends('layouts.landing')

@section('title', 'Cara Booking - ' . $logoText)

@section('content')
<div class="bg-white min-h-screen antialiased text-black">
    
    <!-- HERO SECTION -->
    <section class="relative bg-white border-b-4 border-black py-20 px-6 overflow-hidden">
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-yellow-400 border-4 border-black rotate-12 pointer-events-none opacity-40"></div>
        
        <div class="max-w-6xl mx-auto relative z-10">
            <!-- Back Button -->
            <a id="btn-back-to-home" href="{{ route('landing.index') }}" class="inline-flex items-center gap-2 bg-white text-black border-4 border-black px-4 py-3 min-h-[44px] font-black text-xs uppercase tracking-wider neo-btn-shadow neo-btn-interactive mb-10">
                ← Kembali ke Beranda
            </a>

            <div class="text-center">
                <span class="inline-block py-2 px-5 bg-black text-yellow-400 border-4 border-black text-xs font-black uppercase tracking-widest mb-8 neo-btn-shadow">
                    PANDUAN LENGKAP RESERVASI
                </span>
                
                <h1 id="page-heading-cara-booking" class="text-4xl sm:text-6xl font-black uppercase tracking-tighter text-black leading-none mb-8">
                    <span class="bg-yellow-400 border-4 border-black px-6 py-3 inline-block -rotate-1 my-2 neo-card-shadow">
                        CARA BOOKING KOST ⚡
                    </span>
                </h1>
                
                <p class="text-base sm:text-xl font-bold border-4 border-black bg-white p-6 neo-card-shadow max-w-3xl mx-auto mb-10 leading-relaxed text-black">
                    Ikuti 4 langkah mudah di bawah ini untuk memesan kamar kost impian Anda secara instan, aman, dan otomatis 24 jam non-stop.
                </p>
            </div>
        </div>
    </section>

    <!-- STEPPER SECTION -->
    <section class="max-w-6xl mx-auto px-6 py-20">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            
            <!-- Step 1 -->
            <div class="bg-white border-4 border-black p-6 neo-card-shadow flex flex-col justify-between items-start gap-4 transition-all hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[4px_4px_0px_0px_#000000] group">
                <div class="w-full">
                    <div class="flex justify-between items-center mb-4">
                        <div class="w-12 h-12 flex items-center justify-center border-4 border-black bg-yellow-400 text-black text-xl font-black shadow-[3px_3px_0px_0px_#000000]">
                            1
                        </div>
                        <div class="text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-black group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    
                    <h3 class="text-xl font-black uppercase tracking-tight text-black mb-2">Pilih &amp; Cek Kamar</h3>
                    <p class="text-xs font-bold leading-relaxed text-gray-700 mb-4">
                        Pilih kamar impian Anda dari katalog, lalu cek detail serta simulasi biaya sewa.
                    </p>
                    
                    <ul class="text-xs font-semibold text-black space-y-2 border-t-2 border-dashed border-gray-300 pt-3">
                        <li class="flex items-start gap-1.5"><span class="text-yellow-500">✔</span> Cek foto interior &amp; fasilitas kamar</li>
                        <li class="flex items-start gap-1.5"><span class="text-yellow-500">✔</span> Pilih durasi tinggal (harian/bulanan)</li>
                        <li class="flex items-start gap-1.5"><span class="text-yellow-500">✔</span> Lakukan simulasi perhitungan biaya</li>
                    </ul>
                </div>
                
                <div class="w-full mt-4">
                    <span class="inline-block bg-yellow-100 text-yellow-800 text-[10px] font-black uppercase tracking-wider px-2.5 py-1 border-2 border-black">
                        💡 Tips: Pilih tipe VIP untuk kenyamanan ekstra
                    </span>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="bg-white border-4 border-black p-6 neo-card-shadow flex flex-col justify-between items-start gap-4 transition-all hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[4px_4px_0px_0px_#000000] group">
                <div class="w-full">
                    <div class="flex justify-between items-center mb-4">
                        <div class="w-12 h-12 flex items-center justify-center border-4 border-black bg-yellow-400 text-black text-xl font-black shadow-[3px_3px_0px_0px_#000000]">
                            2
                        </div>
                        <div class="text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-black group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    
                    <h3 class="text-xl font-black uppercase tracking-tight text-black mb-2">Isi Data Diri</h3>
                    <p class="text-xs font-bold leading-relaxed text-gray-700 mb-4">
                        Daftarkan akun dan lengkapi data profil penyewa baru dengan aman.
                    </p>
                    
                    <ul class="text-xs font-semibold text-black space-y-2 border-t-2 border-dashed border-gray-300 pt-3">
                        <li class="flex items-start gap-1.5"><span class="text-yellow-500">✔</span> Daftar dengan Email / Google Auth</li>
                        <li class="flex items-start gap-1.5"><span class="text-yellow-500">✔</span> Masukkan No. WhatsApp yang aktif</li>
                        <li class="flex items-start gap-1.5"><span class="text-yellow-500">✔</span> Lengkapi kontak wali darurat Anda</li>
                    </ul>
                </div>
                
                <div class="w-full mt-4">
                    <span class="inline-block bg-yellow-100 text-yellow-800 text-[10px] font-black uppercase tracking-wider px-2.5 py-1 border-2 border-black">
                        🔒 Data pribadi Anda dienkripsi aman
                    </span>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="bg-white border-4 border-black p-6 neo-card-shadow flex flex-col justify-between items-start gap-4 transition-all hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[4px_4px_0px_0px_#000000] group">
                <div class="w-full">
                    <div class="flex justify-between items-center mb-4">
                        <div class="w-12 h-12 flex items-center justify-center border-4 border-black bg-yellow-400 text-black text-xl font-black shadow-[3px_3px_0px_0px_#000000]">
                            3
                        </div>
                        <div class="text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-black group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    
                    <h3 class="text-xl font-black uppercase tracking-tight text-black mb-2">Bayar Instan</h3>
                    <p class="text-xs font-bold leading-relaxed text-gray-700 mb-4">
                        Selesaikan pembayaran DP (uang muka) atau bayar lunas langsung secara online.
                    </p>
                    
                    <ul class="text-xs font-semibold text-black space-y-2 border-t-2 border-dashed border-gray-300 pt-3">
                        <li class="flex items-start gap-1.5"><span class="text-yellow-500">✔</span> Pembayaran aman via Midtrans Snap</li>
                        <li class="flex items-start gap-1.5"><span class="text-yellow-500">✔</span> Transfer Virtual Account / E-Wallet / QRIS</li>
                        <li class="flex items-start gap-1.5"><span class="text-yellow-500">✔</span> Verifikasi otomatis sistem real-time</li>
                    </ul>
                </div>
                
                <div class="w-full mt-4">
                    <span class="inline-block bg-yellow-100 text-yellow-800 text-[10px] font-black uppercase tracking-wider px-2.5 py-1 border-2 border-black">
                        ⚡ Bayar lunas / DP minimal 30%
                    </span>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="bg-white border-4 border-black p-6 neo-card-shadow flex flex-col justify-between items-start gap-4 transition-all hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[4px_4px_0px_0px_#000000] group">
                <div class="w-full">
                    <div class="flex justify-between items-center mb-4">
                        <div class="w-12 h-12 flex items-center justify-center border-4 border-black bg-yellow-400 text-black text-xl font-black shadow-[3px_3px_0px_0px_#000000]">
                            4
                        </div>
                        <div class="text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-black group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                    </div>
                    
                    <h3 class="text-xl font-black uppercase tracking-tight text-black mb-2">Terima Nota</h3>
                    <p class="text-xs font-bold leading-relaxed text-gray-700 mb-4">
                        Dapatkan invoice resmi PDF dan Kode Akses Kamar digital Anda untuk check-in.
                    </p>
                    
                    <ul class="text-xs font-semibold text-black space-y-2 border-t-2 border-dashed border-gray-300 pt-3">
                        <li class="flex items-start gap-1.5"><span class="text-yellow-500">✔</span> Nota dikirim otomatis ke WA &amp; Email</li>
                        <li class="flex items-start gap-1.5"><span class="text-yellow-500">✔</span> Kode akses kamar dikirim via digital</li>
                        <li class="flex items-start gap-1.5"><span class="text-yellow-500">✔</span> Siap check-in mandiri di tanggal sewa</li>
                    </ul>
                </div>
                
                <div class="w-full mt-4">
                    <span class="inline-block bg-yellow-100 text-yellow-800 text-[10px] font-black uppercase tracking-wider px-2.5 py-1 border-2 border-black">
                        🔑 Kode akses aktif saat masa sewa mulai
                    </span>
                </div>
            </div>
            
        </div>
    </section>

    <!-- BOOKING FAQ ACCORDION SECTION -->
    <section class="bg-gray-50 border-t-4 border-b-4 border-black py-20 px-6">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-16">
                <span class="inline-block py-1 px-3 bg-black text-yellow-400 border-2 border-black text-[10px] font-black uppercase tracking-widest mb-4">
                    TANYA JAWAB BOOKING
                </span>
                <h2 class="text-3xl sm:text-5xl font-black uppercase tracking-tight text-black">
                    Hal Penting Seputar Pemesanan ❓
                </h2>
                <p class="text-xs sm:text-sm font-semibold text-gray-700 mt-4 max-w-xl mx-auto">
                    Ketahui informasi dasar ini sebelum melakukan pemesanan agar transaksi Anda berjalan dengan lancar dan aman.
                </p>
            </div>

            <!-- Accordion List using AlpineJS -->
            <div x-data="{ active: null }" class="space-y-6">
                <x-landing.faq-item :index="1" pertanyaan="Bagaimana cara pembayaran DP atau Pelunasan?" jawaban="Pembayaran dapat diselesaikan langsung secara online menggunakan gateway pembayaran Midtrans Snap. Anda bisa memilih metode Transfer Bank (Virtual Account Mandiri, BCA, BNI, BRI), E-Wallet (GoPay, ShopeePay), QRIS, atau Kartu Kredit. Pembayaran Anda akan otomatis terverifikasi oleh sistem secara real-time tanpa perlu konfirmasi manual atau mengirimkan struk transfer." />
                <x-landing.faq-item :index="2" pertanyaan="Berapa lama batas waktu pembayaran setelah mengisi formulir?" jawaban="Setelah Anda menekan tombol 'Pesan' dan mengisi form data diri, Anda diberikan batas waktu maksimal 24 jam untuk menyelesaikan pembayaran DP (minimal 30%) atau pembayaran lunas. Jika dalam batas waktu tersebut pembayaran belum diterima, sistem kami akan membatalkan pemesanan Anda secara otomatis guna melepaskan kuota kamar kembali ke katalog." />
                <x-landing.faq-item :index="3" pertanyaan="Apakah saya bisa melakukan pembatalan booking?" jawaban="Ya, Anda dapat mengajukan pembatalan reservasi melalui portal/dashboard penyewa sebelum check-in dilakukan. Harap diperhatikan bahwa sesuai kebijakan operasional Kost Asri, pembayaran Uang Muka (DP) yang telah dibayarkan bersifat non-refundable (tidak dapat dikembalikan dengan alasan apapun) untuk kompensasi penutupan sewa kamar dari publik." />
                <x-landing.faq-item :index="4" pertanyaan="Bagaimana cara mendapatkan Kode Akses Kamar digital?" jawaban="Setelah status pemesanan Anda dinyatakan sukses (baik melalui status pembayaran DP terverifikasi atau Lunas), sistem akan secara otomatis menerbitkan Nota Pembayaran PDF resmi beserta Kode Akses Kamar digital Anda. Informasi ini dikirimkan via WhatsApp resmi serta email Anda. Kode akses digital tersebut dapat Anda gunakan untuk masuk ke dalam area kost pada hari pertama masa sewa dimulai." />
            </div>
        </div>
    </section>

    <!-- CTA SECTION -->
    <x-landing.cta-whatsapp 
        title="SIAP MEMESAN KAMAR KOST IMPIAN?" 
        subtitle="Kamar kost premium di Semarang dengan fasilitas lengkap dan keamanan 24 jam sangat diminati. Pesan kamar pilihan Anda sekarang sebelum kehabisan slot kuota!"
        message="Halo Admin Asri Boarding House, saya sedang membaca panduan cara booking dan ingin menanyakan beberapa detail mengenai proses reservasi kamar. Terima kasih."
        buttonText="Tanya Admin via WhatsApp 💬"
        :cleanWa="$waNumber"
    />

</div>
@endsection
