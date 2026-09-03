<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Riwayat Pembayaran') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-white dark:bg-slate-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

            <!-- Statistics Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Card 1: Booking Paid -->
                <div class="bg-emerald-300 dark:bg-emerald-950 p-5 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] transition-all duration-200 hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#000000] dark:hover:shadow-[6px_6px_0px_0px_#ffffff]">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black dark:text-slate-200">Total Bayar Booking</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-900 text-black dark:text-white">
                            🔑
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-black text-black dark:text-white tracking-tight">Rp {{ number_format($totalBookingPaid, 0, ',', '.') }}</div>
                        <div class="text-[10px] text-slate-800 dark:text-slate-300 font-extrabold mt-1">Pembayaran uang muka/penuh pemesanan</div>
                    </div>
                </div>

                <!-- Card 2: Bills Paid -->
                <div class="bg-purple-300 dark:bg-purple-950 p-5 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] transition-all duration-200 hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#000000] dark:hover:shadow-[6px_6px_0px_0px_#ffffff]">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black dark:text-slate-200">Total Bayar Bulanan</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-900 text-black dark:text-white">
                            📅
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-black text-black dark:text-white tracking-tight">Rp {{ number_format($totalBillsPaid, 0, ',', '.') }}</div>
                        <div class="text-[10px] text-slate-800 dark:text-slate-300 font-extrabold mt-1">Pembayaran tagihan sewa bulanan</div>
                    </div>
                </div>

                <!-- Card 3: Success Count -->
                <div class="bg-yellow-300 dark:bg-yellow-950 p-5 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] transition-all duration-200 hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#000000] dark:hover:shadow-[6px_6px_0px_0px_#ffffff]">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black dark:text-slate-200">Transaksi Berhasil</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-900 text-black dark:text-white">
                            💳
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-3xl font-black text-black dark:text-white tracking-tight">{{ $successTransCount }} Transaksi</div>
                        <div class="text-[10px] text-slate-800 dark:text-slate-300 font-extrabold mt-1">Jumlah transaksi sukses/lunas</div>
                    </div>
                </div>
            </div>

            <!-- BAGIAN 1: Pembayaran Booking Kamar (Reservasi) -->
            <div class="admin-card">
                <div class="mb-6 border-b-4 border-black dark:border-white pb-4">
                    <h3 class="text-base font-black text-slate-900 dark:text-slate-100 uppercase tracking-wide">Pembayaran Booking Kamar (Reservasi)</h3>
                    <p class="admin-subtitle">Log pembayaran awal/DP ketika memesan kamar kost pertama kali.</p>
                </div>
                
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead class="admin-table-thead">
                            <tr>
                                <th class="admin-table-th">Order ID</th>
                                <th class="admin-table-th">Kamar</th>
                                <th class="admin-table-th">Jenis Sewa</th>
                                <th class="admin-table-th text-right">Total Pembayaran</th>
                                <th class="admin-table-th">Tanggal Update</th>
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
                                        {{ $item->tipe_sewa }} ({{ $item->is_dp ? 'DP 30%' : 'Penuh' }})
                                    </td>
                                    <td class="admin-table-td text-right font-extrabold text-slate-800 dark:text-slate-100">
                                        Rp {{ number_format($item->is_dp ? $item->nominal_dp : $item->total_harga, 0, ',', '.') }}
                                    </td>
                                    <td class="admin-table-td text-slate-500 dark:text-slate-400 font-semibold">
                                        {{ $item->updated_at ? $item->updated_at->format('d M Y H:i') : '-' }}
                                    </td>
                                    <td class="admin-table-td">
                                        <span class="admin-badge {{ $item->status_badge_class }}">
                                            {{ strtoupper($item->status) }}
                                        </span>
                                    </td>
                                    <td class="admin-table-td">
                                        <a href="{{ route('penyewa.reservasi.pembayaran', $item->id) }}" class="admin-btn-primary py-1 px-3 text-[10px]">
                                            Rincian
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 italic">
                                        Tidak ada riwayat pembayaran booking kamar.
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

            <!-- BAGIAN 2: Pembayaran Tagihan Bulanan (Hanya Untuk Penyewa Aktif) -->
            @if(auth()->user()->penyewa)
                <div class="admin-card">
                    <div class="mb-6 border-b-4 border-black dark:border-white pb-4">
                        <h3 class="text-base font-black text-slate-900 dark:text-slate-100 uppercase tracking-wide">Pembayaran Tagihan Bulanan</h3>
                        <p class="admin-subtitle">Log riwayat pembayaran tagihan sewa rutin bulanan Anda.</p>
                    </div>

                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead class="admin-table-thead">
                                <tr>
                                    <th class="admin-table-th">Invoice ID</th>
                                    <th class="admin-table-th">Periode</th>
                                    <th class="admin-table-th text-right">Total Tagihan</th>
                                    <th class="admin-table-th">Metode</th>
                                    <th class="admin-table-th">Status</th>
                                    <th class="admin-table-th">Tanggal Bayar</th>
                                    <th class="admin-table-th">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="admin-table-tbody">
                                @forelse($tagihan as $t)
                                    <tr class="admin-table-tr">
                                        <td class="admin-table-td text-slate-500 dark:text-slate-400 font-bold">
                                            {{ $t->order_id }}
                                        </td>
                                        <td class="admin-table-td text-slate-700 dark:text-slate-300 font-bold">
                                            {{ sprintf('%02d/%d', $t->periode_bulan, $t->periode_tahun) }}
                                        </td>
                                        <td class="admin-table-td text-right font-extrabold text-slate-800 dark:text-slate-100">
                                            Rp {{ number_format($t->nominal_total, 0, ',', '.') }}
                                        </td>
                                        <td class="admin-table-td capitalize text-slate-500 dark:text-slate-400 font-medium">
                                            {{ $t->metode_pembayaran ?: '-' }}
                                        </td>
                                        <td class="admin-table-td">
                                            <span class="admin-badge {{ $t->status_badge_class }}">
                                                {{ strtoupper($t->status) }}
                                            </span>
                                        </td>
                                        <td class="admin-table-td text-slate-500 dark:text-slate-400 font-semibold">
                                            {{ $t->pembayaran_terkonfirmasi?->tanggal_bayar?->format('d M Y H:i') ?? '-' }}
                                        </td>
                                        <td class="admin-table-td">
                                            @if(auth()->user()->penyewa && auth()->user()->penyewa->status === 'aktif')
                                                <a href="{{ route('penyewa.tagihan.show', $t->id) }}" class="admin-btn-primary py-1 px-3 text-[10px]">
                                                    Detail
                                                </a>
                                            @else
                                                <span class="text-xs text-slate-400 font-semibold">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 italic">
                                            Tidak ada riwayat tagihan bulanan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($tagihan instanceof \Illuminate\Pagination\LengthAwarePaginator && $tagihan->hasPages())
                        <div class="mt-6">
                            {{ $tagihan->links('vendor.pagination.neo-brutalist') }}
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
