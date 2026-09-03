<x-guest-layout size="lg">
    @section('title', 'Atur Ulang Kata Sandi Portal Penyewa')

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
                    Atur Ulang <br>
                    <span class="bg-yellow-400 text-black px-2 py-1 inline-block -rotate-1 border-2 border-black mt-2">Kata Sandi Baru.</span>
                </h1>
                <p class="text-xs text-gray-300 leading-relaxed font-bold">
                    Silakan masukkan kata sandi baru Anda yang kuat dan aman. Setelah berhasil diatur ulang, Anda dapat langsung masuk kembali ke portal penyewa.
                </p>
                
                <div class="space-y-4 pt-2">
                    <div class="flex items-center gap-3.5">
                        <span class="text-lg">🔒</span>
                        <span class="text-xs font-black uppercase tracking-wider text-yellow-400">Kata Sandi Baru yang Aman</span>
                    </div>
                    <div class="flex items-center gap-3.5">
                        <span class="text-lg">🔑</span>
                        <span class="text-xs font-black uppercase tracking-wider text-yellow-400">Langsung Masuk Portal Penyewa</span>
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
                    <h2 class="text-3xl font-black text-black dark:text-white uppercase tracking-tight">Atur Ulang Kata Sandi</h2>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1.5 font-bold uppercase tracking-wide">Buat kata sandi baru untuk mengamankan akun portal Anda.</p>
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

                <form method="POST" action="{{ route('penyewa.password.update') }}" class="space-y-4" id="resetForm" data-testid="form-penyewa-reset-password">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $token }}">

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-[11px] font-black uppercase tracking-wider text-black dark:text-white mb-2">Alamat Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-black dark:text-white">
                                ✉️
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required 
                                {{ $email ? 'readonly' : '' }}
                                class="w-full border-4 border-black dark:border-white bg-white dark:bg-slate-900 focus:bg-yellow-50 dark:focus:bg-slate-800 pl-10 pr-4 py-3.5 text-xs font-bold text-black dark:text-white outline-none transition duration-150 focus:ring-4 focus:ring-yellow-400 placeholder-gray-500 shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] {{ $email ? 'bg-gray-100 dark:bg-slate-800 cursor-not-allowed' : '' }}"
                                data-testid="input-email">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                    </div>

                    <!-- Password Component -->
                    <x-auth.password-input 
                        id="password" 
                        name="password" 
                        label="Kata Sandi Baru" 
                        placeholder="••••••••"
                        autocomplete="new-password"
                    />

                    <!-- Confirm Password Component -->
                    <x-auth.password-input 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        label="Konfirmasi Kata Sandi Baru" 
                        placeholder="••••••••"
                        autocomplete="new-password"
                    />

                    <!-- Submit Button -->
                    <div class="pt-3">
                        <button type="submit" data-testid="btn-submit-penyewa-reset-password" class="w-full bg-yellow-400 hover:bg-yellow-300 text-black border-4 border-black dark:border-white font-black py-3.5 px-4 neo-btn-shadow neo-btn-interactive text-xs uppercase tracking-wider flex items-center justify-center gap-2 cursor-pointer">
                            <span>Simpan Kata Sandi Baru →</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
