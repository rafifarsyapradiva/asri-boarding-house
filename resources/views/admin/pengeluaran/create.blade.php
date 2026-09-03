<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Tambah Pengeluaran Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">
                    
                    <div class="mb-8 flex justify-between items-center border-b-2 border-black dark:border-white pb-4">
                        <div>
                            <h3 class="text-lg font-black text-black dark:text-white uppercase tracking-wider">Form Catat Pengeluaran</h3>
                            <p class="admin-subtitle">Masukkan detail pengeluaran operasional atau maintenance untuk pembukuan kas.</p>
                        </div>
                        <a href="{{ route('admin.pengeluaran.index') }}" class="admin-btn-secondary !py-1 px-3 !text-xs">&larr; Kembali</a>
                    </div>

                    <!-- Display Global Validation Errors -->
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-500 text-white border-4 border-black dark:border-white rounded-none shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                            <div class="font-black text-sm mb-2 uppercase tracking-wider">Terjadi kesalahan input:</div>
                            <ul class="list-disc pl-5 text-xs space-y-1 font-bold">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.pengeluaran.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @include('admin.pengeluaran._form')

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-4 mt-8">
                            <a href="{{ route('admin.pengeluaran.index') }}" class="admin-btn-secondary">
                                Batal
                            </a>
                            <button type="submit" class="admin-btn-primary">
                                Simpan Pengeluaran
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
