<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black uppercase tracking-wider">
            {{ __('Riwayat Pemesanan Kamar') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-white dark:bg-slate-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 bg-yellow-400 text-black border-4 border-black font-black uppercase tracking-wide shadow-[4px_4px_0px_0px_#000000] text-sm">
                    {{ session('success') }}
                </div>
            @endif


            <!-- Statistics Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Card 1: Total Pemesanan -->
                <div class="bg-cyan-300 dark:bg-cyan-950 p-5 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] transition-all duration-200 hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#000000] dark:hover:shadow-[6px_6px_0px_0px_#ffffff]">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black dark:text-slate-200">Total Pemesanan</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-900 text-black dark:text-white">
                            📋
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-3xl font-black text-black dark:text-white tracking-tight">{{ $totalPemesanan }}</div>
                        <div class="text-[10px] text-slate-800 dark:text-slate-300 font-extrabold mt-1">Keseluruhan pemesanan Anda</div>
                    </div>
                </div>

                <!-- Card 2: Menunggu Pembayaran -->
                <div class="bg-yellow-300 dark:bg-yellow-950 p-5 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] transition-all duration-200 hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#000000] dark:hover:shadow-[6px_6px_0px_0px_#ffffff]">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black dark:text-slate-200">Menunggu Pembayaran</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-900 text-black dark:text-white">
                            ⏳
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-3xl font-black text-black dark:text-white tracking-tight">{{ $menungguBayar }}</div>
                        <div class="text-[10px] text-slate-800 dark:text-slate-300 font-extrabold mt-1">Silakan lakukan pembayaran</div>
                    </div>
                </div>

                <!-- Card 3: Pemesanan Aktif -->
                <div class="bg-emerald-300 dark:bg-emerald-950 p-5 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] transition-all duration-200 hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#000000] dark:hover:shadow-[6px_6px_0px_0px_#ffffff]">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black dark:text-slate-200">Pemesanan Aktif</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-900 text-black dark:text-white">
                            ✅
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-3xl font-black text-black dark:text-white tracking-tight">{{ $pemesananAktif }}</div>
                        <div class="text-[10px] text-slate-800 dark:text-slate-300 font-extrabold mt-1">Dikonfirmasi / Lunas / DP</div>
                    </div>
                </div>

                <!-- Card 4: Dibatalkan -->
                <div class="bg-red-300 dark:bg-red-950 p-5 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] transition-all duration-200 hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#000000] dark:hover:shadow-[6px_6px_0px_0px_#ffffff]">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black dark:text-slate-200">Dibatalkan</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-900 text-black dark:text-white">
                            ❌
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-3xl font-black text-black dark:text-white tracking-tight">{{ $pemesananBatal }}</div>
                        <div class="text-[10px] text-slate-800 dark:text-slate-300 font-extrabold mt-1">Pemesanan yang batal</div>
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="admin-card">
                <div class="mb-6 border-b-4 border-black dark:border-white pb-4">
                    <h3 class="text-base font-black text-slate-900 dark:text-slate-100 uppercase tracking-wide">Daftar Riwayat Pemesanan Kamar</h3>
                    <p class="admin-subtitle">Seluruh log pemesanan kamar boarding house Anda.</p>
                </div>

                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead class="admin-table-thead">
                            <tr>
                                <th class="admin-table-th">Order ID</th>
                                <th class="admin-table-th">Kamar</th>
                                <th class="admin-table-th">Tipe Sewa</th>
                                <th class="admin-table-th">Durasi</th>
                                <th class="admin-table-th">Mulai</th>
                                <th class="admin-table-th text-right">Total</th>
                                <th class="admin-table-th">Status</th>
                                <th class="admin-table-th">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="admin-table-tbody">
                            @forelse($reservasi as $item)
                                <tr class="admin-table-tr">
                                    <td class="admin-table-td text-slate-500 dark:text-slate-400 font-bold">
                                        {{ $item->order_id ?? 'RSV-' . $item->id }}
                                    </td>
                                    <td class="admin-table-td text-slate-700 dark:text-slate-300 font-bold">
                                        Kamar {{ $item->kamar?->nomor_kamar ?? '-' }}
                                    </td>
                                    <td class="admin-table-td capitalize text-slate-500 dark:text-slate-400 font-semibold">
                                        {{ $item->tipe_sewa }}
                                    </td>
                                    <td class="admin-table-td text-slate-500 dark:text-slate-400 font-medium">
                                        {{ $item->durasi }} {{ $item->tipe_sewa === 'harian' ? 'Hari' : ($item->tipe_sewa === 'mingguan' ? 'Minggu' : 'Bulan') }}
                                    </td>
                                    <td class="admin-table-td text-slate-500 dark:text-slate-400 font-semibold">
                                        {{ $item->tanggal_mulai ? $item->tanggal_mulai->format('d M Y') : '-' }}
                                    </td>
                                    <td class="admin-table-td text-right font-extrabold text-slate-800 dark:text-slate-100">
                                        Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                                    </td>
                                    <td class="admin-table-td">
                                        <span class="admin-badge {{ $item->status_badge_class }}">
                                            {{ strtoupper($item->status) }}
                                        </span>
                                    </td>
                                    <td class="admin-table-td">
                                        <a href="{{ route('penyewa.reservasi.pembayaran', $item->id) }}" class="admin-btn-primary py-1 px-3 text-[10px]">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 italic">
                                        Anda belum memiliki riwayat pemesanan kamar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($reservasi instanceof \Illuminate\Pagination\LengthAwarePaginator && $reservasi->hasPages())
                    <div class="mt-6">
                        {{ $reservasi->links('vendor.pagination.neo-brutalist') }}
                    </div>
                @endif
            </div>
            
        </div>
    </div>
</x-app-layout>
