<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Edit Foto Galeri') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">

                    <!-- Header Section -->
                    <div class="mb-6 border-b-4 border-black dark:border-white pb-4">
                        <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <span>✏️</span> Form Edit Galeri
                        </h3>
                        <p class="admin-subtitle">Perbarui detail foto galeri di bawah ini.</p>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="{{ route('admin.gallery.update', $gallery->id) }}" enctype="multipart/form-data" class="space-y-6">
                        @method('PUT')
                        @include('admin.gallery.partials.form', ['gallery' => $gallery])
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
