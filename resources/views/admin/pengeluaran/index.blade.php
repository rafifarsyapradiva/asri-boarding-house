<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Pencatatan Pengeluaran (Arus Kas Keluar)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Notification Alerts -->
            @if(session('success'))
                <div class="p-4 bg-emerald-300 dark:bg-emerald-950 text-black dark:text-white border-4 border-black dark:border-white rounded-none shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                    <div class="flex items-center">
                        <span class="mr-2 font-black">✅</span>
                        <span class="font-black text-sm">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-red-400 dark:bg-red-950 text-white dark:text-red-200 border-4 border-black dark:border-white rounded-none shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                    <div class="flex items-center">
                        <span class="mr-2 font-black">❌</span>
                        <span class="font-black text-sm">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">

                    <!-- Header Section -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100">Daftar Pengeluaran Operasional Kost</h3>
                            <p class="admin-subtitle">Kelola pencatatan arus kas keluar, perbaikan fasilitas, utilitas, dan lainnya.</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @php
                                $filterParams = request()->only(['search', 'kategori', 'tanggal_mulai', 'tanggal_selesai']);
                            @endphp
                            <a href="{{ route('admin.pengeluaran.exportPdf', $filterParams) }}" class="admin-btn-danger">
                                📄 Export PDF
                            </a>
                            <a href="{{ route('admin.pengeluaran.exportExcel', $filterParams) }}" class="admin-btn-success">
                                📊 Export Excel
                            </a>
                            <a href="{{ route('admin.pengeluaran.create') }}" class="admin-btn-primary">
                                <span class="text-sm">+</span> Tambah Pengeluaran
                            </a>
                        </div>
                    </div>

                    <!-- Filter Bar (Server-side) -->
                    <form method="GET" action="{{ route('admin.pengeluaran.index') }}" class="bg-slate-50 dark:bg-slate-900 border-4 border-black dark:border-white p-5 rounded-none mb-8 flex flex-wrap gap-4 items-end shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                        <div class="flex-1 min-w-[200px]">
                            <label for="search" class="admin-label">Cari Pengeluaran</label>
                            <input type="text" id="search" name="search" class="admin-input !py-1.5" placeholder="Cari nama pengeluaran..." value="{{ request('search') }}">
                        </div>

                        <div class="min-w-[150px]">
                            <label for="kategori" class="admin-label">Kategori</label>
                            <select id="kategori" name="kategori" class="admin-select !py-1.5">
                                <option value="">Semua Kategori</option>
                                <option value="maintenance" {{ request('kategori') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="utilitas" {{ request('kategori') == 'utilitas' ? 'selected' : '' }}>Utilitas</option>
                                <option value="operasional" {{ request('kategori') == 'operasional' ? 'selected' : '' }}>Operasional</option>
                                <option value="lainnya" {{ request('kategori') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>

                        <div class="min-w-[150px]">
                            <label for="tanggal_mulai" class="admin-label">Tanggal Mulai</label>
                            <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="admin-input !py-1.5" value="{{ request('tanggal_mulai') }}">
                        </div>

                        <div class="min-w-[150px]">
                            <label for="tanggal_selesai" class="admin-label">Tanggal Selesai</label>
                            <input type="date" id="tanggal_selesai" name="tanggal_selesai" class="admin-input !py-1.5" value="{{ request('tanggal_selesai') }}">
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="admin-btn-primary !py-2 !px-4">
                                Filter
                            </button>
                            <a href="{{ route('admin.pengeluaran.index') }}" class="admin-btn-secondary !py-2 !px-4">
                                Reset
                            </a>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead class="admin-table-thead">
                                <tr>
                                    <th class="admin-table-th">Tanggal</th>
                                    <th class="admin-table-th">Nama Pengeluaran</th>
                                    <th class="admin-table-th">Kategori</th>
                                    <th class="admin-table-th text-right">Nominal</th>
                                    <th class="admin-table-th">Nota</th>
                                    <th class="admin-table-th">Keterangan</th>
                                    <th class="admin-table-th">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="admin-table-tbody">
                                @forelse($pengeluaran as $p)
                                    <tr class="admin-table-tr">
                                        <!-- Tanggal -->
                                        <td class="admin-table-td text-slate-500 dark:text-slate-400 font-semibold">
                                            {{ $p->tanggal_pengeluaran->format('d M Y') }}
                                        </td>
                                        <!-- Nama -->
                                        <td class="admin-table-td font-bold text-slate-800 dark:text-slate-100">
                                            {{ $p->nama_pengeluaran }}
                                        </td>
                                        <!-- Kategori -->
                                        <td class="admin-table-td">
                                            @php
                                                $badgeClass = match($p->kategori) {
                                                    'maintenance' => 'admin-badge-warning',
                                                    'utilitas' => 'admin-badge-info',
                                                    'operasional' => 'admin-badge-success',
                                                    default => 'admin-badge-neutral',
                                                };
                                            @endphp
                                            <span class="admin-badge {{ $badgeClass }}">
                                                {{ strtoupper($p->kategori) }}
                                            </span>
                                        </td>
                                        <!-- Nominal -->
                                        <td class="admin-table-td text-right font-extrabold text-slate-800 dark:text-slate-100">
                                            Rp {{ number_format($p->nominal, 0, ',', '.') }}
                                        </td>
                                        <!-- Nota -->
                                        <td class="admin-table-td">
                                            @if($p->bukti_nota)
                                                <a href="{{ asset('storage/' . $p->bukti_nota) }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center gap-1 font-semibold text-xs">
                                                    🖼️ Lihat Nota
                                                </a>
                                            @else
                                                <span class="text-slate-400 dark:text-slate-500 italic text-xs">Tidak ada</span>
                                            @endif
                                        </td>
                                        <!-- Keterangan -->
                                        <td class="admin-table-td text-slate-500 dark:text-slate-400 text-xs truncate max-w-[200px]" title="{{ $p->keterangan }}">
                                            {{ $p->keterangan ?: '-' }}
                                        </td>
                                        <!-- Aksi -->
                                        <td class="admin-table-td">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('admin.pengeluaran.show', $p->id) }}" class="inline-flex items-center px-2 py-1 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600 text-black border-2 border-black dark:border-white rounded-none font-black text-xs uppercase shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1.5px_1.5px_0px_0px_rgba(255,255,255,1)] hover:translate-y-[-1px] hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] active:translate-y-0 active:shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] transition-all cursor-pointer">
                                                    Detail
                                                </a>
                                                <a href="{{ route('admin.pengeluaran.edit', $p->id) }}" class="inline-flex items-center px-2 py-1 bg-cyan-300 hover:bg-cyan-400 text-black border-2 border-black dark:border-white rounded-none font-black text-xs uppercase shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1.5px_1.5px_0px_0px_rgba(255,255,255,1)] hover:translate-y-[-1px] hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] active:translate-y-0 active:shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] transition-all cursor-pointer">
                                                    Edit
                                                </a>
                                                <form action="{{ route('admin.pengeluaran.destroy', $p->id) }}" method="POST" class="inline" data-confirm="Apakah Anda yakin ingin menghapus pencatatan pengeluaran ini?" data-title="Hapus Pengeluaran" data-confirm-danger="true">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-2 py-1 bg-red-400 hover:bg-red-500 text-white border-2 border-black dark:border-white rounded-none font-black text-xs uppercase shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1.5px_1.5px_0px_0px_rgba(255,255,255,1)] hover:translate-y-[-1px] hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] active:translate-y-0 active:shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] transition-all cursor-pointer">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 italic">Belum ada data pengeluaran kost.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $pengeluaran->links('vendor.pagination.neo-brutalist') }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
