<x-guest-layout>
    @section('title', 'Konfirmasi Kata Sandi')

    <div class="mb-6 p-4 bg-yellow-100 dark:bg-yellow-900/30 border-4 border-black dark:border-white text-black dark:text-white">
        <div class="font-black uppercase tracking-wider text-xs flex items-center gap-1.5 mb-1">
            <span>🔒</span> Area Terproteksi
        </div>
        <p class="text-xs font-bold leading-relaxed">
            {{ __('Ini adalah area terproteksi sistem. Harap konfirmasi kata sandi Anda sebelum melanjutkan.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4" data-testid="form-confirm-password">
        @csrf

        <!-- Password Component -->
        <x-auth.password-input 
            id="password" 
            name="password" 
            label="Kata Sandi" 
            placeholder="••••••••"
            autocomplete="current-password"
        />

        <div class="pt-2">
            <button type="submit" data-testid="btn-submit-confirm-password" class="w-full bg-yellow-400 hover:bg-yellow-300 text-black border-4 border-black dark:border-white font-black py-3 px-4 neo-btn-shadow neo-btn-interactive text-xs uppercase tracking-wider flex items-center justify-center gap-2 cursor-pointer">
                <span>{{ __('Konfirmasi Kata Sandi →') }}</span>
            </button>
        </div>
    </form>
</x-guest-layout>
