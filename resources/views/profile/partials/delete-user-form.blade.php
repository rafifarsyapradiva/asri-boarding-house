@props([
    'isDeletable' => true,
    'deleteRoute' => null,
])

@php
    $actionUrl = $deleteRoute ?? (request()->routeIs('penyewa.*') ? route('penyewa.profile.destroy') : route('profile.destroy'));
@endphp

<section class="space-y-6">
    <div class="mb-6 border-b-4 border-red-500 dark:border-red-600 pb-3">
        <h3 class="text-lg font-black text-red-600 dark:text-red-400 uppercase tracking-wide flex items-center gap-2">
            ⚠️ {{ __('Hapus Akun') }}
        </h3>
        <p class="text-xs text-red-700 dark:text-red-300 mt-1 block font-bold">{{ __('Setelah akun Anda dihapus, semua data dan sumber dayanya akan dihapus secara permanen.') }}</p>
    </div>

    @if (!$isDeletable)
        <div class="p-4 bg-amber-100 dark:bg-amber-950/40 border-4 border-amber-400 text-amber-900 dark:text-amber-200 font-extrabold text-xs shadow-[3px_3px_0px_0px_#000000] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.15)]">
            ⚠️ {{ __('Akun Anda tidak dapat dihapus secara mandiri karena memiliki riwayat sewa atau reservasi aktif/nonaktif di Asri Boarding House. Silakan hubungi pengelola kost jika Anda ingin menonaktifkan akun Anda secara permanen.') }}
        </div>
    @else
        <button
            type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="admin-btn-danger"
        >
            🗑️ {{ __('Hapus Akun') }}
        </button>

        <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
            <form method="post" action="{{ $actionUrl }}" class="p-6">
                @csrf
                @method('delete')

                <h2 class="text-base font-extrabold text-slate-800 dark:text-slate-100 mb-2">
                    {{ __('Apakah Anda yakin ingin menghapus akun Anda?') }}
                </h2>

                <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold mb-4">
                    {{ __('Setelah akun Anda dihapus, semua data dan sumber dayanya akan dihapus secara permanen. Silakan masukkan kata sandi Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun Anda secara permanen.') }}
                </p>

                <div class="mt-6">
                    <label for="password" class="admin-label sr-only">{{ __('Kata Sandi') }}</label>

                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="admin-input mt-1 w-3/4"
                        placeholder="{{ __('Kata Sandi') }}"
                        required
                    />

                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close')" class="admin-btn-secondary">
                        ❌ {{ __('Batal') }}
                    </button>

                    <button type="submit" class="admin-btn-danger">
                        🗑️ {{ __('Hapus Akun') }}
                    </button>
                </div>
            </form>
        </x-modal>
    @endif
</section>
