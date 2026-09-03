<x-guest-layout size="lg">
    @section('title', 'Lupa Kata Sandi')

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
                    Lupa <br>
                    <span class="bg-yellow-400 text-black px-2 py-1 inline-block -rotate-1 border-2 border-black mt-2">Kata Sandi.</span>
                </h1>
                <p class="text-xs text-gray-300 leading-relaxed font-bold">
                    Masukkan alamat email Anda dan kami akan mengirimkan email berisi tautan pengaturan ulang kata sandi baru untuk akun Reservasi Anda.
                </p>
                
                <div class="space-y-4 pt-2">
                    <div class="flex items-center gap-3.5">
                        <span class="text-lg">✉️</span>
                        <span class="text-xs font-black uppercase tracking-wider text-yellow-400">Tautan Reset Aman</span>
                    </div>
                    <div class="flex items-center gap-3.5">
                        <span class="text-lg">🛡️</span>
                        <span class="text-xs font-black uppercase tracking-wider text-yellow-400">Proses Cepat & Otomatis</span>
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
                    <h2 class="text-3xl font-black text-black uppercase tracking-tight">Lupa Sandi</h2>
                    <p class="text-xs text-gray-600 mt-1.5 font-bold uppercase tracking-wide">Masukkan email terdaftar untuk mendapatkan tautan reset.</p>
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

                <!-- Session Status (Email reset sent success) -->
                @if (session('status'))
                    <div class="mb-6 p-4 bg-green-100 border-4 border-black text-black relative" id="alertSuccess">
                        <div class="font-black uppercase tracking-wider text-xs flex items-center gap-1.5 mb-1">
                            <span>🎉</span> Sukses!
                        </div>
                        <p class="text-xs font-bold">{{ session('status') }}</p>
                        <button type="button" onclick="document.getElementById('alertSuccess').remove()" class="absolute top-3 right-3 text-black hover:text-green-800 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5" data-testid="form-forgot-password">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-[11px] font-black uppercase tracking-wider text-black mb-2">Alamat Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-black">
                                ✉️
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@example.com"
                                class="w-full border-4 border-black bg-white focus:bg-yellow-50 pl-10 pr-4 py-3.5 text-xs font-bold text-black outline-none transition duration-150 focus:ring-4 focus:ring-yellow-400 placeholder-gray-500"
                                data-testid="input-email">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                    </div>

                    <div class="pt-3">
                        <button type="submit" data-testid="btn-submit-forgot-password" class="w-full bg-yellow-400 hover:bg-yellow-300 text-black border-4 border-black font-black py-3.5 px-4 neo-btn-shadow neo-btn-interactive text-xs uppercase tracking-wider flex items-center justify-center gap-2">
                            <span>Kirim Tautan Reset →</span>
                        </button>
                    </div>

                    <!-- Back to Login Link -->
                    <div class="text-center pt-2">
                        <a href="{{ route('reservasi.login') }}" class="text-xs font-black uppercase tracking-wider text-black hover:underline">
                            Kembali ke Halaman Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
