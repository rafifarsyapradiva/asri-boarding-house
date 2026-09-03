<x-guest-layout>
    @section('title', 'Verifikasi Email')

    <div class="mb-6 p-4 bg-yellow-100 dark:bg-yellow-900/30 border-4 border-black dark:border-white text-black dark:text-white">
        <div class="font-black uppercase tracking-wider text-xs flex items-center gap-1.5 mb-1">
            <span>✉️</span> Verifikasi Email
        </div>
        <p class="text-xs font-bold leading-relaxed">
            {{ __('Terima kasih telah mendaftar! Sebelum memulai, mohon verifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan ke email Anda. Jika Anda tidak menerima email tersebut, kami dengan senang hati akan mengirimkan ulang.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/30 border-4 border-black dark:border-white text-black dark:text-white">
            <div class="font-black uppercase tracking-wider text-xs flex items-center gap-1.5 mb-1">
                <span>🎉</span> Email Terkirim
            </div>
            <p class="text-xs font-bold leading-relaxed">
                {{ __('Tautan verifikasi baru telah dikirim ke alamat email yang Anda berikan saat pendaftaran.') }}
            </p>
        </div>
    @endif

    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}" class="w-full sm:w-auto" data-testid="form-resend-verification">
            @csrf

            <button type="submit" data-testid="btn-resend-verification" class="w-full sm:w-auto bg-yellow-400 hover:bg-yellow-300 text-black border-4 border-black dark:border-white font-black py-2.5 px-4 neo-btn-shadow neo-btn-interactive text-xs uppercase tracking-wider flex items-center justify-center gap-2 cursor-pointer">
                <span>{{ __('Kirim Ulang Email') }}</span>
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto" data-testid="form-logout">
            @csrf

            <button type="submit" class="w-full sm:w-auto bg-white dark:bg-slate-800 text-black dark:text-white border-2 border-black dark:border-white font-black py-2 px-4 neo-shadow-sm hover:translate-x-[1px] hover:translate-y-[1px] text-xs uppercase tracking-wider transition-all cursor-pointer text-center">
                {{ __('Keluar') }}
            </button>
        </form>
    </div>
</x-guest-layout>
