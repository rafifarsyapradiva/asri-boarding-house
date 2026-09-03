<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Keluhan & Pengaduan Saya') }}
        </h2>
    </x-slot>

    <!-- Include Toast Notifications -->
    <x-toast />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">

                    <!-- Header Section -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4 pb-4 border-b-4 border-black dark:border-white">
                        <div>
                            <h3 class="text-lg font-black text-slate-800 dark:text-slate-100 uppercase tracking-wide">Riwayat Keluhan Anda</h3>
                            <p class="admin-subtitle">Daftar keluhan atau kerusakan fasilitas kost yang Anda laporkan.</p>
                        </div>
                        <div>
                            <a href="{{ route('penyewa.keluhan.create') }}" class="admin-btn-primary">
                                <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                                </svg>
                                Buat Keluhan Baru
                            </a>
                        </div>
                    </div>

                    <!-- Status Filter Tabs & Search -->
                    <div class="bg-slate-50 dark:bg-slate-900 border-4 border-black dark:border-white p-5 rounded-none mb-6 flex flex-wrap gap-4 items-center justify-between shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                        <!-- Clean Rendered Tabs -->
                        <div class="flex flex-wrap gap-2">
                            @php
                                $tabs = [
                                    '' => ['label' => 'Semua Keluhan', 'activeBg' => 'bg-cyan-300'],
                                    'pending' => ['label' => 'Pending', 'activeBg' => 'bg-amber-300'],
                                    'diproses' => ['label' => 'Diproses', 'activeBg' => 'bg-blue-300'],
                                    'selesai' => ['label' => 'Selesai', 'activeBg' => 'bg-emerald-300'],
                                ];
                                $currentStatus = (string) request('status', '');
                            @endphp

                            @foreach($tabs as $key => $tab)
                                @php
                                    $isActive = $currentStatus === (string) $key;
                                    $activeClass = $isActive 
                                        ? "{$tab['activeBg']} text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]" 
                                        : "bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 text-slate-900 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] hover:translate-x-[-1px] hover:translate-y-[-1px] hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[3px_3px_0px_0px_rgba(255,255,255,1)]";
                                @endphp
                                <a href="{{ route('penyewa.keluhan.index', array_filter(['status' => $key, 'search' => request('search')])) }}" 
                                   class="inline-flex items-center justify-center px-4 py-2 border-4 border-black dark:border-white rounded-none transition-all duration-150 text-xs uppercase tracking-wider gap-2 cursor-pointer font-black {{ $activeClass }}">
                                    {{ $tab['label'] }}
                                </a>
                            @endforeach
                        </div>

                        <!-- Search Form -->
                        <form method="GET" action="{{ route('penyewa.keluhan.index') }}" class="relative w-full md:w-72 flex gap-2">
                            @if(request('status'))
                                <input type="hidden" name="status" value="{{ request('status') }}">
                            @endif
                            <div class="relative flex-grow">
                                <input type="text" name="search" id="search-input" 
                                       class="admin-input !py-2 !pl-10 w-full" 
                                       placeholder="Cari judul..."
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

                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead class="admin-table-thead">
                                <tr>
                                    <th class="admin-table-th">Judul</th>
                                    <th class="admin-table-th">Kategori</th>
                                    <th class="admin-table-th">Tanggal Lapor</th>
                                    <th class="admin-table-th">Status</th>
                                    <th class="admin-table-th">Tanggapan Admin</th>
                                    <th class="admin-table-th">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="admin-table-tbody">
                                 @forelse ($keluhan as $k)
                                     <tr class="admin-table-tr">
                                         <td class="admin-table-td font-bold text-slate-800 dark:text-slate-100">
                                             {{ $k->judul ?? '-' }}
                                         </td>
                                         <td class="admin-table-td font-semibold text-slate-500 dark:text-slate-400 capitalize">
                                             {{ $k->kategori_label ?? '-' }}
                                         </td>
                                         <td class="admin-table-td text-slate-500 dark:text-slate-400 font-semibold">
                                             {{ $k->created_at?->format('d M Y H:i') ?? '-' }}
                                         </td>
                                         <td class="admin-table-td">
                                             <span class="admin-badge {{ $k->status_badge_class ?? 'admin-badge-neutral' }}">
                                                 {{ strtoupper($k->status ?? '-') }}
                                             </span>
                                         </td>
                                         <td class="admin-table-td text-slate-600 dark:text-slate-400 font-medium max-w-xs truncate">
                                             {{ $k->tanggapan_admin ?: '-' }}
                                         </td>
                                         <td class="admin-table-td">
                                             <a href="{{ route('penyewa.keluhan.show', $k) }}" class="text-xs font-extrabold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition duration-150 underline decoration-2">
                                                 Detail
                                             </a>
                                         </td>
                                     </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 italic font-bold">
                                            Tidak ada data keluhan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($keluhan instanceof \Illuminate\Pagination\LengthAwarePaginator && $keluhan->hasPages())
                    <div class="mt-6">
                        {{ $keluhan->links('vendor.pagination.neo-brutalist') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
