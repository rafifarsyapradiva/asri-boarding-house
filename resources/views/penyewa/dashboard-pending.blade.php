<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
                {{ __('Dashboard Reservasi') }}
            </h2>
            <a href="{{ route('landing.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 border-2 border-black bg-white dark:bg-slate-800 text-black dark:text-white text-xs font-bold uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] hover:translate-y-[-1px] hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[3px_3px_0px_0px_rgba(255,255,255,1)] transition-all shrink-0">
                &larr; Kembali ke Beranda
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-screen text-black dark:text-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Welcome Banner Card -->
            <div class="bg-cyan-300 dark:bg-cyan-800 border-4 border-black dark:border-white p-6 md:p-8 text-black dark:text-white shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)]">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <span class="text-4xl bg-white dark:bg-slate-900 border-2 border-black dark:border-white p-2 shrink-0">👋</span>
                        <div>
                            <h3 class="text-xl md:text-2xl font-black uppercase tracking-tight">Selamat Datang, {{ $user->nama }}!</h3>
                            @if($latestReservasi)
                                <p class="text-xs md:text-sm font-bold mt-1 text-slate-800 dark:text-slate-200">Terima kasih telah memilih Asri Boarding House. Silakan selesaikan pembayaran reservasi Anda untuk dapat menempati kamar.</p>
                            @else
                                <p class="text-xs md:text-sm font-bold mt-1 text-slate-800 dark:text-slate-200">Terima kasih telah bergabung dengan Asri Boarding House. Cari kamar impian Anda untuk mulai menyewa.</p>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('landing.index') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 border-4 border-black dark:border-white bg-white dark:bg-slate-900 text-black dark:text-white text-xs font-black uppercase tracking-wider shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)] transition-all shrink-0 text-center font-bold">
                        &larr; Kembali ke Beranda
                    </a>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left: Reservation Detail Card (Col-span 2) -->
                <div class="lg:col-span-2 space-y-6">
                    @if($latestReservasi)
                    <div class="admin-card bg-white dark:bg-slate-800">
                        <div class="mb-6 border-b-4 border-black dark:border-white pb-3 flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-black text-slate-900 dark:text-slate-100 uppercase tracking-wide">Detail Reservasi Kamar</h3>
                                <p class="admin-subtitle">Informasi kamar dan rincian pemesanan Anda.</p>
                            </div>
                            <span class="px-3 py-1 border-2 border-black dark:border-white bg-yellow-400 text-black text-xs font-black uppercase tracking-widest leading-none shadow-[2px_2px_0px_0px_#000000]">
                                {{ strtoupper($latestReservasi->status) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Room info -->
                            <div class="space-y-4">
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Kamar Pilihan</span>
                                    <span class="text-lg font-black text-slate-900 dark:text-white">Kamar {{ $latestReservasi->kamar->nomor_kamar }} (Lantai {{ $latestReservasi->kamar->lantai }})</span>
                                </div>
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Tipe Kamar</span>
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200 capitalize">{{ $latestReservasi->kamar->tipe }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Harga Sewa Kamar</span>
                                    <span class="text-sm font-extrabold text-slate-800 dark:text-slate-200">Rp {{ number_format($latestReservasi->kamar->harga_bulan, 0, ',', '.') }}/bulan</span>
                                </div>
                            </div>

                            <!-- Rent Details -->
                            <div class="space-y-4">
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Tanggal Masuk (Check-In)</span>
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ \Carbon\Carbon::parse($latestReservasi->tanggal_mulai)->format('d F Y') }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Durasi Sewa</span>
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ $latestReservasi->durasi }} Bulan</span>
                                </div>
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Total Pembayaran Jaminan & Sewa</span>
                                    <span class="text-base font-black text-indigo-600 dark:text-indigo-400">Rp {{ number_format($latestReservasi->total_harga, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Proceed Button (CTA) -->
                        <div class="mt-8">
                            <a href="{{ route('penyewa.reservasi.pembayaran', $latestReservasi->id) }}" class="admin-btn-primary w-full text-center text-sm font-black uppercase tracking-wider block py-4 bg-yellow-400 text-black border-4 border-black shadow-[4px_4px_0px_0px_#000000] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#000000] transition-all">
                                Lanjutkan ke Pembayaran Reservasi &rarr;
                            </a>
                        </div>
                    </div>
                    @else
                    <div class="admin-card bg-white dark:bg-slate-800 flex flex-col items-center justify-center text-center p-8 border-4 border-black dark:border-white shadow-[6px_6px_0px_0px_#000000] dark:shadow-[6px_6px_0px_0px_#ffffff]">
                        <div class="w-20 h-20 bg-yellow-300 dark:bg-yellow-400 border-4 border-black rounded-full flex items-center justify-center text-4xl mb-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                            🛏️
                        </div>
                        <h3 class="text-2xl font-black uppercase tracking-tight text-slate-900 dark:text-slate-100">Pemesanan Kamar Belum Ditemukan</h3>
                        <p class="text-sm font-bold text-slate-600 dark:text-slate-300 mt-2 max-w-md">
                            Anda belum memiliki reservasi kamar kost saat ini. Silakan cari dan pilih kamar kost putri terbaik kami untuk memulai proses penyewaan.
                        </p>
                        <div class="mt-8 flex flex-col sm:flex-row gap-4 w-full justify-center">
                            <a href="{{ route('landing.kamar') }}" class="admin-btn-primary px-6 text-center text-sm font-black uppercase tracking-wider block py-4 bg-yellow-400 text-black border-4 border-black shadow-[4px_4px_0px_0px_#000000] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#000000] transition-all">
                                Cari & Booking Kamar &rarr;
                            </a>
                            <a href="{{ route('landing.index') }}" class="admin-btn-primary px-6 text-center text-sm font-black uppercase tracking-wider block py-4 bg-white dark:bg-slate-900 text-black dark:text-white border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#000000] dark:hover:shadow-[6px_6px_0px_0px_#ffffff] transition-all">
                                Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Right: Step Progress Checklist -->
                <div>
                    <div class="admin-card bg-white dark:bg-slate-800">
                        <div class="mb-6 border-b-4 border-black dark:border-white pb-3">
                            <h3 class="text-base font-black text-slate-900 dark:text-slate-100 uppercase tracking-wide">Tahapan Reservasi</h3>
                            <p class="admin-subtitle">Langkah untuk mengaktifkan hunian.</p>
                        </div>

                        @php
                            $steps = $latestReservasi ? [
                                ['title' => '1. Registrasi Akun', 'desc' => 'Akun Anda telah berhasil dibuat dan terdaftar.', 'status' => 'done'],
                                ['title' => '2. Selesaikan Pembayaran', 'desc' => 'Lakukan transfer deposit/sewa melalui gerbang Midtrans.', 'status' => 'active'],
                                ['title' => '3. Aktivasi Pengelola', 'desc' => 'Pengelola akan melakukan pengecekan administrasi.', 'status' => 'pending'],
                                ['title' => '4. Siap Ditempati', 'desc' => 'Serah terima kunci kamar dan Anda siap menghuni kamar.', 'status' => 'pending'],
                            ] : [
                                ['title' => '1. Registrasi Akun', 'desc' => 'Akun Anda telah berhasil dibuat dan terdaftar.', 'status' => 'done'],
                                ['title' => '2. Pilih & Booking Kamar', 'desc' => 'Silakan telusuri kamar yang tersedia dan lakukan pemesanan online.', 'status' => 'active'],
                                ['title' => '3. Selesaikan Pembayaran', 'desc' => 'Lakukan transfer deposit/sewa melalui gerbang Midtrans.', 'status' => 'pending'],
                                ['title' => '4. Aktivasi Pengelola', 'desc' => 'Pengelola akan melakukan pengecekan administrasi.', 'status' => 'pending'],
                                ['title' => '5. Siap Ditempati', 'desc' => 'Serah terima kunci kamar dan Anda siap menghuni kamar.', 'status' => 'pending'],
                            ];
                        @endphp

                        <div class="space-y-6">
                            @foreach($steps as $idx => $step)
                                <div class="flex items-start gap-3 {{ $step['status'] === 'pending' ? 'opacity-60' : '' }}">
                                    @if($step['status'] === 'done')
                                        <div class="w-8 h-8 rounded-full border-2 border-black bg-emerald-300 flex items-center justify-center font-bold text-black shrink-0">
                                            ✓
                                        </div>
                                    @elseif($step['status'] === 'active')
                                        <div class="w-8 h-8 rounded-full border-2 border-black bg-yellow-300 flex items-center justify-center font-black text-black shrink-0">
                                            {{ $idx + 1 }}
                                        </div>
                                    @else
                                        <div class="w-8 h-8 rounded-full border-2 border-black bg-slate-200 dark:bg-slate-700 text-slate-500 flex items-center justify-center font-bold shrink-0">
                                            {{ $idx + 1 }}
                                        </div>
                                    @endif
                                    <div>
                                        <span class="text-xs font-black uppercase tracking-wide text-slate-900 dark:text-white">{{ $step['title'] }}</span>
                                        <p class="text-[10px] text-slate-500 font-semibold mt-0.5">{{ $step['desc'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Customer Support Info -->
                    <div class="mt-6 bg-yellow-100 dark:bg-yellow-950/20 border-4 border-black dark:border-white p-5 text-black dark:text-yellow-200 shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff]">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-slate-100">Butuh Bantuan?</h4>
                        @php
                            $waCs = \App\Models\Setting::get('contact_whatsapp', config('services.wa_owner', '6282219575575'));
                            $cleanWaCs = \App\Models\Setting::formatWhatsapp($waCs);
                        @endphp
                        <a href="https://wa.me/{{ $cleanWaCs }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 border-2 border-black bg-green-500 text-white text-[10px] font-black uppercase tracking-widest shadow-[1px_1px_0px_0px_#000000] hover:translate-y-[-1px] hover:shadow-[2px_2px_0px_0px_#000000] transition-all">
                            💬 Chat WhatsApp
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
