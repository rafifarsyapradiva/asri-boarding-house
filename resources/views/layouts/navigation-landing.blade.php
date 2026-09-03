<!-- SECTION NAVBAR (Navigasi Kaku) -->
<nav x-data="{ mobileMenuOpen: false }" class="bg-white dark:bg-slate-900 border-b-4 border-black dark:border-white sticky top-0 z-50 text-black dark:text-white transition-colors duration-200">
    <div class="max-w-6xl mx-auto px-6 h-20 flex items-center justify-between">
        <!-- Logo -->
        <a href="{{ route('landing.index') }}" class="flex items-center gap-2 group">
            <div class="px-4 py-2 bg-yellow-400 text-black border-4 border-black font-black uppercase tracking-wider text-lg sm:text-xl neo-btn-shadow transition duration-200 hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_#000000]">
                {{ \App\Models\Setting::get('logo_icon') }} {{ \App\Models\Setting::get('logo_text') }}
            </div>
        </a>

        <!-- Nav Items -->
        <div class="hidden lg:flex items-center gap-6 font-black text-sm uppercase tracking-wider">
            <a href="{{ route('landing.fasilitas') }}" class="hover:text-yellow-600 dark:hover:text-yellow-400 hover:underline underline-offset-4 decoration-2 {{ request()->routeIs('landing.fasilitas') ? 'text-yellow-600 dark:text-yellow-400 underline decoration-4' : 'text-black dark:text-white' }}">Fasilitas</a>
            <a href="{{ route('landing.kamar') }}" class="hover:text-yellow-600 dark:hover:text-yellow-400 hover:underline underline-offset-4 decoration-2 {{ request()->routeIs('landing.kamar') ? 'text-yellow-600 dark:text-yellow-400 underline decoration-4' : 'text-black dark:text-white' }}">Tipe Kamar</a>
            <a href="{{ route('landing.caraBooking') }}" class="hover:text-yellow-600 dark:hover:text-yellow-400 hover:underline underline-offset-4 decoration-2 {{ request()->routeIs('landing.caraBooking') ? 'text-yellow-600 dark:text-yellow-400 underline decoration-4' : 'text-black dark:text-white' }}">Cara Booking</a>
        </div>

        <!-- Auth & Hamburger Container -->
        <div class="flex items-center gap-3">
            <!-- Theme Toggle Button -->
            <button 
                x-data="themeToggle" 
                @click="toggle()" 
                class="p-2.5 text-black dark:text-white border-2 border-black dark:border-white neo-shadow-sm hover:bg-yellow-400 dark:hover:bg-yellow-400 dark:hover:text-black focus:outline-none focus-visible:outline-3 focus-visible:outline-blue-600 dark:focus-visible:outline-blue-400 focus-visible:outline-offset-2 transition-all duration-150 cursor-pointer flex items-center justify-center w-11 h-11 min-h-[44px] min-w-[44px]"
                title="Ubah Tema"
            >
                <!-- Sun Icon (shows in dark mode) -->
                <svg x-show="theme === 'dark'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9h-1m15.364-3.636l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                </svg>
                <!-- Moon Icon (shows in light mode) -->
                <svg x-show="theme === 'light'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>

            <!-- Auth Buttons (Desktop) -->
            <div class="hidden sm:flex items-center gap-3">
                @auth
                    <!-- Desktop Dropdown Profile -->
                    <div class="hidden md:inline-block">
                        <x-dropdown align="right" width="48" contentClasses="py-1 bg-white dark:bg-slate-800 border-2 border-black dark:border-white">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center text-xs font-black bg-white dark:bg-slate-900 text-black dark:text-white px-3.5 py-2 border-2 border-black dark:border-white neo-shadow-sm hover:bg-yellow-400 dark:hover:text-black focus:outline-none transition-all duration-150 cursor-pointer">
                                    <span>👋 {{ auth()->user()->nama }}</span>
                                    <svg class="fill-current h-4 w-4 ms-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="block px-4 py-2 text-[10px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider">
                                    {{ __('Kelola Akun') }}
                                </div>
                                @if(auth()->user()->role === 'admin')
                                    <x-dropdown-link :href="route('admin.profile.edit')">
                                        {{ __('Pengaturan Akun') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.settings.edit')">
                                        {{ __('Pengaturan Konten') }}
                                    </x-dropdown-link>
                                @else
                                    <x-dropdown-link :href="route('profile.edit')">
                                        {{ __('Halaman Profil Akun') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('penyewa.peraturan')">
                                        {{ __('Peraturan Kost') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('profile.edit')">
                                        {{ __('Pengaturan') }}
                                    </x-dropdown-link>
                                @endif
                            </x-slot>
                        </x-dropdown>
                    </div>
                    <a href="{{ route('dashboard') }}" class="px-4 py-3 min-h-[44px] inline-flex items-center justify-center bg-yellow-400 hover:bg-yellow-300 text-black text-xs font-black border-2 border-black dark:border-white uppercase tracking-wider neo-btn-shadow neo-btn-interactive">
                        @if(auth()->user()->hasBookingInProgress())
                            Pembayaran Reservasi
                        @else
                            Dashboard
                        @endif
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-3 min-h-[44px] inline-flex items-center justify-center bg-white hover:bg-gray-100 text-black text-xs font-black border-2 border-black dark:border-white uppercase tracking-wider neo-btn-shadow neo-btn-interactive">
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('reservasi.login') }}" class="px-4 py-3 min-h-[44px] inline-flex items-center justify-center bg-yellow-400 hover:bg-yellow-300 text-black text-xs font-black border-2 border-black dark:border-white uppercase tracking-wider neo-btn-shadow neo-btn-interactive">
                        Masuk
                    </a>
                    <a href="{{ route('reservasi.register') }}" class="px-4 py-3 min-h-[44px] inline-flex items-center justify-center bg-white hover:bg-gray-100 text-black text-xs font-black border-2 border-black dark:border-white uppercase tracking-wider neo-btn-shadow neo-btn-interactive">
                        Daftar
                    </a>
                @endauth
            </div>

            <!-- Hamburger Button (Mobile) -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="lg:hidden p-3 bg-yellow-400 text-black border-2 border-black dark:border-white font-black uppercase tracking-wider neo-shadow-sm transition hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[1px_1px_0px_0px_#000000] focus:outline-none" aria-label="Toggle menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                    <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" style="display: none;"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         style="display: none;"
         class="lg:hidden border-t-4 border-black dark:border-white bg-white dark:bg-slate-900 text-black dark:text-white">
        <div class="px-6 py-6 flex flex-col gap-4 font-black uppercase tracking-wider text-sm">
            <!-- Links -->
            <a href="{{ route('landing.fasilitas') }}" class="py-2.5 border-b-2 border-black dark:border-white hover:text-yellow-600 dark:hover:text-yellow-400 {{ request()->routeIs('landing.fasilitas') ? 'text-yellow-600 dark:text-yellow-400 underline decoration-2' : '' }}">Fasilitas</a>
            <a href="{{ route('landing.kamar') }}" class="py-2.5 border-b-2 border-black dark:border-white hover:text-yellow-600 dark:hover:text-yellow-400 {{ request()->routeIs('landing.kamar') ? 'text-yellow-600 dark:text-yellow-400 underline decoration-2' : '' }}">Tipe Kamar</a>
            <a href="{{ route('landing.caraBooking') }}" class="py-2.5 border-b-2 border-black dark:border-white hover:text-yellow-600 dark:hover:text-yellow-400 {{ request()->routeIs('landing.caraBooking') ? 'text-yellow-600 dark:text-yellow-400 underline decoration-2' : '' }}">Cara Booking</a>

            <!-- Mobile Auth Buttons -->
            <div class="flex flex-col gap-3 pt-3 lg:hidden">
                @auth
                    <div class="text-xs font-black bg-yellow-100 dark:bg-slate-800 text-black dark:text-white px-4 py-3 border-2 border-black dark:border-white neo-shadow-sm text-center">
                        👋 {{ auth()->user()->nama }}
                    </div>
                    @if(auth()->user()->role === 'penyewa')
                        <div class="flex flex-col gap-2 pl-4 border-l-2 border-black dark:border-white">
                            <a href="{{ route('profile.edit') }}" class="text-xs font-bold text-slate-700 dark:text-slate-300 hover:text-black dark:hover:text-white">Halaman Profil Akun</a>
                            <a href="{{ route('penyewa.peraturan') }}" class="text-xs font-bold text-slate-700 dark:text-slate-300 hover:text-black dark:hover:text-white">Peraturan Kost</a>
                            <a href="{{ route('profile.edit') }}" class="text-xs font-bold text-slate-700 dark:text-slate-300 hover:text-black dark:hover:text-white">Pengaturan</a>
                        </div>
                    @endif
                    <a href="{{ route('dashboard') }}" class="w-full text-center px-4 py-3 bg-yellow-400 hover:bg-yellow-300 text-black text-xs font-black border-2 border-black dark:border-white uppercase tracking-wider neo-btn-shadow neo-btn-interactive">
                        @if(auth()->user()->hasBookingInProgress())
                            Pembayaran Reservasi
                        @else
                            Dashboard
                        @endif
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full px-4 py-3 bg-white dark:bg-slate-800 hover:bg-gray-100 dark:hover:bg-slate-700 text-black dark:text-white text-xs font-black border-2 border-black dark:border-white uppercase tracking-wider neo-btn-shadow neo-btn-interactive">
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('reservasi.login') }}" class="w-full text-center px-4 py-3 bg-yellow-400 hover:bg-yellow-300 text-black text-xs font-black border-2 border-black dark:border-white uppercase tracking-wider neo-btn-shadow neo-btn-interactive">
                        Masuk
                    </a>
                    <a href="{{ route('reservasi.register') }}" class="w-full text-center px-4 py-3 bg-white dark:bg-slate-800 hover:bg-gray-100 dark:hover:bg-slate-700 text-black dark:text-white text-xs font-black border-2 border-black dark:border-white uppercase tracking-wider neo-btn-shadow neo-btn-interactive">
                        Daftar
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
