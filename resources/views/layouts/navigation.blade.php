<nav x-data="{ open: false }" class="bg-white border-b-4 border-black relative z-40">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                        <div class="px-4 py-1.5 bg-yellow-400 text-black border-2 border-black font-black uppercase tracking-wider text-sm shadow-[2px_2px_0px_0px_#000000] transition duration-200 hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[1px_1px_0px_0px_#000000]">
                            {{ \App\Models\Setting::get('logo_icon', '🏠') }} {{ \App\Models\Setting::get('logo_text', 'ASRI') }}
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('admin.dashboard') || request()->routeIs('penyewa.dashboard') || request()->routeIs('penyewa.reservasi.show') || request()->routeIs('penyewa.reservasi.pembayaran')">
                        @if(auth()->check() && auth()->user()->hasBookingInProgress())
                            {{ __('Pembayaran Reservasi') }}
                        @else
                            {{ __('Dashboard') }}
                        @endif
                    </x-nav-link>

                    @if(auth()->user()->role === 'penyewa')
                        <x-nav-link :href="route('penyewa.reservasi.index')" :active="request()->routeIs('penyewa.reservasi.index')">
                            {{ __('Riwayat Pemesanan') }}
                        </x-nav-link>
                        <x-nav-link :href="route('penyewa.pembayaran')" :active="request()->routeIs('penyewa.pembayaran')">
                            {{ __('Riwayat Pembayaran') }}
                        </x-nav-link>
                        <x-nav-link :href="route('penyewa.chat.history')" :active="request()->routeIs('penyewa.chat.history') || request()->routeIs('penyewa.reservasi.chat')">
                            {{ __('Riwayat Chat') }}
                        </x-nav-link>
                        <x-nav-link :href="route('penyewa.peraturan')" :active="request()->routeIs('penyewa.peraturan')">
                            {{ __('Peraturan & Tata Tertib') }}
                        </x-nav-link>
                    @endif

                    @if(auth()->user()->role === 'admin')
                        <x-nav-link :href="route('admin.kamar.index')" :active="request()->routeIs('admin.kamar.*')">
                            {{ __('Kamar') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.penyewa.index')" :active="request()->routeIs('admin.penyewa.*')">
                            {{ __('Penyewa') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.reservasi.index')" :active="request()->routeIs('admin.reservasi.*')">
                            {{ __('Reservasi') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.tagihan.index')" :active="request()->routeIs('admin.tagihan.*')">
                            {{ __('Tagihan') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.laporan.index')" :active="request()->routeIs('admin.laporan.*')">
                            {{ __('Laporan') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.reviews.index')" :active="request()->routeIs('admin.reviews.*')">
                            {{ __('Review Pelanggan') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.settings.edit')" :active="request()->routeIs('admin.settings.*')">
                            {{ __('Pengaturan Konten') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.profile.edit')" :active="request()->routeIs('admin.profile.*')">
                            {{ __('Pengaturan Akun') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 border-2 border-black text-xs font-black uppercase tracking-wider text-black bg-white shadow-[2px_2px_0px_0px_#000000] hover:translate-x-[1px] hover:translate-y-[1px] hover:shadow-[1px_1px_0px_0px_#000000] focus:outline-none transition-all duration-150 cursor-pointer">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @if(auth()->user()->role === 'admin')
                            <x-dropdown-link :href="route('admin.profile.edit')">
                                {{ __('Pengaturan Akun') }}
                            </x-dropdown-link>
                        @else
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                        @endif

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('admin.dashboard') || request()->routeIs('penyewa.dashboard') || request()->routeIs('penyewa.reservasi.show') || request()->routeIs('penyewa.reservasi.pembayaran')">
                @if(auth()->check() && auth()->user()->hasBookingInProgress())
                    {{ __('Pembayaran Reservasi') }}
                @else
                    {{ __('Dashboard') }}
                @endif
            </x-responsive-nav-link>

            @if(auth()->user()->role === 'penyewa')
                <x-responsive-nav-link :href="route('penyewa.reservasi.index')" :active="request()->routeIs('penyewa.reservasi.index')">
                    {{ __('Riwayat Pemesanan') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('penyewa.pembayaran')" :active="request()->routeIs('penyewa.pembayaran')">
                    {{ __('Riwayat Pembayaran') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('penyewa.chat.history')" :active="request()->routeIs('penyewa.chat.history') || request()->routeIs('penyewa.reservasi.chat')">
                    {{ __('Riwayat Chat') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('penyewa.peraturan')" :active="request()->routeIs('penyewa.peraturan')">
                    {{ __('Peraturan & Tata Tertib') }}
                </x-responsive-nav-link>
            @endif

            @if(auth()->user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.kamar.index')" :active="request()->routeIs('admin.kamar.*')">
                    {{ __('Kamar') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.penyewa.index')" :active="request()->routeIs('admin.penyewa.*')">
                    {{ __('Penyewa') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.reservasi.index')" :active="request()->routeIs('admin.reservasi.*')">
                    {{ __('Reservasi') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.tagihan.index')" :active="request()->routeIs('admin.tagihan.*')">
                    {{ __('Tagihan') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.laporan.index')" :active="request()->routeIs('admin.laporan.*')">
                    {{ __('Laporan') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.reviews.index')" :active="request()->routeIs('admin.reviews.*')">
                    {{ __('Review Pelanggan') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.settings.edit')" :active="request()->routeIs('admin.settings.*')">
                    {{ __('Pengaturan Konten') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.profile.edit')" :active="request()->routeIs('admin.profile.*')">
                    {{ __('Pengaturan Akun') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t-4 border-black">
            <div class="px-4 py-2.5 bg-yellow-50 border-b-2 border-black">
                <div class="font-black text-sm uppercase tracking-wider text-black">{{ Auth::user()->name }}</div>
                <div class="font-bold text-[10px] text-gray-600">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                @if(auth()->user()->role === 'admin')
                    <x-responsive-nav-link :href="route('admin.profile.edit')">
                        {{ __('Pengaturan Akun') }}
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
