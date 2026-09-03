<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Manajemen Review Pelanggan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">

                    <!-- Header Section -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100">Daftar Review Pelanggan</h3>
                            <p class="admin-subtitle">Kelola testimoni penyewa kost untuk meningkatkan kepercayaan calon penyewa baru.</p>
                        </div>
                        <a href="{{ route('admin.reviews.create') }}" class="admin-btn-primary">
                            <span class="text-sm">+</span> Tambah Review Baru
                        </a>
                    </div>

                    <!-- Search Bar -->
                    <div class="bg-slate-50 dark:bg-slate-900 border-4 border-black dark:border-white p-5 rounded-none mb-6 flex flex-wrap gap-4 items-center justify-between shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                        <form method="GET" action="{{ route('admin.reviews.index') }}" class="flex flex-wrap gap-3 items-center w-full">
                            <div class="relative flex-grow max-w-md">
                                <input type="text" name="search" id="search-input" 
                                       class="admin-input !py-2 !pl-10 w-full" 
                                       placeholder="Cari nama, pekerjaan, ulasan..."
                                       value="{{ request('search') }}">
                                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="admin-btn-primary !py-2.5 !px-5">
                                    Cari
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('admin.reviews.index') }}" class="admin-btn-secondary !py-2.5 !px-5">
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Alert Notifications -->
                    @if(session('success'))
                        <div class="mb-6 p-4 bg-emerald-300 dark:bg-emerald-950 text-black dark:text-white border-4 border-black dark:border-white rounded-none shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                            <div class="flex items-center">
                                <span class="mr-2 font-black">✅</span>
                                <span class="font-black text-sm">{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Review Table Component -->
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead class="admin-table-thead">
                                <tr>
                                    <th scope="col" class="admin-table-th">Foto</th>
                                    <th scope="col" class="admin-table-th">Nama Pelanggan</th>
                                    <th scope="col" class="admin-table-th">Pekerjaan / Status</th>
                                    <th scope="col" class="admin-table-th text-center">Rating</th>
                                    <th scope="col" class="admin-table-th">Komentar / Ulasan</th>
                                    <th scope="col" class="admin-table-th">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="admin-table-tbody">
                                @forelse($reviews as $review)
                                    <tr class="admin-table-tr">
                                        <!-- Foto (Menggunakan Model Accessor) -->
                                        <td class="admin-table-td">
                                            @if($review->foto_url)
                                                <img src="{{ $review->foto_url }}" alt="Foto {{ $review->nama }}" class="w-10 h-10 object-cover rounded-none border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]">
                                            @else
                                                <div class="w-10 h-10 rounded-none bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white border-2 border-black dark:border-white font-black text-sm shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]">
                                                    {{ $review->initials }}
                                                </div>
                                            @endif
                                        </td>
                                        <!-- Nama -->
                                        <td class="admin-table-td font-bold text-slate-800 dark:text-slate-100">
                                            {{ $review->nama }}
                                        </td>
                                        <!-- Pekerjaan / Status -->
                                        <td class="admin-table-td text-slate-500 dark:text-slate-400 font-semibold text-xs">
                                            {{ $review->pekerjaan_formatted }}
                                        </td>
                                        <!-- Rating -->
                                        <td class="admin-table-td text-center">
                                            <div class="flex items-center justify-center gap-0.5 text-amber-400">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <span class="{{ $i <= $review->bintang ? '' : 'text-slate-200 dark:text-slate-700' }}">★</span>
                                                @endfor
                                            </div>
                                        </td>
                                        <!-- Komentar / Ulasan -->
                                        <td class="admin-table-td !whitespace-normal text-slate-600 dark:text-slate-300 max-w-[300px] break-words text-xs font-medium" title="{{ $review->ulasan }}">
                                            {{ $review->ulasan }}
                                        </td>
                                        <!-- Aksi -->
                                        <td class="admin-table-td">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('admin.reviews.edit', $review->id) }}" class="inline-flex items-center px-2 py-1 bg-cyan-300 hover:bg-cyan-400 text-black border-2 border-black dark:border-white font-black text-xs uppercase shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1.5px_1.5px_0px_0px_rgba(255,255,255,1)] hover:translate-y-[-1px] hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] active:translate-y-0 active:shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] transition-all cursor-pointer">
                                                    Edit
                                                </a>
                                                <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="inline" data-confirm="Apakah Anda yakin ingin menghapus ulasan ini?" data-title="Hapus Review" data-confirm-danger="true">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-2 py-1 bg-red-400 hover:bg-red-500 text-white border-2 border-black dark:border-white font-black text-xs uppercase shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1.5px_1.5px_0px_0px_rgba(255,255,255,1)] hover:translate-y-[-1px] hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] active:translate-y-0 active:shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] transition-all cursor-pointer">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 italic">Belum ada review pelanggan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($reviews->hasPages())
                        <div class="mt-6">
                            {{ $reviews->links('vendor.pagination.neo-brutalist') }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
