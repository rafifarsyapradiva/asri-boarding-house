@props([
    'user' => null,
    'updateRoute' => null,
])

@php
    $user = $user ?? auth()->user();
    $route = $updateRoute ?? (request()->routeIs('penyewa.*') ? route('penyewa.profile.update') : route('profile.update'));
@endphp

<section>
    <div class="mb-8 border-b-4 border-black dark:border-white pb-3">
        <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase tracking-wide flex items-center gap-2">
            👤 {{ __('Informasi Profil') }}
        </h3>
        <p class="admin-subtitle">{{ __('Perbarui informasi profil dan alamat email akun Anda.') }}</p>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ $route }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <label for="nama" class="admin-label">{{ __('Nama Lengkap') }}</label>
            <input id="nama" name="nama" type="text" class="admin-input mt-1" value="{{ old('nama', $user->nama) }}" required autofocus autocomplete="name">
            <x-input-error class="mt-2" :messages="$errors->get('nama')" />
        </div>

        <div>
            <label for="email" class="admin-label">{{ __('Alamat Email') }}</label>
            <input id="email" name="email" type="email" class="admin-input mt-1" value="{{ old('email', $user->email) }}" required autocomplete="username">
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-4 p-4 border-2 border-dashed border-yellow-400 bg-yellow-50 dark:bg-yellow-950/20 text-yellow-900 dark:text-yellow-200">
                    <p class="text-xs font-bold">
                        {{ __('Alamat email Anda belum terverifikasi.') }}

                        <button form="send-verification" type="submit" class="underline text-xs font-black hover:text-yellow-600 dark:hover:text-yellow-300">
                            {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-black text-xs text-emerald-600 dark:text-emerald-400">
                            {{ __('Tautan verifikasi baru telah dikirim ke alamat email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <label for="no_hp" class="admin-label">{{ __('Nomor WhatsApp') }}</label>
            <input id="no_hp" name="no_hp" type="text" class="admin-input mt-1" value="{{ old('no_hp', $user->no_hp) }}" required autocomplete="tel">
            <x-input-error class="mt-2" :messages="$errors->get('no_hp')" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="admin-btn-primary">
                💾 {{ __('Simpan') }}
            </button>

            @if (session('status') === 'profile-updated')
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
