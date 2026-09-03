<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Pengaturan Akun') }}
        </h2>
    </x-slot>

    <div class="py-12">
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

            <div class="grid grid-cols-1 gap-6">
                <!-- Card 1: Informasi Profil -->
                <div class="admin-card">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form', [
                            'user'        => $user ?? auth()->user(),
                            'updateRoute' => $updateRoute ?? null
                        ])
                    </div>
                </div>

                <!-- Card 2: Perbarui Password -->
                <div class="admin-card">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <!-- Card 3: Hapus Akun -->
                <div class="admin-card border-red-500 dark:border-red-600 shadow-[6px_6px_0px_0px_#ef4444] bg-red-50/10 dark:bg-red-950/5">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form', [
                            'isDeletable' => $isDeletable ?? true,
                            'deleteRoute' => $deleteRoute ?? null
                        ])
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
