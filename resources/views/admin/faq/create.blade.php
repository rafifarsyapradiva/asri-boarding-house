<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah FAQ Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">

                    <!-- Header Section -->
                    <div class="mb-6 border-b border-slate-100 dark:border-slate-700/50 pb-4">
                        <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <span aria-hidden="true">❓</span> Form FAQ Baru
                        </h3>
                        <p class="admin-subtitle">Silakan isi detail pertanyaan dan jawaban FAQ untuk dipublikasikan.</p>
                    </div>

                    <!-- Reusable Form Partial -->
                    @include('admin.faq._form', [
                        'action' => route('admin.faq.store'),
                        'submitLabel' => 'Simpan FAQ'
                    ])

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
