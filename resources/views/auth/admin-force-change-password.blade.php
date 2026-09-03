<x-guest-layout size="lg">
    @section('title', 'Wajib Ganti Password Pertama Kali')

    <div class="grid grid-cols-12 min-h-[580px] w-full">
        <!-- Left panel: Security Alert & Info -->
        <div class="col-span-12 lg:col-span-5 hidden lg:flex flex-col justify-between p-10 bg-gradient-to-br from-amber-800 via-slate-900 to-zinc-950 text-white relative overflow-hidden">
            <!-- Decorative Blobs inside left panel -->
            <div class="absolute -top-12 -left-12 w-48 h-48 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-16 -right-16 w-64 h-64 bg-zinc-700/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <!-- Branding Header -->
            <div class="flex items-center gap-3 relative z-10">
                <div class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-xl flex items-center justify-center text-lg border border-white/20">
                    ⚠️
                </div>
                <div>
                    <h3 class="font-extrabold text-white tracking-tight leading-none text-sm">Asri Boarding House</h3>
                    <span class="text-[9px] text-amber-400 font-bold tracking-widest uppercase mt-1 block">Proteksi Keamanan</span>
                </div>
            </div>

            <!-- Welcoming Info -->
            <div class="my-auto space-y-6 relative z-10">
                <h1 class="text-2xl font-black leading-tight tracking-tight text-white">
                    Ganti Kata Sandi <br>Pertama Kali.
                </h1>
                <p class="text-xs text-zinc-400 leading-relaxed font-medium">
                    Demi keamanan sistem manajemen "Antigravity", Anda diwajibkan untuk mengganti kata sandi bawaan (default) saat pertama kali login.
                </p>
                
                <div class="space-y-4 pt-2">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-lg bg-white/5 flex items-center justify-center text-xs border border-white/10 shrink-0">🔑</div>
                        <span class="text-xs font-semibold text-zinc-300">Min. 8 Karakter & Kombinasi</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-lg bg-white/5 flex items-center justify-center text-xs border border-white/10 shrink-0">🛡️</div>
                        <span class="text-xs font-semibold text-zinc-300">Mencegah Akses Tidak Sah</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Note -->
            <div class="text-[10px] text-zinc-500 font-semibold relative z-10">
                Langkah ini wajib diselesaikan untuk membuka akses penuh ke dashboard.
            </div>
        </div>

        <!-- Right panel: Form -->
        <div class="col-span-12 lg:col-span-7 p-8 lg:p-12 flex flex-col justify-center bg-white/95">
            <div>
                <!-- Form Header -->
                <div class="mb-8">
                    <h2 class="text-2xl font-black text-slate-900 leading-tight">Pengaturan Kata Sandi Baru</h2>
                    <p class="text-xs text-slate-400 mt-1.5 font-medium">Silakan tentukan kata sandi baru untuk akun administrator Anda.</p>
                </div>

                <form method="POST" action="{{ route('admin.force-change-password.update') }}" class="space-y-4" data-testid="form-force-change-password">
                    @csrf

                    <!-- Password Component -->
                    <x-auth.password-input 
                        id="password" 
                        name="password" 
                        label="Kata Sandi Baru" 
                        placeholder="Min. 8 karakter, huruf & angka"
                        autocomplete="new-password"
                    />

                    <!-- Confirm Password Component -->
                    <x-auth.password-input 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        label="Konfirmasi Kata Sandi Baru" 
                        placeholder="Ulangi kata sandi baru"
                        autocomplete="new-password"
                    />

                    <!-- Submit Button -->
                    <div class="pt-3">
                        <button type="submit" data-testid="btn-submit-force-change-password" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 px-4 rounded-xl shadow-lg transition duration-200 text-xs uppercase tracking-wider flex items-center justify-center gap-2">
                            <span>Simpan & Lanjutkan</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Logout option -->
                <div class="mt-6 text-center">
                    <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs text-slate-400 hover:text-slate-600 underline font-semibold transition">
                            Keluar (Logout)
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
