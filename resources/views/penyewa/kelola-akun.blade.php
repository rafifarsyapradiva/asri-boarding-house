<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Kelola Akun Saya') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-screen" x-data="{ 
        activeTab: new URLSearchParams(window.location.search).get('tab') || 'profile',
        setTab(tab) {
            this.activeTab = tab;
            const url = new URL(window.location);
            url.searchParams.set('tab', tab);
            window.history.pushState({}, '', url);
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Success Status Alert -->
            @if(session('status') === 'profile-updated' || session('status') === 'password-updated')
                <div 
                    role="alert"
                    aria-live="polite"
                    class="p-4 bg-emerald-300 dark:bg-emerald-950 text-black dark:text-white border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] font-extrabold flex items-center gap-2"
                >
                    <span class="text-lg" aria-hidden="true">✅</span>
                    <span class="text-sm">
                        {{ session('status') === 'profile-updated' 
                            ? __('Informasi profil berhasil diperbarui!') 
                            : __('Kata sandi akun berhasil diperbarui!') }}
                    </span>
                </div>
            @endif

            @if(session('error'))
                <div 
                    role="alert"
                    aria-live="polite"
                    class="p-4 bg-red-300 dark:bg-red-950 text-black dark:text-white border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] font-extrabold flex items-center gap-2"
                >
                    <span class="text-lg" aria-hidden="true">❌</span>
                    <span class="text-sm">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Brutalist Tabs Navigation -->
            <div class="flex flex-wrap gap-3 border-b-4 border-black dark:border-white pb-4">
                <button @click="setTab('profile')" 
                        :class="activeTab === 'profile' ? 'bg-yellow-400 text-black border-4 border-black shadow-[4px_4px_0px_0px_#000000] font-black' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)] hover:translate-y-[-1px] font-bold'" 
                        class="px-5 py-3 text-sm uppercase tracking-wider transition-all duration-150 flex items-center gap-2 rounded-none cursor-pointer">
                    👤 {{ __('Profil Saya') }}
                </button>
                <button @click="setTab('password')" 
                        :class="activeTab === 'password' ? 'bg-yellow-400 text-black border-4 border-black shadow-[4px_4px_0px_0px_#000000] font-black' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)] hover:translate-y-[-1px] font-bold'" 
                        class="px-5 py-3 text-sm uppercase tracking-wider transition-all duration-150 flex items-center gap-2 rounded-none cursor-pointer">
                    🔑 {{ __('Ubah Password') }}
                </button>
            </div>

            <!-- Tab Content 1: Profil Saya -->
            <div x-show="activeTab === 'profile'" x-cloak class="space-y-6">
                <!-- Card 1: Informasi Profil -->
                <div class="admin-card">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form', [
                            'user'        => $user ?? auth()->user(),
                            'updateRoute' => $updateRoute ?? null
                        ])
                    </div>
                </div>

                <!-- Card 2: Hapus Akun -->
                <div class="admin-card border-red-500 dark:border-red-600 shadow-[6px_6px_0px_0px_#ef4444] bg-red-50/10 dark:bg-red-950/5">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form', [
                            'isDeletable' => $isDeletable ?? true,
                            'deleteRoute' => $deleteRoute ?? null
                        ])
                    </div>
                </div>
            </div>

            <!-- Tab Content 3: Ubah Password -->
            <div x-show="activeTab === 'password'" x-cloak class="space-y-6">
                <!-- Card 1: Perbarui Password -->
                <div class="admin-card">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
