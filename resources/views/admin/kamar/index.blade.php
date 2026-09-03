<x-app-layout>
    <x-toast />
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Manajemen Kamar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">

                    <!-- Header Component: Judul halaman dan Tombol '+ Tambah Kamar' -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100">Daftar & Kondisi Kamar Asri Boarding House</h3>
                            <p class="admin-subtitle">Pantau kondisi ketersediaan, status unit, serta kelola fasilitas dan tipe kamar.</p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('admin.fasilitas.index') }}" class="admin-btn-secondary">
                                Kelola Fasilitas
                            </a>
                            <a href="{{ route('admin.kamar.create') }}" class="admin-btn-primary">
                                <span class="text-sm">+</span> Tambah Kamar
                            </a>
                        </div>
                    </div>

                    <!-- Stat Cards Ringkasan Status Kamar (Interactive Summary Cards) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                        <!-- Card 1: Total Kamar -->
                        <button type="button" onclick="filterKamarByStatus('')" class="w-full text-left cursor-pointer bg-cyan-300 dark:bg-cyan-950 p-4 rounded-none border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] transition-all duration-150 hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)] focus:outline-none focus:ring-4 focus:ring-cyan-500 {{ !request('status') ? 'ring-4 ring-cyan-500' : '' }}">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-black uppercase tracking-wider text-black dark:text-cyan-200">Total Kamar</span>
                                <div class="p-1 border-2 border-black dark:border-white bg-white dark:bg-slate-800 text-black dark:text-white shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] rounded-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-2">
                                <div class="text-3xl font-black text-black dark:text-white">{{ $stats['total'] ?? 0 }} <span class="text-xs font-bold">Unit</span></div>
                                <div class="flex items-center justify-between mt-2 pt-2 border-t-2 border-black dark:border-white text-[10px] font-extrabold text-black dark:text-cyan-200">
                                    <span>Tingkat Hunian</span>
                                    <span class="px-1.5 py-0.5 border border-black dark:border-white bg-white dark:bg-slate-800 text-black dark:text-white font-black">{{ $occupancyRate ?? 0 }}%</span>
                                </div>
                            </div>
                        </button>

                        <!-- Card 2: Kamar Tersedia -->
                        <button type="button" onclick="filterKamarByStatus('tersedia')" class="w-full text-left cursor-pointer bg-emerald-300 dark:bg-emerald-950 p-4 rounded-none border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] transition-all duration-150 hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)] focus:outline-none focus:ring-4 focus:ring-emerald-500 {{ request('status') === 'tersedia' ? 'ring-4 ring-emerald-500' : '' }}">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-black uppercase tracking-wider text-black dark:text-emerald-200">Tersedia</span>
                                <div class="p-1 border-2 border-black dark:border-white bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] rounded-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-2">
                                <div class="text-3xl font-black text-black dark:text-white">{{ $stats['tersedia'] ?? 0 }} <span class="text-xs font-bold">Unit</span></div>
                                <div class="flex items-center justify-between mt-2 pt-2 border-t-2 border-black dark:border-white text-[10px] font-extrabold text-black dark:text-emerald-200">
                                    <span>Siap Disewakan</span>
                                    <span class="font-black text-emerald-800 dark:text-emerald-300">Klik filter</span>
                                </div>
                            </div>
                        </button>

                        <!-- Card 3: Kamar Terisi -->
                        <button type="button" onclick="filterKamarByStatus('terisi')" class="w-full text-left cursor-pointer bg-blue-300 dark:bg-blue-950 p-4 rounded-none border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] transition-all duration-150 hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)] focus:outline-none focus:ring-4 focus:ring-blue-500 {{ request('status') === 'terisi' ? 'ring-4 ring-blue-500' : '' }}">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-black uppercase tracking-wider text-black dark:text-blue-200">Terisi</span>
                                <div class="p-1 border-2 border-black dark:border-white bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] rounded-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-2">
                                <div class="text-3xl font-black text-black dark:text-white">{{ $stats['terisi'] ?? 0 }} <span class="text-xs font-bold">Unit</span></div>
                                <div class="flex items-center justify-between mt-2 pt-2 border-t-2 border-black dark:border-white text-[10px] font-extrabold text-black dark:text-blue-200">
                                    <span>Aktif Dihuni</span>
                                    <span class="font-black text-blue-800 dark:text-blue-300">Klik filter</span>
                                </div>
                            </div>
                        </button>

                        <!-- Card 4: Kamar Maintenance -->
                        <button type="button" onclick="filterKamarByStatus('maintenance')" class="w-full text-left cursor-pointer bg-amber-300 dark:bg-amber-950 p-4 rounded-none border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] transition-all duration-150 hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)] focus:outline-none focus:ring-4 focus:ring-amber-500 {{ request('status') === 'maintenance' ? 'ring-4 ring-amber-500' : '' }}">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-black uppercase tracking-wider text-black dark:text-amber-200">Maintenance</span>
                                <div class="p-1 border-2 border-black dark:border-white bg-white dark:bg-slate-800 text-amber-600 dark:text-amber-400 shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] rounded-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-2">
                                <div class="text-3xl font-black text-black dark:text-white">{{ $stats['maintenance'] ?? 0 }} <span class="text-xs font-bold">Unit</span></div>
                                <div class="flex items-center justify-between mt-2 pt-2 border-t-2 border-black dark:border-white text-[10px] font-extrabold text-black dark:text-amber-200">
                                    <span>Perbaikan/Renovasi</span>
                                    <span class="font-black text-amber-800 dark:text-amber-300">Klik filter</span>
                                </div>
                            </div>
                        </button>
                    </div>

                    <!-- Quick Status Pills Navigation & Mode Switcher -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 pb-4 border-b-2 border-black dark:border-white">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-black text-slate-700 dark:text-slate-300 uppercase tracking-wide mr-1">Filter Status:</span>
                            <button onclick="filterKamarByStatus('')" class="px-3 py-1.5 text-xs font-black border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] transition {{ !request('status') ? 'bg-black text-white dark:bg-white dark:text-black' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:text-white' }}">
                                Semua ({{ $stats['total'] ?? 0 }})
                            </button>
                            <button onclick="filterKamarByStatus('tersedia')" class="px-3 py-1.5 text-xs font-black border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] transition {{ request('status') === 'tersedia' ? 'bg-emerald-400 text-black' : 'bg-emerald-100 dark:bg-emerald-950 dark:text-emerald-200 hover:bg-emerald-200' }}">
                                🟢 Tersedia ({{ $stats['tersedia'] ?? 0 }})
                            </button>
                            <button onclick="filterKamarByStatus('terisi')" class="px-3 py-1.5 text-xs font-black border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] transition {{ request('status') === 'terisi' ? 'bg-blue-400 text-black' : 'bg-blue-100 dark:bg-blue-950 dark:text-blue-200 hover:bg-blue-200' }}">
                                🔵 Terisi ({{ $stats['terisi'] ?? 0 }})
                            </button>
                            <button onclick="filterKamarByStatus('maintenance')" class="px-3 py-1.5 text-xs font-black border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] transition {{ request('status') === 'maintenance' ? 'bg-amber-400 text-black' : 'bg-amber-100 dark:bg-amber-950 dark:text-amber-200 hover:bg-amber-200' }}">
                                🟡 Maintenance ({{ $stats['maintenance'] ?? 0 }})
                            </button>
                            @if(($stats['deleted'] ?? 0) > 0)
                                <button onclick="filterKamarByStatus('deleted')" class="px-3 py-1.5 text-xs font-black border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] transition {{ request('status') === 'deleted' ? 'bg-rose-500 text-white' : 'bg-rose-100 dark:bg-rose-950 dark:text-rose-200 hover:bg-rose-200' }}">
                                    🗑️ Terhapus ({{ $stats['deleted'] }})
                                </button>
                            @endif
                        </div>

                        <!-- View Mode Toggle -->
                        <div class="flex items-center border-2 border-black dark:border-white bg-slate-100 dark:bg-slate-800 p-1 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            <button id="btnViewTable" onclick="switchViewMode('table')" class="px-3 py-1 text-xs font-black border border-black dark:border-white bg-white dark:bg-slate-900 text-black dark:text-white shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">
                                📋 Tabel
                            </button>
                            <button id="btnViewGrid" onclick="switchViewMode('grid')" class="px-3 py-1 text-xs font-black border border-transparent text-slate-600 dark:text-slate-300 hover:text-black dark:hover:text-white">
                                🗺️ Denah/Visual Grid
                            </button>
                        </div>
                    </div>

                    <!-- Filter Bar Component: Dropdown filter berdasarkan Tipe Kamar, Status Kamar, dan Lantai -->
                    <div class="bg-yellow-100 dark:bg-slate-900 border-4 border-black dark:border-white p-5 rounded-none mb-8 flex flex-wrap gap-6 items-center shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                        <div class="flex flex-col min-w-[150px]">
                            <label for="filter_tipe" class="admin-label">Tipe Kamar</label>
                            <select id="filter_tipe" class="admin-select-sm" onchange="filterKamar()">
                                <option value="">Semua Tipe</option>
                                <option value="standar" {{ request('tipe') === 'standar' ? 'selected' : '' }}>Standar</option>
                                <option value="deluxe" {{ request('tipe') === 'deluxe' ? 'selected' : '' }}>Deluxe</option>
                                <option value="vip" {{ request('tipe') === 'vip' ? 'selected' : '' }}>VIP</option>
                            </select>
                        </div>
                        <div class="flex flex-col min-w-[150px]">
                            <label for="filter_status" class="admin-label">Status</label>
                            <select id="filter_status" class="admin-select-sm" onchange="filterKamar()">
                                <option value="">Semua Status</option>
                                <option value="tersedia" {{ request('status') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                <option value="terisi" {{ request('status') === 'terisi' ? 'selected' : '' }}>Terisi</option>
                                <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="deleted" {{ request('status') === 'deleted' ? 'selected' : '' }}>Terhapus (Trash)</option>
                            </select>
                        </div>
                        <div class="flex flex-col min-w-[150px]">
                            <label for="filter_lantai" class="admin-label">Lantai</label>
                            <select id="filter_lantai" class="admin-select-sm" onchange="filterKamar()">
                                <option value="">Semua Lantai</option>
                                @foreach(($allFloors ?? []) as $l)
                                    <option value="{{ $l }}" {{ request('lantai') == $l ? 'selected' : '' }}>Lantai {{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if(request('tipe') || request('status') || request('lantai'))
                            <div class="flex flex-col justify-end">
                                <a href="{{ route('admin.kamar.index') }}" class="px-3 py-2 text-xs font-black bg-rose-300 hover:bg-rose-400 text-black border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                    Reset Filter
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- VIEW MODE 1: Kamar Table Component -->
                    <div id="sectionTable" class="admin-table-container">
                        <table class="admin-table">
                            <thead class="admin-table-thead">
                                <tr>
                                    <th scope="col" class="admin-table-th">Foto</th>
                                    <th scope="col" class="admin-table-th">No. Kamar</th>
                                    <th scope="col" class="admin-table-th">Lantai</th>
                                    <th scope="col" class="admin-table-th">Tipe</th>
                                    <th scope="col" class="admin-table-th">Fasilitas</th>
                                    <th scope="col" class="admin-table-th">Harga/Bulan</th>
                                    <th scope="col" class="admin-table-th">Status</th>
                                    <th scope="col" class="admin-table-th">Penyewa Aktif</th>
                                    <th scope="col" class="admin-table-th">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="admin-table-tbody">
                                @forelse($kamar as $k)
                                    <tr class="admin-table-tr" data-tipe="{{ strtolower($k->tipe) }}" data-status="{{ $k->status }}" data-lantai="{{ $k->lantai }}">
                                        <!-- Foto -->
                                        <td class="admin-table-td">
                                            <img src="{{ $k->foto_url }}" alt="Kamar {{ $k->nomor_kamar }}" class="w-16 h-12 object-cover rounded-none border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]">
                                        </td>
                                        <!-- No. Kamar -->
                                        <td class="admin-table-td font-bold text-slate-800 dark:text-slate-100">
                                            Kamar {{ $k->nomor_kamar }}
                                        </td>
                                        <!-- Lantai -->
                                        <td class="admin-table-td text-slate-500 dark:text-slate-400 font-semibold">
                                            Lantai {{ $k->lantai }}
                                        </td>
                                        <!-- Tipe -->
                                        <td class="admin-table-td capitalize font-bold text-slate-800 dark:text-slate-200">
                                            {{ $k->tipe }}
                                        </td>
                                        <!-- Fasilitas -->
                                        <td class="admin-table-td">
                                            <div class="flex flex-wrap gap-1.5 max-w-[200px]">
                                                @foreach($k->fasilitas as $f)
                                                    <span class="px-2 py-0.5 text-[10px] font-black bg-cyan-300 text-black border border-black dark:border-white shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1px_1px_0px_0px_rgba(255,255,255,1)] rounded-none">
                                                        {{ $f->nama }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <!-- Harga/Bulan -->
                                        <td class="admin-table-td font-extrabold text-slate-900 dark:text-slate-100">
                                            Rp {{ number_format($k->harga_bulan, 0, ',', '.') }}
                                        </td>
                                        <!-- Status -->
                                        <td class="admin-table-td">
                                            @if($k->status === 'tersedia')
                                                <span class="admin-badge admin-badge-success">Tersedia</span>
                                            @elseif($k->status === 'terisi')
                                                <span class="admin-badge admin-badge-info">Terisi</span>
                                            @else
                                                <span class="admin-badge admin-badge-warning">Maintenance</span>
                                            @endif
                                        </td>
                                        <!-- Penyewa Aktif -->
                                        <td class="admin-table-td">
                                            @if($k->penyewaAktif)
                                                <a href="{{ route('admin.penyewa.show', $k->penyewaAktif->id) }}" class="text-blue-600 dark:text-blue-400 font-bold hover:underline">
                                                    {{ $k->penyewaAktif?->user?->nama ?? 'N/A' }}
                                                </a>
                                            @else
                                                <span class="text-slate-400 dark:text-slate-500 font-light italic text-xs">Belum terisi</span>
                                            @endif
                                        </td>
                                        <!-- Aksi -->
                                        <td class="admin-table-td">
                                            <div class="flex items-center gap-3">
                                                @if($k->trashed())
                                                    <form action="{{ route('admin.kamar.restore', $k->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold text-xs shadow-sm hover:shadow-md transition cursor-pointer">
                                                            Restore
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('admin.kamar.show', $k->id) }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white transition duration-150">
                                                        Detail
                                                    </a>
                                                    <a href="{{ route('admin.kamar.edit', $k->id) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition duration-150">
                                                        Edit
                                                    </a>
                                                    <!-- Ubah Status Dropdown -->
                                                    <form action="{{ route('admin.kamar.updateStatus', $k->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <select name="status" onchange="this.form.submit()" class="admin-select-sm !py-1 !px-2 text-[10px]">
                                                            <option value="tersedia" {{ $k->status === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                                            <option value="terisi" {{ $k->status === 'terisi' ? 'selected' : '' }}>Terisi</option>
                                                            <option value="maintenance" {{ $k->status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                                        </select>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 italic">Belum ada data kamar kost.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- VIEW MODE 2: Denah Visual Grid Section (Per Lantai Breakdown) -->
                    <div id="sectionGrid" class="hidden space-y-8">
                        @forelse(($allKamarGrouped ?? []) as $lantai => $kamarsInFloor)
                            <div class="bg-slate-50 dark:bg-slate-900 border-4 border-black dark:border-white p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                                <div class="flex items-center justify-between mb-4 border-b-2 border-black dark:border-white pb-3">
                                    <h4 class="text-base font-black text-black dark:text-white flex items-center gap-2">
                                        <span>🏢 Lantai {{ $lantai }}</span>
                                        <span class="text-xs font-bold px-2 py-0.5 border border-black dark:border-white bg-yellow-300 text-black">
                                            {{ $kamarsInFloor->count() }} Unit
                                        </span>
                                    </h4>
                                    <div class="flex gap-3 text-xs font-black">
                                        <span class="text-emerald-600 dark:text-emerald-400">🟢 {{ $kamarsInFloor->where('status', 'tersedia')->count() }} Tersedia</span>
                                        <span class="text-blue-600 dark:text-blue-400">🔵 {{ $kamarsInFloor->where('status', 'terisi')->count() }} Terisi</span>
                                        <span class="text-amber-600 dark:text-amber-400">🟡 {{ $kamarsInFloor->where('status', 'maintenance')->count() }} Maint.</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                    @foreach($kamarsInFloor as $km)
                                        <div class="border-3 border-black dark:border-white p-4 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,1)] bg-white dark:bg-slate-800 flex flex-col justify-between space-y-3">
                                            <div class="flex items-start justify-between">
                                                <div>
                                                    <span class="text-xs uppercase font-extrabold text-slate-400">Kamar</span>
                                                    <h5 class="text-lg font-black text-black dark:text-white">{{ $km->nomor_kamar }}</h5>
                                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 capitalize">{{ $km->tipe }}</span>
                                                </div>
                                                <div>
                                                    @if($km->status === 'tersedia')
                                                        <span class="px-2 py-1 text-[10px] font-black bg-emerald-300 text-black border border-black shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">
                                                            Tersedia
                                                        </span>
                                                    @elseif($km->status === 'terisi')
                                                        <span class="px-2 py-1 text-[10px] font-black bg-blue-300 text-black border border-black shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">
                                                            Terisi
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-1 text-[10px] font-black bg-amber-300 text-black border border-black shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">
                                                            Maintenance
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="text-xs border-t border-slate-200 dark:border-slate-700 pt-2 space-y-1">
                                                <div class="flex justify-between text-slate-600 dark:text-slate-300">
                                                    <span>Harga:</span>
                                                    <span class="font-extrabold text-black dark:text-white">Rp {{ number_format($km->harga_bulan, 0, ',', '.') }}/bln</span>
                                                </div>
                                                <div class="flex justify-between text-slate-600 dark:text-slate-300">
                                                    <span>Penyewa:</span>
                                                    @if($km->penyewaAktif)
                                                        <a href="{{ route('admin.penyewa.show', $km->penyewaAktif->id) }}" class="font-black text-blue-600 dark:text-blue-400 hover:underline truncate max-w-[120px]">
                                                            {{ $km->penyewaAktif?->user?->nama ?? 'N/A' }}
                                                        </a>
                                                    @else
                                                        <span class="text-slate-400 italic">Kosong</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="flex items-center justify-between border-t-2 border-black dark:border-white pt-2 text-[10px] font-black">
                                                <a href="{{ route('admin.kamar.show', $km->id) }}" class="text-slate-600 dark:text-slate-300 hover:text-black dark:hover:text-white underline">
                                                    Detail &rarr;
                                                </a>
                                                <form action="{{ route('admin.kamar.updateStatus', $km->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status" onchange="this.form.submit()" class="admin-select-sm !py-0.5 !px-1 text-[10px] bg-slate-100 dark:bg-slate-900">
                                                        <option value="tersedia" {{ $km->status === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                                        <option value="terisi" {{ $km->status === 'terisi' ? 'selected' : '' }}>Terisi</option>
                                                        <option value="maintenance" {{ $km->status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                                    </select>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-slate-400 italic border-2 border-black">Belum ada kamar terdaftar.</div>
                        @endforelse
                    </div>

                    <!-- Pagination (Table Mode) -->
                    <div id="sectionPagination" class="mt-6">
                        {{ $kamar->links('vendor.pagination.neo-brutalist') }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Server-side filtering & View Switcher script -->
    <script>
        function filterKamarByStatus(status) {
            const el = document.getElementById('filter_status');
            if (el) el.value = status;
            filterKamar();
        }

        function filterKamar() {
            const tipe = document.getElementById('filter_tipe')?.value;
            const status = document.getElementById('filter_status')?.value;
            const lantai = document.getElementById('filter_lantai')?.value;
            
            const url = new URL(window.location.href);
            if (tipe) url.searchParams.set('tipe', tipe); else url.searchParams.delete('tipe');
            if (status) url.searchParams.set('status', status); else url.searchParams.delete('status');
            if (lantai) url.searchParams.set('lantai', lantai); else url.searchParams.delete('lantai');
            url.searchParams.set('page', '1');
            
            window.location.href = url.pathname + url.search;
        }

        function switchViewMode(mode) {
            const tableSec = document.getElementById('sectionTable');
            const gridSec = document.getElementById('sectionGrid');
            const pagSec = document.getElementById('sectionPagination');
            const btnTable = document.getElementById('btnViewTable');
            const btnGrid = document.getElementById('btnViewGrid');

            if (!tableSec || !gridSec) return;

            if (mode === 'grid') {
                tableSec.classList.add('hidden');
                gridSec.classList.remove('hidden');
                if (pagSec) pagSec.classList.add('hidden');
                
                btnGrid?.classList.add('bg-white', 'dark:bg-slate-900', 'text-black', 'dark:text-white', 'border', 'border-black', 'dark:border-white', 'shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]');
                btnTable?.classList.remove('bg-white', 'dark:bg-slate-900', 'text-black', 'dark:text-white', 'border', 'border-black', 'dark:border-white', 'shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]');
                btnTable?.classList.add('border-transparent', 'text-slate-600', 'dark:text-slate-300');
                localStorage.setItem('kamar_view_mode', 'grid');
            } else {
                tableSec.classList.remove('hidden');
                gridSec.classList.add('hidden');
                if (pagSec) pagSec.classList.remove('hidden');

                btnTable?.classList.add('bg-white', 'dark:bg-slate-900', 'text-black', 'dark:text-white', 'border', 'border-black', 'dark:border-white', 'shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]');
                btnGrid?.classList.remove('bg-white', 'dark:bg-slate-900', 'text-black', 'dark:text-white', 'border', 'border-black', 'dark:border-white', 'shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]');
                btnGrid?.classList.add('border-transparent', 'text-slate-600', 'dark:text-slate-300');
                localStorage.setItem('kamar_view_mode', 'table');
            }
        }

        // Restore user view preference
        document.addEventListener('DOMContentLoaded', () => {
            const savedMode = localStorage.getItem('kamar_view_mode');
            if (savedMode === 'grid') {
                switchViewMode('grid');
            }
        });
    </script>
</x-app-layout>
