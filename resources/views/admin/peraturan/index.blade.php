<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Kelola Peraturan & Tata Tertib Kost') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">

                    <!-- Header Section -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100">Daftar Peraturan & Tata Tertib</h3>
                            <p class="admin-subtitle">Kelola aturan resmi kost putri Asri Boarding House agar tampil dinamis di halaman penyewa.</p>
                        </div>
                        <a href="{{ route('admin.peraturan.create') }}" class="admin-btn-primary">
                            <span class="text-sm">+</span> Tambah Peraturan
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

                    <!-- Search Bar -->
                    <div class="bg-slate-50 dark:bg-slate-900 border-4 border-black dark:border-white p-5 rounded-none mb-6 flex flex-wrap gap-4 items-center justify-between shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                        <form method="GET" action="{{ route('admin.peraturan.index') }}" class="flex flex-wrap gap-3 items-center w-full">
                            <div class="relative flex-grow max-w-md">
                                <input type="text" name="search" id="search-input" 
                                       class="admin-input !py-2 !pl-10 w-full" 
                                       placeholder="Cari judul, deskripsi..."
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
                                    <a href="{{ route('admin.peraturan.index') }}" class="admin-btn-secondary !py-2.5 !px-5">
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Peraturan Table Component -->
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead class="admin-table-thead">
                                <tr>
                                    <th scope="col" class="admin-table-th text-center w-12">No.</th>
                                    <th scope="col" class="admin-table-th text-center w-16">Urutan</th>
                                    <th scope="col" class="admin-table-th w-24">Ikon</th>
                                    <th scope="col" class="admin-table-th w-64">Judul Peraturan</th>
                                    <th scope="col" class="admin-table-th">Deskripsi Aturan</th>
                                    <th scope="col" class="admin-table-th w-32">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="admin-table-tbody">
                                @forelse($peraturan as $p)
                                    <tr class="admin-table-tr">
                                        <!-- No. -->
                                        <td class="admin-table-td text-center font-bold text-slate-500 dark:text-slate-400">
                                            {{ $loop->iteration + $peraturan->firstItem() - 1 }}
                                        </td>
                                        <!-- Urutan -->
                                        <td class="admin-table-td text-center font-bold text-slate-500 dark:text-slate-400">
                                            {{ $p->urutan }}
                                        </td>
                                        <!-- Ikon -->
                                        <td class="admin-table-td">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-none {{ $p->badge_color_class }}">
                                                    <x-icon :name="$p->ikon" class="w-4 h-4 shrink-0" />
                                                </span>
                                                <span class="text-xs font-bold capitalize text-slate-700 dark:text-slate-300">
                                                    {{ $p->ikon_label }}
                                                </span>
                                            </div>
                                        </td>
                                        <!-- Judul -->
                                        <td class="admin-table-td !whitespace-normal font-extrabold text-slate-800 dark:text-slate-100">
                                            {{ $p->judul }}
                                        </td>
                                        <!-- Deskripsi -->
                                        <td class="admin-table-td !whitespace-normal text-slate-500 dark:text-slate-400 font-semibold text-xs leading-relaxed max-w-md break-words">
                                            {{ $p->deskripsi }}
                                        </td>
                                        <!-- Aksi -->
                                        <td class="admin-table-td">
                                            <div class="flex items-center gap-4">
                                                <a href="{{ route('admin.peraturan.edit', $p->id) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition duration-150">
                                                    Edit
                                                </a>
                                                <form action="{{ route('admin.peraturan.destroy', $p->id) }}" method="POST" class="inline" data-confirm="Apakah Anda yakin ingin menghapus peraturan ini?" data-title="Hapus Peraturan" data-confirm-danger="true">
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
                                        <td colspan="6" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 italic">Belum ada peraturan kost. Silakan tambahkan peraturan baru.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($peraturan->hasPages())
                        <div class="mt-6">
                            {{ $peraturan->links('vendor.pagination.neo-brutalist') }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
