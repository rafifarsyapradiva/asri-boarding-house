@php
    $user = auth()->user();
    $isPenyewaAktif = $user && $user->isActiveTenant();

    $navClass = function(bool $active) {
        return $active
            ? 'flex items-center px-4 py-3 rounded-none text-sm font-black gap-3 border-2 border-black dark:border-white bg-yellow-400 text-black shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff] translate-x-[-1px] translate-y-[-1px] transition-all duration-150 select-none'
            : 'flex items-center px-4 py-3 rounded-none text-sm font-extrabold gap-3 border-2 border-transparent hover:border-black dark:hover:border-white hover:bg-yellow-400 hover:text-black dark:hover:text-black text-slate-700 dark:text-slate-300 transition-all duration-150 select-none';
    };
@endphp

<!-- Mobile Sidebar Overlay -->
<div x-show="sidebarOpen"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-40 bg-slate-900/60 lg:hidden"
     style="display: none;"
     @click="sidebarOpen = false">
</div>

<!-- Sidebar Container -->
<div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
     class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-slate-900 border-r-4 border-black dark:border-white text-slate-600 dark:text-slate-300 flex flex-col transition-all duration-300 ease-in-out lg:translate-x-0"
     x-cloak>

    <!-- Logo / Brand Header -->
    <div class="flex items-center justify-between h-16 px-6 border-b-4 border-black dark:border-white shrink-0 bg-yellow-400 text-black">
        <a href="{{ $user ? route($user->getDashboardRouteName()) : route('landing.index') }}" class="block">
            <x-application-logo class="block h-9 w-auto fill-current text-black" />
        </a>
        <!-- Close Button (Mobile Only) -->
        <button @click="sidebarOpen = false" class="text-black hover:bg-yellow-300 lg:hidden focus:outline-none p-1 border-2 border-black shadow-[1px_1px_0px_0px_#000000]" aria-label="Tutup Menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Navigation Links (Scrollable) -->
    <div class="flex-1 overflow-y-auto px-4 py-6 space-y-1.5 scrollbar-thin scrollbar-thumb-slate-300 dark:scrollbar-thumb-slate-700 scrollbar-track-transparent">
        
        @if($user && $user->isAdmin())
            <!-- Section Title: Menu Utama -->
            <div class="px-3 mb-2 text-[10px] font-black text-slate-900 dark:text-slate-400 uppercase tracking-widest">
                Menu Utama
            </div>

            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" 
               class="{{ $navClass(request()->routeIs('admin.dashboard')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- Kalender Kontrol -->
            <a href="{{ route('admin.calendar.index') }}" 
               class="{{ $navClass(request()->routeIs('admin.calendar.*')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Kalender Kontrol</span>
            </a>

            <!-- Kamar -->
            <a href="{{ route('admin.kamar.index') }}" 
               class="{{ $navClass(request()->routeIs('admin.kamar.*')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Kamar</span>
            </a>

            <!-- Fasilitas -->
            <a href="{{ route('admin.fasilitas.index') }}" 
               class="{{ $navClass(request()->routeIs('admin.fasilitas.*')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
                <span>Fasilitas</span>
            </a>

            <!-- Penyewa -->
            <a href="{{ route('admin.penyewa.index') }}" 
               class="{{ $navClass(request()->routeIs('admin.penyewa.*')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>Penyewa</span>
            </a>

            <!-- Section Title: Transaksi -->
            <div class="px-3 pt-4 mb-2 text-[10px] font-black text-slate-900 dark:text-slate-400 uppercase tracking-widest">
                Transaksi
            </div>

            <!-- Reservasi -->
            <a href="{{ route('admin.reservasi.index') }}" 
               class="{{ $navClass(request()->routeIs('admin.reservasi.*')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Reservasi</span>
            </a>

            <!-- Tagihan -->
            <a href="{{ route('admin.tagihan.index') }}" 
               class="{{ $navClass(request()->routeIs('admin.tagihan.*')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span>Tagihan</span>
            </a>

            <!-- Pengeluaran -->
            <a href="{{ route('admin.pengeluaran.index') }}" 
               class="{{ $navClass(request()->routeIs('admin.pengeluaran.*')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <span>Pengeluaran</span>
            </a>

            <!-- Section Title: Analitis & Ulasan -->
            <div class="px-3 pt-4 mb-2 text-[10px] font-black text-slate-900 dark:text-slate-400 uppercase tracking-widest">
                Laporan & Ulasan
            </div>

            <!-- Laporan -->
            <a href="{{ route('admin.laporan.index') }}" 
               class="{{ $navClass(request()->routeIs('admin.laporan.*')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                </svg>
                <span>Laporan</span>
            </a>

            <!-- Review Pelanggan -->
            <a href="{{ route('admin.reviews.index') }}" 
               class="{{ $navClass(request()->routeIs('admin.reviews.*')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.36 1.243.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.906a1 1 0 00.95-.69l1.519-4.674z" />
                </svg>
                <span>Review Pelanggan</span>
            </a>

            <!-- Keluhan Penyewa -->
            <a href="{{ route('admin.keluhan.index') }}" 
               class="{{ $navClass(request()->routeIs('admin.keluhan.*')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Keluhan Penyewa</span>
            </a>

            <!-- Chat Guest -->
            <a href="{{ route('admin.guest-chats.index') }}" 
               class="{{ $navClass(request()->routeIs('admin.guest-chats.*')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <span>Chat Guest</span>
            </a>

            <!-- FAQ -->
            <a href="{{ route('admin.faq.index') }}" 
               class="{{ $navClass(request()->routeIs('admin.faq.*')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>FAQ</span>
            </a>

            <!-- Section Title: Sistem -->
            <div class="px-3 pt-4 mb-2 text-[10px] font-black text-slate-900 dark:text-slate-400 uppercase tracking-widest">
                Pengaturan
            </div>

            <!-- Pengaturan Konten -->
            <a href="{{ route('admin.settings.edit') }}" 
               class="{{ $navClass(request()->routeIs('admin.settings.*')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Pengaturan Konten</span>
            </a>

            <!-- Notifikasi Global -->
            <a href="{{ route('admin.notifikasi.index') }}" 
               class="{{ $navClass(request()->routeIs('admin.notifikasi.*')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span>Notifikasi Global</span>
            </a>

            <!-- Notifikasi Khusus -->
            <a href="{{ route('admin.notifikasi-khusus.index') }}" 
               class="{{ $navClass(request()->routeIs('admin.notifikasi-khusus.*')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 11-6 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <span>Notifikasi Khusus</span>
            </a>

            <!-- Kelola Peraturan -->
            <a href="{{ route('admin.peraturan.index') }}" 
               class="{{ $navClass(request()->routeIs('admin.peraturan.*')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Kelola Peraturan</span>
            </a>

            <!-- Kelola Galeri -->
            <a href="{{ route('admin.gallery.index') }}" 
               class="{{ $navClass(request()->routeIs('admin.gallery.*')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Kelola Galeri</span>
            </a>

            <!-- Pengaturan Akun -->
            <a href="{{ route('admin.profile.edit') }}" 
               class="{{ $navClass(request()->routeIs('admin.profile.*')) }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>Pengaturan Akun</span>
            </a>
        @elseif($user && $user->role === 'penyewa')
            @php
                $latestReservasi = $user->latest_active_reservasi;
            @endphp

            @if($isPenyewaAktif)
                <!-- Section Title: Menu Utama -->
                <div class="px-3 mb-2 text-[10px] font-black text-slate-900 dark:text-slate-400 uppercase tracking-widest">
                    Menu Utama
                </div>

                <!-- Dashboard -->
                <a href="{{ route('penyewa.dashboard') }}" 
                   class="{{ $navClass(request()->routeIs('penyewa.dashboard')) }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- Peraturan Kost -->
                <a href="{{ route('penyewa.peraturan') }}" 
                   class="{{ $navClass(request()->routeIs('penyewa.peraturan')) }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Peraturan Kost</span>
                </a>

                <!-- Section Title: Keuangan & Transaksi -->
                <div class="px-3 pt-4 mb-2 text-[10px] font-black text-slate-900 dark:text-slate-400 uppercase tracking-widest">
                    Keuangan & Transaksi
                </div>

                <!-- Tagihan Saya -->
                <a href="{{ route('penyewa.tagihan.index') }}" 
                   class="{{ $navClass(request()->routeIs('penyewa.tagihan.*')) }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span>Tagihan Saya</span>
                </a>

                <!-- Riwayat Pemesanan -->
                <a href="{{ route('penyewa.reservasi.index') }}" 
                   class="{{ $navClass(request()->routeIs('penyewa.reservasi.index')) }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Riwayat Pemesanan</span>
                </a>

                <!-- Riwayat Pembayaran -->
                <a href="{{ route('penyewa.pembayaran') }}" 
                   class="{{ $navClass(request()->routeIs('penyewa.pembayaran')) }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <span>Riwayat Pembayaran</span>
                </a>

                <!-- Section Title: Layanan & Komunikasi -->
                <div class="px-3 pt-4 mb-2 text-[10px] font-black text-slate-900 dark:text-slate-400 uppercase tracking-widest">
                    Layanan & Komunikasi
                </div>

                <!-- Riwayat Chat -->
                <a href="{{ route('penyewa.chat.history') }}" 
                   class="{{ $navClass(request()->routeIs('penyewa.chat.history') || request()->routeIs('reservasi.chat')) }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span>Riwayat Chat</span>
                </a>

                <!-- Notifikasi Saya -->
                <a href="{{ route('penyewa.notifikasi.index') }}" 
                   class="{{ $navClass(request()->routeIs('penyewa.notifikasi.*')) }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span>Notifikasi Saya</span>
                </a>

                <!-- Keluhan Saya -->
                <a href="{{ route('penyewa.keluhan.index') }}" 
                   class="{{ $navClass(request()->routeIs('penyewa.keluhan.*')) }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Keluhan Saya</span>
                </a>

                <!-- Section Title: Pengaturan -->
                <div class="px-3 pt-4 mb-2 text-[10px] font-black text-slate-900 dark:text-slate-400 uppercase tracking-widest">
                    Pengaturan
                </div>

                <!-- Pengaturan Akun -->
                <a href="{{ route('profile.edit', ['tab' => 'profile']) }}" 
                   class="{{ $navClass(request()->routeIs('profile.edit')) }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Pengaturan Akun</span>
                </a>
            @else
                <!-- Section Title: Menu Reservasi -->
                <div class="px-3 mb-2 text-[10px] font-black text-slate-900 dark:text-slate-400 uppercase tracking-widest">
                    Menu Reservasi
                </div>

                <!-- Pembayaran Reservasi -->
                @if($latestReservasi)
                    <a href="{{ route('penyewa.reservasi.pembayaran', $latestReservasi->id) }}" 
                       class="{{ $navClass(request()->routeIs('penyewa.reservasi.show') || request()->routeIs('penyewa.reservasi.pembayaran')) }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span>Pembayaran Reservasi</span>
                    </a>
                @endif

                <!-- Riwayat Pemesanan -->
                <a href="{{ route('penyewa.reservasi.index') }}" 
                   class="{{ $navClass(request()->routeIs('penyewa.reservasi.index')) }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Riwayat Pemesanan</span>
                </a>

                <!-- Riwayat Pembayaran -->
                <a href="{{ route('penyewa.pembayaran') }}" 
                   class="{{ $navClass(request()->routeIs('penyewa.pembayaran')) }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <span>Riwayat Pembayaran</span>
                </a>

                <!-- Section Title: Layanan & Komunikasi -->
                <div class="px-3 pt-4 mb-2 text-[10px] font-black text-slate-900 dark:text-slate-400 uppercase tracking-widest">
                    Layanan & Komunikasi
                </div>

                <!-- Riwayat Chat -->
                <a href="{{ route('penyewa.chat.history') }}" 
                   class="{{ $navClass(request()->routeIs('penyewa.chat.history') || request()->routeIs('reservasi.chat')) }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span>Riwayat Chat</span>
                </a>

                <!-- Section Title: Pengaturan -->
                <div class="px-3 pt-4 mb-2 text-[10px] font-black text-slate-900 dark:text-slate-400 uppercase tracking-widest">
                    Pengaturan
                </div>

                <!-- Pengaturan Akun -->
                <a href="{{ route('profile.edit', ['tab' => 'profile']) }}" 
                   class="{{ $navClass(request()->routeIs('profile.edit')) }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Pengaturan Akun</span>
                </a>
            @endif
        @endif

    </div>

    <!-- Sidebar Footer / Profile info & Logout -->
    <div class="p-4 border-t-4 border-black dark:border-white shrink-0 bg-gray-50/50 dark:bg-slate-950/20">
        <div class="flex items-center justify-between gap-3 p-2.5 bg-white dark:bg-slate-800 rounded-none border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff]">
            <div class="min-w-0 flex-1">
                <div class="text-xs font-black text-slate-900 dark:text-white truncate">{{ Auth::user()->name }}</div>
                <div class="text-[10px] font-bold text-slate-500 truncate">{{ Auth::user()->email }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                @csrf
                <button type="submit" class="p-2 text-black dark:text-white bg-red-400 hover:bg-red-500 border-2 border-black dark:border-white shadow-[1px_1px_0px_0px_#000000] dark:shadow-[1px_1px_0px_0px_#ffffff] rounded-none transition-all flex items-center justify-center cursor-pointer" title="Keluar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

</div>
