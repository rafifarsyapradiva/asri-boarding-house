<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Pengaturan Akun') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Success / Error Notification -->
            <x-flash-message />

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Info Section -->
                <div class="admin-card flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 bg-blue-300 text-blue-950 border-2 border-black dark:border-white rounded-none flex items-center justify-center text-xl mb-4 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            ⚙️
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100 mb-2">Informasi Akun Admin</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-4">
                            Sebagai Administrator utama (Pemilik Kost), Anda memiliki kontrol penuh atas sistem. Jaga kerahasiaan data profil, alamat email, dan kata sandi Anda.
                        </p>
                        <div class="space-y-3 pt-2 text-xs font-semibold text-slate-600 dark:text-slate-400">
                            <div class="flex items-center gap-2">
                                <span class="w-3.5 h-3.5 border border-black dark:border-white bg-blue-300"></span>
                                <span>Perubahan Email wajib berakhiran @gmail.com</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3.5 h-3.5 border border-black dark:border-white bg-emerald-300"></span>
                                <span>Nama Lengkap maksimal 100 karakter</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3.5 h-3.5 border border-black dark:border-white bg-indigo-300"></span>
                                <span>Nomor WhatsApp valid (format Indonesia)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3.5 h-3.5 border border-black dark:border-white bg-amber-300"></span>
                                <span>Kata Sandi baru minimal 8 karakter dengan huruf & angka</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 pt-4 border-t-4 border-black dark:border-white text-xs font-bold text-slate-500 dark:text-slate-400">
                        Terakhir diperbarui: {{ $user->updated_at?->diffForHumans() ?? 'Belum pernah' }}
                    </div>
                </div>

                <!-- Form Section dengan Alpine.js untuk reactive password toggle -->
                <div class="lg:col-span-2 admin-card" x-data="{ showPassword: false, showConfirmPassword: false }">
                    <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <!-- Nama Lengkap -->
                        <div>
                            <label for="nama" class="admin-label">Nama Lengkap</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                                    👤
                                </span>
                                <input id="nama" type="text" name="nama" value="{{ old('nama', $user->nama) }}" required autocomplete="name"
                                    class="admin-input !pl-10 @error('nama') !border-red-500 @enderror">
                            </div>
                            <x-input-error :messages="$errors->get('nama')" class="mt-1.5" />
                        </div>

                        <!-- Nomor WhatsApp -->
                        <div>
                            <label for="no_hp" class="admin-label">Nomor WhatsApp</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                                    📞
                                </span>
                                <input id="no_hp" type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" required autocomplete="tel"
                                    class="admin-input !pl-10 @error('no_hp') !border-red-500 @enderror">
                            </div>
                            <x-input-error :messages="$errors->get('no_hp')" class="mt-1.5" />
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="admin-label">Email Administrator (Gmail)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                                    📧
                                </span>
                                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email"
                                    class="admin-input !pl-10 @error('email') !border-red-500 @enderror">
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                        </div>

                        <hr class="border-b-4 border-black dark:border-white my-6" />

                        <!-- Password -->
                        <div>
                            <label for="password" class="admin-label">Kata Sandi Baru</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                                    🔒
                                </span>
                                <input id="password" :type="showPassword ? 'text' : 'password'" name="password" autocomplete="new-password" placeholder="Kosongkan jika tidak ingin mengubah kata sandi"
                                    class="admin-input !pl-10 !pr-10 @error('password') !border-red-500 @enderror">
                                <button type="button" @click="showPassword = !showPassword" aria-label="Toggle kata sandi baru" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition duration-150 cursor-pointer">
                                    <template x-if="!showPassword">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </template>
                                    <template x-if="showPassword">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.822 7.822L21 21m-2.228-2.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                        </svg>
                                    </template>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="admin-label">Konfirmasi Kata Sandi Baru</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                                    🔒
                                </span>
                                <input id="password_confirmation" :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" autocomplete="new-password" placeholder="Ulangi kata sandi baru"
                                    class="admin-input !pl-10 !pr-10 @error('password_confirmation') !border-red-500 @enderror">
                                <button type="button" @click="showConfirmPassword = !showConfirmPassword" aria-label="Toggle konfirmasi kata sandi" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition duration-150 cursor-pointer">
                                    <template x-if="!showConfirmPassword">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </template>
                                    <template x-if="showConfirmPassword">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.822 7.822L21 21m-2.228-2.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                        </svg>
                                    </template>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
                        </div>

                        <!-- Action Button -->
                        <div class="flex justify-end pt-4 border-t-4 border-black dark:border-white">
                            <button type="submit" class="admin-btn-primary">
                                💾 Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
