<x-guest-layout size="lg">
    @section('title', 'Masuk Portal Reservasi')

    <div class="grid grid-cols-12 min-h-[600px] w-full rounded-none overflow-hidden border-4 border-black">
        <!-- Left panel: Split Design (Solid Black & Yellow accents) -->
        <div class="col-span-12 lg:col-span-5 hidden lg:flex flex-col justify-between p-10 bg-black text-white relative overflow-hidden border-r-4 border-black">
            <!-- Branding Header -->
            <div class="flex items-center gap-3 relative z-10">
                <div class="px-3.5 py-1.5 bg-yellow-400 text-black border-2 border-black font-black uppercase tracking-wider text-xs shadow-[2px_2px_0px_0px_#000000]">
                    {{ \App\Models\Setting::get('logo_icon', '🏡') }} {{ \App\Models\Setting::get('logo_text', 'Asri Boarding House') }}
                </div>
            </div>

            <!-- Welcoming Info -->
            <div class="my-auto space-y-6 relative z-10">
                <h1 class="text-3xl sm:text-4xl font-black leading-none tracking-tight text-white uppercase font-sans">
                    Sistem Reservasi <br>
                    <span class="bg-yellow-400 text-black px-2 py-1 inline-block -rotate-1 border-2 border-black mt-2">Kamar Online.</span>
                </h1>
                <p class="text-xs text-gray-300 leading-relaxed font-bold">
                    Masuk ke portal reservasi untuk memilih kamar kost idaman Anda, melacak pembayaran Midtrans, dan mengelola pesanan Anda kapan saja.
                </p>
                
                <div class="space-y-4 pt-2">
                    <div class="flex items-center gap-3.5">
                        <span class="text-lg">📶</span>
                        <span class="text-xs font-black uppercase tracking-wider text-yellow-400">WiFi Cepat & Listrik Gratis</span>
                    </div>
                    <div class="flex items-center gap-3.5">
                        <span class="text-lg">💳</span>
                        <span class="text-xs font-black uppercase tracking-wider text-yellow-400">Integrasi Midtrans Realtime</span>
                    </div>
                </div>
            </div>

            <!-- Footer Notes -->
            <div class="text-[10px] text-gray-500 font-black tracking-widest uppercase">
                © {{ date('Y') }} {{ \App\Models\Setting::get('logo_text', 'Asri Boarding House') }}
            </div>
        </div>

        <!-- Right panel: Form -->
        <div class="col-span-12 lg:col-span-7 p-8 sm:p-12 flex flex-col justify-center bg-white">
            <div class="w-full max-w-md mx-auto">
                <!-- Form Header -->
                <div class="mb-8 text-center lg:text-left">
                    <h2 class="text-3xl font-black text-black uppercase tracking-tight">Selamat Datang</h2>
                    <p class="text-xs text-gray-600 mt-1.5 font-bold uppercase tracking-wide">Masuk untuk memesan kamar kost impian Anda secara instan.</p>
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
                        <button type="button" onclick="document.getElementById('alertError').remove()" class="absolute top-3 right-3 text-black hover:text-red-600 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endif

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <!-- Google Sign-in Component (Neobrutalism Theme) -->
                <div class="mb-6">
                    <x-auth.google-button :route="route('auth.google', ['from' => 'reservasi'])" text="Masuk dengan Google" theme="neobrutalism" />
                </div>

                <!-- Divider -->
                <div class="relative flex items-center justify-center my-6">
                    <span class="absolute inset-x-0 h-1 bg-black"></span>
                    <span class="relative bg-white px-3 text-[10px] text-black font-black uppercase tracking-wider">ATAU MASUK SECARA MANUAL</span>
                </div>

                <!-- Manual Login Form -->
                <form method="POST" action="{{ route('reservasi.login') }}" class="space-y-5" id="loginForm" data-testid="form-reservasi-login">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-[11px] font-black uppercase tracking-wider text-black mb-2">Alamat Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-black">
                                ✉️
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="name@example.com"
                                class="w-full border-4 border-black bg-white focus:bg-yellow-50 pl-10 pr-4 py-3.5 text-xs font-bold text-black outline-none transition duration-150 focus:ring-4 focus:ring-yellow-400 placeholder-gray-500"
                                data-testid="input-reservasi-email">
                        </div>
                    </div>

                    <!-- Password Component (Neobrutalism Theme) -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="block text-[11px] font-black uppercase tracking-wider text-black">Kata Sandi</span>
                            @if (Route::has('password.request'))
                                <a class="text-[10px] font-black uppercase tracking-wider text-black hover:underline" href="{{ route('password.request') }}">
                                    Lupa sandi?
                                </a>
                            @endif
                        </div>
                        <x-auth.password-input 
                            id="password" 
                            name="password" 
                            label="" 
                            placeholder="••••••••"
                            autocomplete="current-password"
                            theme="neobrutalism"
                        />
                    </div>

                    <!-- Remember Me & Link -->
                    <div class="flex items-center justify-between pt-1">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                            <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 border-2 border-black text-black focus:ring-yellow-400 focus:ring-2 shadow-sm transition cursor-pointer" data-testid="input-remember">
                            <span class="ms-2 text-xs text-black font-black uppercase tracking-wider group-hover:text-yellow-600 transition">Ingat Saya</span>
                        </label>
                        
                        <a class="text-xs font-black uppercase tracking-wider text-black hover:underline" href="{{ route('reservasi.register') }}">
                            Daftar Akun Baru
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-3">
                        <button type="submit" data-testid="btn-submit-reservasi-login" class="w-full bg-yellow-400 hover:bg-yellow-300 text-black border-4 border-black font-black py-3.5 px-4 neo-btn-shadow neo-btn-interactive text-xs uppercase tracking-wider flex items-center justify-center gap-2">
                            <span>Masuk ke Akun →</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
