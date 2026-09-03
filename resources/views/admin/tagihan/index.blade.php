<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Daftar Tagihan Kost') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">

                    <!-- Alert message component -->
                    <x-flash-message />

                    <!-- Header Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100">Semua Tagihan Bulanan</h3>
                        <p class="admin-subtitle">Daftar tagihan sewa kamar untuk seluruh penyewa aktif.</p>
                    </div>

                    <!-- Filter Bar (Server-side) -->
                    <form method="GET" action="{{ route('admin.tagihan.index') }}" class="bg-slate-50 dark:bg-slate-900 border-4 border-black dark:border-white p-5 rounded-none mb-6 flex flex-wrap gap-4 items-end shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                        <div class="flex-1 min-w-[250px]">
                            <label for="search-input" class="admin-label">Cari Tagihan</label>
                            <input type="text" id="search-input" name="search" class="admin-input !py-1.5" placeholder="Cari Order ID, nama penyewa, nomor kamar..." value="{{ request('search') }}">
                        </div>
                        <div class="min-w-[150px]">
                            <label for="status-filter" class="admin-label">Status Pembayaran</label>
                            <select id="status-filter" name="status" class="admin-select !py-1.5 w-full">
                                <option value="">Semua Status</option>
                                <option value="LUNAS" {{ request('status') === 'LUNAS' ? 'selected' : '' }}>LUNAS</option>
                                <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>PENDING</option>
                                <option value="TERLAMBAT" {{ request('status') === 'TERLAMBAT' ? 'selected' : '' }}>TERLAMBAT</option>
                                <option value="GAGAL" {{ request('status') === 'GAGAL' ? 'selected' : '' }}>GAGAL</option>
                                <option value="KADALUARSA" {{ request('status') === 'KADALUARSA' ? 'selected' : '' }}>KADALUARSA</option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="admin-btn-primary !py-2 !px-4">
                                Filter
                            </button>
                            <a href="{{ route('admin.tagihan.index') }}" class="admin-btn-secondary !py-2 !px-4">
                                Reset
                            </a>
                        </div>
                    </form>

                    <div class="admin-table-container">
                        <table class="admin-table" id="tagihan-table">
                            <thead class="admin-table-thead">
                                <tr>
                                    <th class="admin-table-th">Order ID</th>
                                    <th class="admin-table-th">Penyewa</th>
                                    <th class="admin-table-th">Kamar</th>
                                    <th class="admin-table-th">Periode</th>
                                    <th class="admin-table-th">Jatuh Tempo</th>
                                    <th class="admin-table-th">Total Tagihan</th>
                                    <th class="admin-table-th">Status</th>
                                    <th class="admin-table-th">Metode</th>
                                    <th class="admin-table-th">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="admin-table-tbody">
                                @forelse ($tagihan as $tgh)
                                    <tr class="admin-table-tr" data-status="{{ strtoupper($tgh->computed_status) }}">
                                        <td class="admin-table-td font-extrabold text-slate-800 dark:text-slate-100 search-field">
                                            {{ $tgh->order_id }}
                                        </td>
                                        <td class="admin-table-td font-semibold text-slate-800 dark:text-slate-200 search-field">
                                            {{ $tgh->penyewa?->user?->nama ?? '-' }}
                                        </td>
                                        <td class="admin-table-td font-bold text-slate-800 dark:text-slate-100 search-field">
                                            Kamar {{ $tgh->penyewa?->kamar?->nomor_kamar ?? '-' }}
                                        </td>
                                        <td class="admin-table-td text-slate-500 dark:text-slate-400 font-semibold">
                                            {{ sprintf('%02d/%d', $tgh->periode_bulan, $tgh->periode_tahun) }}
                                        </td>
                                        <td class="admin-table-td text-slate-500 dark:text-slate-400 font-semibold">
                                            {{ $tgh->tanggal_jatuh_tempo ? $tgh->tanggal_jatuh_tempo->format('d M Y') : '-' }}
                                        </td>
                                        <td class="admin-table-td font-extrabold text-slate-800 dark:text-slate-100">
                                            Rp {{ number_format($tgh->nominal_total, 0, ',', '.') }}
                                        </td>
                                        <td class="admin-table-td">
                                            <span class="admin-badge {{ $tgh->status_badge_class }}">
                                                {{ strtoupper($tgh->computed_status) }}
                                            </span>
                                        </td>
                                        <td class="admin-table-td text-slate-500 dark:text-slate-400 font-medium capitalize">
                                            {{ $tgh->metode_pembayaran ?: '-' }}
                                        </td>
                                        <td class="admin-table-td">
                                             <a href="{{ route('admin.tagihan.show', $tgh) }}" class="inline-flex items-center px-2 py-1 bg-cyan-300 hover:bg-cyan-400 text-black border-2 border-black dark:border-white font-black text-xs uppercase shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1.5px_1.5px_0px_0px_rgba(255,255,255,1)] hover:translate-y-[-1px] hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] active:translate-y-0 active:shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] transition-all cursor-pointer">
                                                 Detail
                                             </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 italic">
                                            Tidak ada data tagihan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $tagihan->links('vendor.pagination.neo-brutalist') }}
                    </div>
                </div>
            </div>
        </div>
    </div>


</x-app-layout>
