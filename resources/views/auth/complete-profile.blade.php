<x-guest-layout>
    @section('title', 'Lengkapi Profil')

    <div class="mb-6 text-center lg:text-left">
        <h2 class="text-2xl font-black text-black uppercase tracking-tight">Lengkapi Profil</h2>
        <p class="text-xs text-gray-600 mt-1.5 font-bold uppercase tracking-wide">Nomor WhatsApp diperlukan untuk menerima notifikasi tagihan.</p>
    </div>

    <div class="mb-6 p-4 bg-yellow-100 border-4 border-black text-black text-xs font-bold uppercase tracking-wide leading-relaxed">
        {{ __('Sebelum dapat melanjutkan transaksi atau mengakses dashboard, Anda wajib melengkapi nomor WhatsApp aktif terlebih dahulu.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Validation Block Alert -->
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
        </div>
    @endif

    <form method="POST" action="{{ route('profil.complete') }}" class="space-y-5" data-testid="form-complete-profile">
        @csrf

        <!-- WhatsApp Number -->
        <div>
            <label for="no_hp" class="block text-[11px] font-black uppercase tracking-wider text-black mb-1.5">Nomor WhatsApp Aktif</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-black">
                    📞
                </span>
                <input id="no_hp" type="text" name="no_hp" value="{{ old('no_hp', auth()->user()->display_no_hp) }}" placeholder="Contoh: 081234567890" required autofocus
                    class="w-full border-4 border-black bg-white focus:bg-yellow-50 pl-10 pr-4 py-3.5 text-xs font-bold text-black outline-none transition duration-150 focus:ring-4 focus:ring-yellow-400 placeholder-gray-500"
                    data-testid="input-no-hp">
            </div>
        </div>

        <!-- NIK -->
        <div>
            <label for="nik" class="block text-[11px] font-black uppercase tracking-wider text-black mb-1.5">NIK Calon Penyewa (16 Digit)</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-black">
                    🪪
                </span>
                <input id="nik" type="text" name="nik" value="{{ old('nik', auth()->user()->nik) }}" placeholder="Contoh: 320102XXXXXXXXXX" required maxlength="16" minlength="16"
                    class="w-full border-4 border-black bg-white focus:bg-yellow-50 pl-10 pr-4 py-3.5 text-xs font-bold text-black outline-none transition duration-150 focus:ring-4 focus:ring-yellow-400 placeholder-gray-500"
                    data-testid="input-nik">
            </div>
        </div>

        <!-- Nama Wali -->
        <div>
            <label for="nama_wali" class="block text-[11px] font-black uppercase tracking-wider text-black mb-1.5">Nama Wali / Orang Tua</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-black">
                    👤
                </span>
                <input id="nama_wali" type="text" name="nama_wali" value="{{ old('nama_wali', auth()->user()->nama_wali) }}" placeholder="Nama Wali Lengkap" required
                    class="w-full border-4 border-black bg-white focus:bg-yellow-50 pl-10 pr-4 py-3.5 text-xs font-bold text-black outline-none transition duration-150 focus:ring-4 focus:ring-yellow-400 placeholder-gray-500"
                    data-testid="input-nama-wali">
            </div>
        </div>

        <!-- No HP Wali -->
        <div>
            <label for="no_wali" class="block text-[11px] font-black uppercase tracking-wider text-black mb-1.5">No HP Wali / Orang Tua</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-black">
                    📞
                </span>
                <input id="no_wali" type="text" name="no_wali" value="{{ old('no_wali', auth()->user()->no_wali) }}" placeholder="Contoh: 081234567890" required
                    class="w-full border-4 border-black bg-white focus:bg-yellow-50 pl-10 pr-4 py-3.5 text-xs font-bold text-black outline-none transition duration-150 focus:ring-4 focus:ring-yellow-400 placeholder-gray-500"
                    data-testid="input-no-wali">
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" data-testid="btn-submit-complete-profile" class="w-full bg-yellow-400 hover:bg-yellow-300 text-black border-4 border-black font-black py-3.5 px-4 neo-btn-shadow neo-btn-interactive text-xs uppercase tracking-wider flex items-center justify-center gap-2">
                <span>Simpan & Lanjutkan →</span>
            </button>
        </div>
    </form>
</x-guest-layout>
