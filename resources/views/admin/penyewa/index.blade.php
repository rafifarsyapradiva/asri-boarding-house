<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Manajemen Penyewa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">

                    <!-- Alert message -->
                    <x-flash-message />

                    <!-- Header Section -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100">Daftar Penyewa Kost</h3>
                            <p class="admin-subtitle">Kelola informasi penyewa aktif, wali, tipe sewa, dan status checkout.</p>
                        </div>
                        <div class="flex flex-wrap gap-3 items-center w-full sm:w-auto">
                            <a id="btn-export-pdf" data-testid="btn-export-pdf" href="{{ route('admin.penyewa.exportPdf', request()->query()) }}" class="inline-flex items-center justify-center bg-cyan-400 hover:bg-cyan-500 text-black font-black py-2.5 px-4 border-4 border-black rounded-none shadow-[4px_4px_0px_0px_#000000] hover:translate-x-0.5 hover:translate-y-0.5 active:translate-x-1 active:translate-y-1 active:shadow-none transition-all duration-100 text-xs uppercase tracking-wider gap-2 cursor-pointer">
                                📄 Export PDF
                            </a>
                            <a id="btn-export-csv" data-testid="btn-export-csv" href="{{ route('admin.penyewa.exportCsv', request()->query()) }}" class="inline-flex items-center justify-center bg-emerald-400 hover:bg-emerald-500 text-black font-black py-2.5 px-4 border-4 border-black rounded-none shadow-[4px_4px_0px_0px_#000000] hover:translate-x-0.5 hover:translate-y-0.5 active:translate-x-1 active:translate-y-1 active:shadow-none transition-all duration-100 text-xs uppercase tracking-wider gap-2 cursor-pointer">
                                📊 Export Excel/CSV
                            </a>
                            <a href="{{ route('admin.penyewa.create') }}" data-testid="btn-tambah-penyewa" class="admin-btn-primary">
                                <span class="text-sm">+</span> Tambah Penyewa
                            </a>
                        </div>
                    </div>

                    <!-- Search Input & Live Filter -->
                    <form method="GET" action="{{ route('admin.penyewa.index') }}" class="bg-yellow-100 dark:bg-slate-900 border-4 border-black dark:border-white p-4 rounded-none mb-6 flex flex-wrap gap-4 items-center shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                        <div class="flex-1 min-w-[250px]">
                            <label for="search-input" class="admin-label">Cari Penyewa</label>
                            <div class="flex gap-2">
                                <input type="text" name="search" id="search-input" data-testid="search-input" value="{{ request('search') }}" class="admin-input !py-1.5" placeholder="Cari nama, email, nomor kamar, atau nomor HP...">
                                <button type="submit" class="admin-btn-primary !py-1.5">Cari</button>
                                @if(request()->anyFilled(['search', 'status']))
                                    <a href="{{ route('admin.penyewa.index') }}" class="admin-btn-secondary !py-1.5 !px-4 text-xs font-black">
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="min-w-[150px]">
                            <label for="status-filter" class="admin-label">Status Keaktifan</label>
                            <select id="status-filter" name="status" data-testid="status-filter" class="admin-select-sm w-full" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                <option value="deleted" {{ request('status') === 'deleted' ? 'selected' : '' }}>Terhapus (Trash)</option>
                            </select>
                        </div>
                    </form>

                    <!-- Table List -->
                    <div class="admin-table-container">
                        <table class="admin-table" id="tenant-table" data-testid="tenant-table">
                            <thead class="admin-table-thead">
                                <tr>
                                    <th scope="col" class="admin-table-th">Nama / Kontak</th>
                                    <th scope="col" class="admin-table-th">Kamar</th>
                                    <th scope="col" class="admin-table-th">NIK</th>
                                    <th scope="col" class="admin-table-th">Tipe Sewa (Durasi)</th>
                                    <th scope="col" class="admin-table-th">Tanggal Masuk</th>
                                    <th scope="col" class="admin-table-th">Wali / HP Wali</th>
                                    <th scope="col" class="admin-table-th">Status</th>
                                    <th scope="col" class="admin-table-th">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="admin-table-tbody">
                                @forelse($penyewa as $penyewaItem)
                                    <tr class="admin-table-tr" data-status="{{ $penyewaItem->status }}" data-testid="penyewa-row-{{ $penyewaItem->id }}">
                                        <!-- Nama / Kontak -->
                                        <td class="admin-table-td">
                                            <div class="font-extrabold text-slate-800 dark:text-slate-100 search-field">{{ $penyewaItem->user->nama ?? '-' }}</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400 search-field">{{ $penyewaItem->user->email ?? '-' }}</div>
                                            <div class="text-xs text-slate-400 dark:text-slate-500 search-field">{{ $penyewaItem->user->no_hp ?? '-' }}</div>
                                        </td>
                                        <!-- Kamar -->
                                        <td class="admin-table-td">
                                            @if($penyewaItem->kamar)
                                                <span class="font-bold text-slate-800 dark:text-slate-100 search-field">Kamar {{ $penyewaItem->kamar->nomor_kamar }}</span>
                                                <div class="text-xs text-slate-400 dark:text-slate-500 capitalize">{{ $penyewaItem->kamar->tipe }}</div>
                                            @else
                                                <span class="text-slate-400 italic text-xs">Belum ditentukan</span>
                                            @endif
                                        </td>
                                        <!-- NIK -->
                                        <td class="admin-table-td text-slate-500 dark:text-slate-400 font-medium">
                                            {{ $penyewaItem->nik }}
                                        </td>
                                        <!-- Tipe Sewa -->
                                        <td class="admin-table-td capitalize font-semibold text-slate-800 dark:text-slate-200">
                                            {{ $penyewaItem->durasi_formatted ?? ($penyewaItem->tipe_sewa . ' (' . $penyewaItem->durasi . ')') }}
                                            <div class="text-xs text-slate-500 dark:text-slate-400 font-normal mt-0.5">
                                                Rp {{ number_format($penyewaItem->harga_sewa ?? ($penyewaItem->kamar ? $penyewaItem->kamar->harga_bulan : 0), 0, ',', '.') }}/bln
                                            </div>
                                        </td>
                                        <!-- Tanggal Masuk -->
                                        <td class="admin-table-td text-slate-500 dark:text-slate-400 font-medium">
                                            {{ \Carbon\Carbon::parse($penyewaItem->tanggal_masuk)->format('d M Y') }}
                                        </td>
                                        <!-- Wali -->
                                        <td class="admin-table-td">
                                            <div class="text-slate-800 dark:text-slate-200 font-bold">{{ $penyewaItem->nama_wali }}</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $penyewaItem->no_wali }}</div>
                                        </td>
                                        <!-- Status -->
                                        <td class="admin-table-td">
                                            @if($penyewaItem->status === 'aktif')
                                                <span class="admin-badge admin-badge-success">Aktif</span>
                                            @else
                                                <span class="admin-badge admin-badge-danger">Nonaktif</span>
                                            @endif
                                        </td>
                                        <!-- Aksi -->
                                        <td class="admin-table-td">
                                            <div class="flex items-center gap-3">
                                                @if($penyewaItem->trashed())
                                                    <form action="{{ route('admin.penyewa.restore', $penyewaItem->id) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold text-xs shadow-sm hover:shadow-md transition cursor-pointer">
                                                            Restore
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('admin.penyewa.show', $penyewaItem->id) }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white transition duration-150">
                                                        Detail
                                                    </a>
                                                    <a href="{{ route('admin.penyewa.edit', $penyewaItem->id) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition duration-150">
                                                        Edit
                                                    </a>
                                                     @if($penyewaItem->status === 'aktif')
                                                         <a href="{{ route('admin.penyewa.checkout.form', $penyewaItem->id) }}" class="text-xs font-bold text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition duration-150">
                                                             Checkout
                                                         </a>
                                                     @endif
                                                    @if(($penyewaItem->tagihan_count ?? 0) == 0)
                                                        <form action="{{ route('admin.penyewa.destroy', $penyewaItem->id) }}" method="POST" class="inline-block" data-confirm="Apakah Anda yakin ingin menghapus permanen penyewa dan akun loginnya?" data-title="Hapus Penyewa" data-confirm-danger="true">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-xs font-bold text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition duration-150">
                                                                Hapus
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 italic">Belum ada data penyewa kost.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $penyewa->links('vendor.pagination.neo-brutalist') }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
