<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Tambah Foto Galeri Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">

                    <!-- Header Section -->
                    <div class="mb-6 border-b-4 border-black dark:border-white pb-4">
                        <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <span>🖼️</span> Form Galeri Baru
                        </h3>
                        <p class="admin-subtitle">Silakan isi detail foto galeri untuk dipublikasikan di halaman depan.</p>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @include('admin.gallery.partials.form')
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
