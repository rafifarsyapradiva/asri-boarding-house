<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Manajemen Galeri') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">

                    <!-- Header Section -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100">Daftar Galeri Kost</h3>
                            <p class="admin-subtitle">Kelola foto-foto suasana dan kamar Kost Asri yang ditampilkan di landing page.</p>
                        </div>
                        <a href="{{ route('admin.gallery.create') }}" class="admin-btn-primary">
                            <span class="text-sm">+</span> Tambah Foto Galeri
                        </a>
                    </div>

                    <!-- Alert Notifications -->
                    @if(session('success'))
                        <div class="p-4 mb-6 bg-emerald-400 dark:bg-emerald-800 border-4 border-black dark:border-white text-black dark:text-white font-extrabold shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                            <div class="flex items-center">
                                <span class="mr-2">✅</span>
                                <span class="font-semibold text-sm">{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Search Bar & Filters -->
                    <div class="bg-slate-50 dark:bg-slate-900 border-4 border-black dark:border-white p-5 rounded-none mb-6 flex flex-wrap gap-4 items-center justify-between shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                        <form method="GET" action="{{ route('admin.gallery.index') }}" class="flex flex-wrap gap-3 items-center w-full">
                            <div class="relative flex-grow max-w-md">
                                <label for="search-input" class="sr-only">Cari Galeri</label>
                                <input type="text" 
                                       name="search" 
                                       id="search-input" 
                                       class="admin-input !py-2 !pl-10 w-full" 
                                       placeholder="Cari judul, deskripsi..."
                                       value="{{ request('search') }}"
                                       aria-label="Cari judul atau deskripsi galeri">
                                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="relative w-44">
                                <label for="status-filter" class="sr-only">Filter Status</label>
                                <select id="status-filter" name="status" class="admin-input !py-2 w-full" aria-label="Filter status galeri">
                                    <option value="">Semua Status</option>
                                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                                    <option value="inactive" @selected(request('status') === 'inactive')>Non-Aktif</option>
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="admin-btn-primary !py-2.5 !px-5">
                                    Filter
                                </button>
                                @if(request('search') || (request('status') !== null && request('status') !== ''))
                                    <a href="{{ route('admin.gallery.index') }}" class="admin-btn-secondary !py-2.5 !px-5">
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Gallery Table Component -->
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead class="admin-table-thead">
                                <tr>
                                    <th scope="col" class="admin-table-th text-center w-12">No.</th>
                                    <th scope="col" class="admin-table-th">Foto</th>
                                    <th scope="col" class="admin-table-th">Judul</th>
                                    <th scope="col" class="admin-table-th">Deskripsi</th>
                                    <th scope="col" class="admin-table-th text-center">Urutan</th>
                                    <th scope="col" class="admin-table-th text-center">Status Aktif</th>
                                    <th scope="col" class="admin-table-th">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="admin-table-tbody">
                                @forelse($galleries as $gallery)
                                    <tr class="admin-table-tr" data-testid="gallery-row-{{ $gallery->id }}">
                                        <!-- No. -->
                                        <td class="admin-table-td text-center font-bold text-slate-500 dark:text-slate-400">
                                            {{ $loop->iteration + $galleries->firstItem() - 1 }}
                                        </td>
                                        <!-- Foto Preview -->
                                        <td class="admin-table-td">
                                            @if($gallery->foto_url)
                                                <img src="{{ $gallery->foto_url }}" alt="{{ $gallery->judul }}" class="w-16 h-16 object-cover rounded-none border-4 border-black dark:border-white shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,1)]">
                                            @else
                                                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 border-4 border-dashed border-black dark:border-white rounded-none flex items-center justify-center text-xs text-slate-400 dark:text-slate-500 font-bold shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,1)]">
                                                    No Image
                                                </div>
                                            @endif
                                        </td>
                                        <!-- Judul -->
                                        <td class="admin-table-td !whitespace-normal font-bold text-slate-800 dark:text-slate-100 max-w-[200px] break-words" title="{{ $gallery->judul }}">
                                            {{ $gallery->judul }}
                                        </td>
                                        <!-- Deskripsi -->
                                        <td class="admin-table-td !whitespace-normal text-slate-500 dark:text-slate-400 font-medium max-w-[300px] break-words" title="{{ $gallery->deskripsi }}">
                                            {{ $gallery->deskripsi ?? '-' }}
                                        </td>
                                        <!-- Urutan -->
                                        <td class="admin-table-td text-center font-bold text-slate-700 dark:text-slate-300">
                                            {{ $gallery->urutan }}
                                        </td>
                                        <!-- Status Aktif -->
                                        <td class="admin-table-td text-center">
                                            @if($gallery->is_active)
                                                <span class="admin-badge admin-badge-success">Aktif</span>
                                            @else
                                                <span class="admin-badge admin-badge-danger">Non-Aktif</span>
                                            @endif
                                        </td>
                                        <!-- Aksi -->
                                        <td class="admin-table-td">
                                            <div class="flex items-center gap-4">
                                                <a href="{{ route('admin.gallery.edit', $gallery->id) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition duration-150">
                                                    Edit
                                                </a>
                                                <form action="{{ route('admin.gallery.destroy', $gallery->id) }}" method="POST" class="inline" data-confirm="Apakah Anda yakin ingin menghapus foto galeri ini?" data-title="Hapus Galeri" data-confirm-danger="true">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-xs font-bold text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition duration-150">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 italic">Belum ada foto galeri yang terdaftar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($galleries->hasPages())
                        <div class="mt-6">
                            {{ $galleries->links('vendor.pagination.neo-brutalist') }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
