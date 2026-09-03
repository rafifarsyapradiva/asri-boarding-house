<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Ubah Review Pelanggan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">

                    <!-- Header Section -->
                    <div class="mb-6 border-b border-slate-100 dark:border-slate-700/50 pb-4">
                        <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <span>✏️</span> Edit Ulasan Pelanggan
                        </h3>
                        <p class="admin-subtitle">Silakan perbarui data ulasan di bawah ini.</p>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="{{ route('admin.reviews.update', $review->id) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Include Shared Form Partial -->
                        @include('admin.reviews.partials.form', ['review' => $review])

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-700/50">
                            <a href="{{ route('admin.reviews.index') }}" class="admin-btn-secondary">
                                Batal
                            </a>
                            <button type="submit" class="admin-btn-primary">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
