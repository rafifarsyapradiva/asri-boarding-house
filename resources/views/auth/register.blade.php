<x-guest-layout size="lg">
    @section('title', 'Daftar Akun')

    <div class="grid grid-cols-12 min-h-[600px] w-full rounded-none overflow-hidden border-4 border-black dark:border-white">
        <!-- Left panel: Split Design (Solid Black & Yellow accents) -->
        <div class="col-span-12 lg:col-span-5 hidden lg:flex flex-col justify-between p-10 bg-black text-white relative overflow-hidden border-r-4 border-black dark:border-white">
            <!-- Branding Header -->
            <div class="flex items-center gap-3 relative z-10">
                <div class="px-3.5 py-1.5 bg-yellow-400 text-black border-2 border-black font-black uppercase tracking-wider text-xs shadow-[2px_2px_0px_0px_#000000]">
                    {{ \App\Models\Setting::get('logo_icon', '🏡') }} {{ \App\Models\Setting::get('logo_text', 'Asri Boarding House') }}
                </div>
            </div>

            <!-- Welcoming Info -->
            <div class="my-auto space-y-6 relative z-10">
                <h1 class="text-3xl sm:text-4xl font-black leading-none tracking-tight text-white uppercase font-sans">
                    Bergabung <br>
                    <span class="bg-yellow-400 text-black px-2 py-1 inline-block -rotate-1 border-2 border-black mt-2">Bersama Kami.</span>
                </h1>
                <p class="text-xs text-gray-300 leading-relaxed font-bold">
                    Daftar akun sekarang untuk mulai memesan kamar kost. Akses penuh ke booking online instan, riwayat tagihan, serta notifikasi WhatsApp real-time.
                </p>
                
                <div class="space-y-4 pt-2">
                    <div class="flex items-center gap-3.5">
                        <span class="text-lg">📝</span>
                        <span class="text-xs font-black uppercase tracking-wider text-yellow-400">Proses Booking Cepat & Instan</span>
                    </div>
                    <div class="flex items-center gap-3.5">
                        <span class="text-lg">🔔</span>
                        <span class="text-xs font-black uppercase tracking-wider text-yellow-400">Notifikasi WA & Email Tagihan</span>
                    </div>
                    <div class="flex items-center gap-3.5">
                        <span class="text-lg">🛡️</span>
                        <span class="text-xs font-black uppercase tracking-wider text-yellow-400">Sistem Keamanan Data Terjaga</span>
                    </div>
                </div>
            </div>

            <!-- Footer Notes -->
            <div class="text-[10px] text-gray-500 font-black tracking-widest uppercase">
                © {{ date('Y') }} {{ \App\Models\Setting::get('logo_text', 'Asri Boarding House') }}
            </div>
        </div>

        <!-- Right panel: Form -->
        <div class="col-span-12 lg:col-span-7 p-8 sm:p-12 flex flex-col justify-center bg-white dark:bg-slate-900">
            <div class="w-full max-w-md mx-auto">
                <!-- Form Header -->
                <div class="mb-8 text-center lg:text-left">
                    <h2 class="text-3xl font-black text-black dark:text-white uppercase tracking-tight">Daftar Akun Baru</h2>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1.5 font-bold uppercase tracking-wide">Buat akun untuk melakukan reservasi kamar kost.</p>
                </div>

                <!-- Global Validation Block Alert -->
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-yellow-100 border-4 border-black text-black relative" id="alertError">
                        <div class="font-black uppercase tracking-wider text-xs flex items-center gap-1.5 mb-2">
                            <span>⚠️</span> Terjadi kesalahan:
                        </div>
                        <ul class="list-disc pl-5 space-y-1 text-xs font-bold">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" onclick="document.getElementById('alertError').remove()" class="absolute top-3 right-3 text-black hover:text-red-600 transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endif

                <!-- Google OAuth Component -->
                <div class="mb-6">
                    <x-auth.google-button :route="route('auth.google')" text="Daftar dengan Google" />
                </div>

                <!-- Divider -->
                <div class="relative flex items-center justify-center my-6">
                    <span class="absolute inset-x-0 h-1 bg-black dark:bg-white"></span>
                    <span class="relative bg-white dark:bg-slate-900 px-3 text-[10px] text-black dark:text-white font-black uppercase tracking-wider">ATAU DAFTAR MANUAL</span>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-4" id="registerForm" data-testid="form-register">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-[11px] font-black uppercase tracking-wider text-black dark:text-white mb-2">Nama Lengkap</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-black dark:text-white">
                                👤
                            </span>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Nama Lengkap Anda"
                                class="w-full border-4 border-black dark:border-white bg-white dark:bg-slate-900 focus:bg-yellow-50 dark:focus:bg-slate-800 pl-10 pr-4 py-3.5 text-xs font-bold text-black dark:text-white outline-none transition duration-150 focus:ring-4 focus:ring-yellow-400 placeholder-gray-500 shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff]"
                                data-testid="input-name">
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-[11px] font-black uppercase tracking-wider text-black dark:text-white mb-2">Alamat Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-black dark:text-white">
                                ✉️
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="name@example.com"
                                class="w-full border-4 border-black dark:border-white bg-white dark:bg-slate-900 focus:bg-yellow-50 dark:focus:bg-slate-800 pl-10 pr-4 py-3.5 text-xs font-bold text-black dark:text-white outline-none transition duration-150 focus:ring-4 focus:ring-yellow-400 placeholder-gray-500 shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff]"
                                data-testid="input-email">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                    </div>

                    <!-- Password Component -->
                    <x-auth.password-input 
                        id="password" 
                        name="password" 
                        label="Kata Sandi" 
                        placeholder="Min. 8 karakter"
                        autocomplete="new-password"
                    />

                    <!-- Confirm Password Component -->
                    <x-auth.password-input 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        label="Konfirmasi Kata Sandi" 
                        placeholder="Ulangi kata sandi"
                        autocomplete="new-password"
                    />

                    <div class="flex items-center justify-between pt-1">
                        <a class="text-xs font-black uppercase tracking-wider text-black dark:text-yellow-400 hover:underline" href="{{ route('login') }}">
                            Sudah punya akun? Masuk
                        </a>
                    </div>

                    <!-- Register Button -->
                    <div class="pt-3">
                        <button type="submit" data-testid="btn-submit-register" class="w-full bg-yellow-400 hover:bg-yellow-300 text-black border-4 border-black dark:border-white font-black py-3.5 px-4 neo-btn-shadow neo-btn-interactive text-xs uppercase tracking-wider flex items-center justify-center gap-2 cursor-pointer">
                            <span>Daftar Akun Sekarang →</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
