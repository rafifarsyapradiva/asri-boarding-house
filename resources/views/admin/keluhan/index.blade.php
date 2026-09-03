<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Manajemen Keluhan & Pengaduan') }}
        </h2>
    </x-slot>

    <!-- Include Toast Notifications -->
    <x-toast />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">

                    <!-- Header Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100">Daftar Pengaduan Masuk</h3>
                        <p class="admin-subtitle">Semua keluhan penyewa dan laporan kerusakan fasilitas Kost Asri.</p>
                    </div>

                    <!-- Status Filter Tabs & Search -->
                    <div class="bg-slate-50 dark:bg-slate-900 border-4 border-black dark:border-white p-5 rounded-none mb-6 flex flex-wrap gap-4 items-center justify-between shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                        <!-- Tabs -->
                        <div class="flex flex-wrap gap-2">
                            @php
                                $tabs = [
                                    '' => ['label' => 'Semua Keluhan', 'activeBg' => 'bg-cyan-300'],
                                    'pending' => ['label' => 'Pending', 'activeBg' => 'bg-amber-300'],
                                    'diproses' => ['label' => 'Diproses', 'activeBg' => 'bg-blue-300'],
                                    'selesai' => ['label' => 'Selesai', 'activeBg' => 'bg-emerald-300'],
                                ];
                                $baseTabStyle = 'inline-flex items-center justify-center px-4 py-2 border-4 border-black dark:border-white rounded-none transition-all duration-150 text-xs uppercase tracking-wider gap-2 cursor-pointer font-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]';
                                $inactiveTabStyle = 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 text-slate-900 hover:translate-x-[-1px] hover:translate-y-[-1px] hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[3px_3px_0px_0px_rgba(255,255,255,1)]';
                            @endphp

                            @foreach($tabs as $key => $tab)
                                @php $isActive = $status === $key || (empty($key) && !$status); @endphp
                                <a href="{{ route('admin.keluhan.index', array_filter(['status' => $key, 'search' => request('search')])) }}" 
                                   class="{{ $baseTabStyle }} {{ $isActive ? "{$tab['activeBg']} text-black" : $inactiveTabStyle }}">
                                    {{ $tab['label'] }}
                                </a>
                            @endforeach
                        </div>

                        <!-- Server-side Search Input Form -->
                        <form method="GET" action="{{ route('admin.keluhan.index') }}" class="relative w-full md:w-72 flex gap-2">
                            @if($status)
                                <input type="hidden" name="status" value="{{ $status }}">
                            @endif
                            <div class="relative flex-grow">
                                <input type="text" name="search" id="search-input" 
                                       class="admin-input !py-2 !pl-10 w-full" 
                                       placeholder="Cari penyewa, kamar, judul..."
                                       value="{{ request('search') }}">
                                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>
                            <button type="submit" class="admin-btn-primary !py-2 !px-4">
                                Cari
                            </button>
                        </form>
                    </div>

                    <!-- Table -->
                    <div class="admin-table-container">
                        <table class="admin-table" id="keluhan-table">
                            <thead class="admin-table-thead">
                                <tr>
                                    <th class="admin-table-th">Penyewa</th>
                                    <th class="admin-table-th">Kamar</th>
                                    <th class="admin-table-th">Judul Laporan</th>
                                    <th class="admin-table-th">Kategori</th>
                                    <th class="admin-table-th">Tanggal Lapor</th>
                                    <th class="admin-table-th">Status</th>
                                    <th class="admin-table-th">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="admin-table-tbody">
                                @forelse ($keluhan as $k)
                                    <tr class="admin-table-tr select-row">
                                        <td class="admin-table-td font-extrabold text-slate-900 dark:text-slate-200">
                                            {{ $k->penyewa?->user?->nama ?? '-' }}
                                        </td>
                                        <td class="admin-table-td font-bold text-slate-900 dark:text-slate-100">
                                            Kamar {{ $k->penyewa?->kamar?->nomor_kamar ?? '-' }}
                                        </td>
                                        <td class="admin-table-td font-semibold text-slate-800 dark:text-slate-200">
                                            {{ $k->judul ?? '-' }}
                                        </td>
                                        <td class="admin-table-td text-slate-500 dark:text-slate-400 font-semibold capitalize">
                                            {{ $k->kategori_label ?? '-' }}
                                        </td>
                                        <td class="admin-table-td text-slate-500 dark:text-slate-400 font-semibold">
                                            {{ $k->created_at ? $k->created_at->format('d M Y H:i') : '-' }}
                                        </td>
                                        <td class="admin-table-td">
                                            <span class="admin-badge {{ $k->status_badge_class }}">
                                                {{ strtoupper($k->status ?? '') }}
                                            </span>
                                        </td>
                                        <td class="admin-table-td">
                                            <a href="{{ route('admin.keluhan.show', $k) }}" class="inline-flex items-center px-2 py-1 bg-cyan-300 hover:bg-cyan-400 text-black border-2 border-black dark:border-white font-black text-xs uppercase shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1.5px_1.5px_0px_0px_rgba(255,255,255,1)] hover:translate-y-[-1px] hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] active:translate-y-0 active:shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] transition-all cursor-pointer">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 italic">
                                            Tidak ada data keluhan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($keluhan->hasPages())
                        <div class="mt-6">
                            {{ $keluhan->links('vendor.pagination.neo-brutalist') }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
