<section>
    <div class="mb-8 border-b-4 border-black dark:border-white pb-3">
        <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-wide flex items-center gap-2">
            🔑 {{ __('Perbarui Kata Sandi') }}
        </h3>
        <p class="admin-subtitle">{{ __('Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.') }}</p>
    </div>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        @php
            $fields = [
                ['id' => 'update_password_current_password', 'name' => 'current_password', 'label' => __('Kata Sandi Saat Ini'), 'autocomplete' => 'current-password', 'errorKey' => 'current_password'],
                ['id' => 'update_password_password', 'name' => 'password', 'label' => __('Kata Sandi Baru'), 'autocomplete' => 'new-password', 'errorKey' => 'password'],
                ['id' => 'update_password_password_confirmation', 'name' => 'password_confirmation', 'label' => __('Konfirmasi Kata Sandi Baru'), 'autocomplete' => 'new-password', 'errorKey' => 'password_confirmation'],
            ];
        @endphp

        @foreach ($fields as $field)
            <div x-data="{ show: false }">
                <label for="{{ $field['id'] }}" class="admin-label">{{ $field['label'] }}</label>
                <div class="relative mt-1">
                    <input 
                        id="{{ $field['id'] }}" 
                        name="{{ $field['name'] }}" 
                        :type="show ? 'text' : 'password'" 
                        class="admin-input !pr-10" 
                        autocomplete="{{ $field['autocomplete'] }}" 
                        required
                    >
                    <button 
                        type="button" 
                        @click="show = !show" 
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition duration-150 cursor-pointer"
                        :aria-label="show ? '{{ __('Sembunyikan kata sandi') }}' : '{{ __('Tampilkan kata sandi') }}'"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path x-show="!show" stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path x-show="!show" stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            <path x-show="show" x-cloak stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.822 7.822L21 21m-2.228-2.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->updatePassword->get($field['errorKey'])" class="mt-2" />
            </div>
        @endforeach

        <div class="flex items-center gap-4">
            <button type="submit" class="admin-btn-primary">
                💾 {{ __('Simpan') }}
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-xs font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-1"
                >
                    <span>✨</span> {{ __('Berhasil disimpan.') }}
                </p>
            @endif
        </div>
    </form>
</section>
