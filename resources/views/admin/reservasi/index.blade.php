<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Daftar Reservasi Kamar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">

                    <!-- Alert message -->
                    <x-flash-message />

                    <!-- Header Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100">Manajemen Reservasi Calon Penyewa</h3>
                        <p class="admin-subtitle">Verifikasi pembayaran DP / lunas penuh reservasi kamar kost yang dilakukan calon penyewa.</p>
                    </div>

                    <!-- Filter Bar (Server-side) -->
                    <form method="GET" action="{{ route('admin.reservasi.index') }}" class="bg-slate-50 dark:bg-slate-900 border-4 border-black dark:border-white p-5 rounded-none mb-6 flex flex-wrap gap-4 items-end shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                        <div class="flex-1 min-w-[250px]">
                            <label for="search-input" class="admin-label">Cari Reservasi</label>
                            <input type="text" id="search-input" name="search" class="admin-input !py-1.5" placeholder="Cari Order ID, nama calon penyewa, nomor kamar..." value="{{ request('search') }}">
                        </div>
                        <div class="min-w-[150px]">
                            <label for="status-filter" class="admin-label">Status Reservasi</label>
                            <select id="status-filter" name="status" class="admin-select-sm w-full">
                                <option value="">Semua Status</option>
                                @foreach(['PENDING', 'DP', 'LUNAS', 'DIKONFIRMASI', 'BATAL'] as $st)
                                    <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                                @endforeach
                                <option value="deleted" {{ request('status') === 'deleted' ? 'selected' : '' }}>Terhapus (Trash)</option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="admin-btn-primary !py-2 !px-4">
                                Filter
                            </button>
                            <a href="{{ route('admin.reservasi.index') }}" class="admin-btn-secondary !py-2 !px-4">
                                Reset
                            </a>
                        </div>
                    </form>

                    <div class="admin-table-container">
                        <table class="admin-table" id="reservasi-table">
                            <thead class="admin-table-thead">
                                <tr>
                                    <th class="admin-table-th">Order ID</th>
                                    <th class="admin-table-th">Calon Penyewa</th>
                                    <th class="admin-table-th">Kamar</th>
                                    <th class="admin-table-th">Tipe Sewa</th>
                                    <th class="admin-table-th">Mulai</th>
                                    <th class="admin-table-th">Status</th>
                                    <th class="admin-table-th">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="admin-table-tbody">
                                @forelse($reservasi as $res)
                                    <tr class="admin-table-tr">
                                        <td class="admin-table-td font-extrabold text-slate-800 dark:text-slate-100">
                                            {{ $res->order_id ?? 'RSV-' . $res->id }}
                                        </td>
                                        <td class="admin-table-td font-semibold text-slate-800 dark:text-slate-200">
                                            {{ $res->user?->nama ?? '-' }}
                                        </td>
                                        <td class="admin-table-td font-bold text-slate-800 dark:text-slate-100">
                                            Kamar {{ $res->kamar?->nomor_kamar ?? '-' }}
                                        </td>
                                        <td class="admin-table-td capitalize font-medium text-slate-500 dark:text-slate-400">
                                            {{ $res->tipe_sewa }}
                                        </td>
                                        <td class="admin-table-td text-slate-500 dark:text-slate-400 font-semibold">
                                            {{ $res->tanggal_mulai ? $res->tanggal_mulai->format('d M Y') : '-' }}
                                        </td>
                                        <td class="admin-table-td">
                                            <x-reservation-status-badge :status="$res->status" />
                                        </td>
                                        <td class="admin-table-td">
                                             <div class="flex items-center gap-2">
                                                 @if($res->trashed())
                                                     <form action="{{ route('admin.reservasi.restore', $res->id) }}" method="POST" class="inline-block">
                                                         @csrf
                                                         <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold text-xs shadow-sm hover:shadow-md transition cursor-pointer">
                                                             Restore
                                                         </button>
                                                     </form>
                                                 @else
                                                     <a href="{{ route('admin.reservasi.show', $res->id) }}" class="inline-flex items-center px-2 py-1 bg-cyan-300 hover:bg-cyan-400 text-black border-2 border-black dark:border-white rounded-none font-black text-xs uppercase shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1.5px_1.5px_0px_0px_rgba(255,255,255,1)] hover:translate-y-[-1px] hover:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] active:translate-y-0 active:shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] transition-all cursor-pointer">
                                                         Detail
                                                     </a>
                                                     @if(strtolower($res->status) === 'batal')
                                                         <form action="{{ route('admin.reservasi.destroy', $res->id) }}" method="POST" class="inline-block" data-confirm="Apakah Anda yakin ingin menghapus reservasi ini secara permanen dari database?" data-title="Konfirmasi Hapus" data-confirm-danger="true">
                                                             @csrf
                                                             @method('DELETE')
                                                             <button type="submit" class="inline-flex items-center px-2 py-1 bg-red-400 hover:bg-red-500 text-black border-2 border-black dark:border-white rounded-none font-black text-xs uppercase shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1.5px_1.5px_0px_0px_rgba(255,255,255,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all cursor-pointer">
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
                                        <td colspan="7" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 italic">
                                            Tidak ada data reservasi masuk.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $reservasi->links('vendor.pagination.neo-brutalist') }}
                    </div>
                </div>
            </div>


        </div>
    </div>


</x-app-layout>
