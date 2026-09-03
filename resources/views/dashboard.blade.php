<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="p-6 text-black dark:text-white font-bold text-sm">
                    {{ __("Anda telah berhasil masuk ke sistem!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
