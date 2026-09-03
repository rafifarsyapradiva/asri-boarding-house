<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Edit Peraturan Kost') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">

                    <!-- Header Section -->
                    <div class="mb-6 border-b-4 border-black dark:border-white pb-4">
                        <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <span>✏️</span> Edit Peraturan Kost
                        </h3>
                        <p class="admin-subtitle">Silakan sesuaikan isi detail aturan kost agar perubahan langsung diterapkan pada halaman tata tertib penyewa.</p>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="{{ route('admin.peraturan.update', $peraturan->id) }}" class="space-y-6">
                        @method('PUT')
                        @include('admin.peraturan._form')
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
