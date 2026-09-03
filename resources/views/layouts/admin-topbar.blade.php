@php
    $user = auth()->user();
    $isPenyewaAktif = $user && $user->isActiveTenant();
@endphp
<header class="sticky top-0 z-30 flex items-center justify-between h-16 px-4 bg-white border-b-4 border-black dark:bg-slate-900 dark:border-white shrink-0 sm:px-6 lg:px-8">
    <div class="flex items-center gap-3">
        <!-- Hamburger Menu Button (Mobile Only) -->
        <button @click="sidebarOpen = !sidebarOpen" class="p-2 text-black dark:text-white bg-white dark:bg-slate-800 border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff] hover:bg-yellow-400 dark:hover:bg-yellow-400 hover:text-black dark:hover:text-black lg:hidden focus:outline-none focus-visible:outline-2 focus-visible:outline-blue-600 dark:focus-visible:outline-blue-400 focus-visible:outline-offset-2 transition-all duration-150 cursor-pointer" aria-label="Buka Menu Sidebar">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <!-- Title / Brand on Mobile -->
        <div class="flex items-center lg:hidden">
            <a href="{{ $user ? route($user->getDashboardRouteName()) : route('landing.index') }}" class="block">
                <x-application-logo class="block h-8 w-auto fill-current text-slate-900 dark:text-white" />
            </a>
        </div>
    </div>

    <!-- Right Side Profile Dropdown -->
    <div class="flex items-center gap-4">
        <!-- Theme Toggle Button -->
        <button 
            x-data="themeToggle" 
            @click="toggle()" 
            class="p-2.5 text-black dark:text-white bg-white dark:bg-slate-800 border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff] hover:bg-yellow-400 dark:hover:bg-yellow-400 hover:text-black dark:hover:text-black focus:outline-none focus-visible:outline-2 focus-visible:outline-blue-600 dark:focus-visible:outline-blue-400 focus-visible:outline-offset-2 transition-all duration-150 cursor-pointer neo-btn-interactive"
            title="Ubah Tema"
            aria-label="Ubah Tema Tampilan"
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

        @php
            $initials = $user ? $user->initials : 'U';
        @endphp
        <x-dropdown align="right" width="56" contentClasses="py-0 bg-white dark:bg-slate-900 overflow-hidden border-2 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] rounded-none">
            <x-slot name="trigger">
                <button class="inline-flex items-center gap-2 px-3 py-2 min-h-[44px] text-sm leading-4 font-black text-black dark:text-white bg-white dark:bg-slate-800 border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff] hover:bg-yellow-400 dark:hover:bg-yellow-400 hover:text-black dark:hover:text-black focus:outline-none focus-visible:outline-2 focus-visible:outline-blue-600 dark:focus-visible:outline-blue-400 focus-visible:outline-offset-2 transition-all duration-150 cursor-pointer neo-btn-interactive">
                    <!-- User Avatar or Initials -->
                    @if(Auth::user()->foto && !str_starts_with(Auth::user()->foto, 'temp_'))
                        <img src="{{ (str_starts_with(Auth::user()->foto, 'http')) ? Auth::user()->foto : asset('storage/' . Auth::user()->foto) }}" alt="{{ Auth::user()->name }}" width="32" height="32" class="w-8 h-8 rounded-full border-2 border-black object-cover shrink-0">
                    @else
                        <div class="w-8 h-8 rounded-full bg-yellow-400 text-black flex items-center justify-center font-black text-sm border-2 border-black dark:border-white shrink-0">
                            {{ $initials }}
                        </div>
                    @endif
                    <span class="font-extrabold hidden md:inline-block">👋 {{ Auth::user()->name }}</span>
                    <div class="ms-1 shrink-0">
                        <svg class="fill-current h-4 w-4 text-black dark:text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </button>
            </x-slot>

            <x-slot name="content">
                <!-- User Info Header -->
                <div class="px-4 py-3 border-b-2 border-black dark:border-white bg-yellow-50 dark:bg-slate-950/80">
                    <div class="flex items-center gap-3">
                        @if(Auth::user()->foto && !str_starts_with(Auth::user()->foto, 'temp_'))
                            <img src="{{ (str_starts_with(Auth::user()->foto, 'http')) ? Auth::user()->foto : asset('storage/' . Auth::user()->foto) }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full border-2 border-black object-cover shrink-0">
                        @else
                            <div class="w-10 h-10 rounded-full bg-yellow-400 text-black flex items-center justify-center font-black text-base border-2 border-black dark:border-white shrink-0">
                                {{ $initials }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <p class="text-sm font-black text-slate-900 dark:text-slate-100 truncate leading-none mb-1">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] font-bold text-slate-600 dark:text-slate-400 truncate leading-none mb-1.5">{{ Auth::user()->email }}</p>
                            <span class="inline-block text-[8px] font-black uppercase tracking-widest px-1.5 py-0.5 border border-black dark:border-white bg-yellow-400 text-black leading-none">
                                {{ auth()->user()->role === 'admin' ? 'Admin' : ($isPenyewaAktif ? 'Penyewa Aktif' : 'Penyewa Pending') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Links with Icons -->
                <div class="py-1">
                    <!-- Dashboard -->
                    <x-dropdown-link :href="auth()->user()->role === 'admin' ? route('admin.dashboard') : route('penyewa.dashboard')" class="flex items-center gap-2.5 px-4 py-2 hover:bg-yellow-100 dark:hover:bg-slate-800 font-bold text-xs">
                        <svg class="w-4 h-4 text-black dark:text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                        </svg>
                        <span>{{ __('Dashboard') }}</span>
                    </x-dropdown-link>

                    <!-- Profil & Keamanan -->
                    <x-dropdown-link :href="auth()->user()->role === 'admin' ? route('admin.profile.edit') : route('profile.edit')" class="flex items-center gap-2.5 px-4 py-2 hover:bg-yellow-100 dark:hover:bg-slate-800 font-bold text-xs">
                        <svg class="w-4 h-4 text-black dark:text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>{{ __('Profil & Keamanan') }}</span>
                    </x-dropdown-link>

                    @if(auth()->user()->role === 'admin')
                        <!-- Pengaturan Konten -->
                        <x-dropdown-link :href="route('admin.settings.edit')" class="flex items-center gap-2.5 px-4 py-2 hover:bg-yellow-100 dark:hover:bg-slate-800 font-bold text-xs">
                            <svg class="w-4 h-4 text-black dark:text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>{{ __('Pengaturan Konten') }}</span>
                        </x-dropdown-link>
                    @else
                        <!-- Peraturan Kost -->
                        <x-dropdown-link :href="route('penyewa.peraturan')" class="flex items-center gap-2.5 px-4 py-2 hover:bg-yellow-100 dark:hover:bg-slate-800 font-bold text-xs">
                            <svg class="w-4 h-4 text-black dark:text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>{{ __('Peraturan Kost') }}</span>
                        </x-dropdown-link>
                    @endif
                </div>

                <div class="border-t-2 border-black dark:border-white"></div>

                <!-- Authentication -->
                <div class="py-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();"
                                class="flex items-center gap-2.5 px-4 py-2 text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-950/20 font-black text-xs">
                            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>{{ __('Keluar') }}</span>
                        </x-dropdown-link>
                    </form>
                </div>
            </x-slot>
        </x-dropdown>
    </div>
</header>
