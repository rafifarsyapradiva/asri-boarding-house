<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Laporan Penagihan Bulanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filter Bar -->
            <div class="admin-card">
                <div class="mb-6 border-b border-slate-100 dark:border-slate-700/50 pb-4">
                    <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100">Filter & Ekspor Laporan</h3>
                    <p class="admin-subtitle">Saring data penagihan berdasarkan periode dan status, atau unduh laporan dalam format dokumen.</p>
                </div>

                <form method="GET" action="{{ route('admin.laporan.index') }}" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[150px]">
                        <label for="bulan" class="admin-label">Bulan</label>
                        <select id="bulan" name="bulan" class="admin-select !py-2">
                            <option value="">Semua Bulan</option>
                            @php
                                $daftarBulan = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                            @endphp
                            @foreach ($daftarBulan as $angkaBulan => $namaBulan)
                                <option value="{{ $angkaBulan }}" {{ request('bulan') == $angkaBulan ? 'selected' : '' }}>
                                    {{ $namaBulan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex-1 min-w-[150px]">
                        <label for="tahun" class="admin-label">Tahun</label>
                        <select id="tahun" name="tahun" class="admin-select !py-2">
                            <option value="">Semua Tahun</option>
                            @php
                                $tahunSekarang = (int) date('Y');
                                $tahunMulai = $tahunSekarang - 3;
                                $tahunSelesai = $tahunSekarang + 1;
                            @endphp
                            @for ($tahun = $tahunMulai; $tahun <= $tahunSelesai; $tahun++)
                                <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                                    {{ $tahun }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="flex-1 min-w-[150px]">
                        <label for="status" class="admin-label">Status</label>
                        <select id="status" name="status" class="admin-select !py-2">
                            <option value="">Semua Status</option>
                            @foreach(['pending' => 'Pending', 'terlambat' => 'Terlambat', 'lunas' => 'Lunas', 'gagal' => 'Gagal', 'kadaluarsa' => 'Kadaluarsa'] as $keyStatus => $labelStatus)
                                <option value="{{ $keyStatus }}" {{ request('status') == $keyStatus ? 'selected' : '' }}>
                                    {{ $labelStatus }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="admin-btn-primary !py-2.5 !px-5">
                            Filter
                        </button>
                        <a href="{{ route('admin.laporan.index') }}" class="admin-btn-secondary !py-2.5 !px-5">
                            Reset
                        </a>
                    </div>

                    <div class="ml-auto flex gap-2">
                        <a href="{{ route('admin.laporan.exportPdf', request()->all()) }}" class="inline-flex items-center justify-center bg-rose-400 hover:bg-rose-500 text-black font-black py-2.5 px-5 border-4 border-black dark:border-white rounded-none shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)] active:translate-x-[0px] active:translate-y-[0px] active:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] transition-all duration-150 text-xs uppercase tracking-wider gap-2 cursor-pointer">
                            <span>📄</span> PDF
                        </a>
                        <a href="{{ route('admin.laporan.exportExcel', request()->all()) }}" class="inline-flex items-center justify-center bg-emerald-400 hover:bg-emerald-500 text-black font-black py-2.5 px-5 border-4 border-black dark:border-white rounded-none shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)] active:translate-x-[0px] active:translate-y-[0px] active:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] transition-all duration-150 text-xs uppercase tracking-wider gap-2 cursor-pointer">
                            <span>📊</span> Excel
                        </a>
                    </div>
                </form>
            </div>

            <!-- Neraca Kas Sederhana Section -->
            @php
                $isPositif = $saldoBersih >= 0;
                $cardBgSaldo = $isPositif ? 'bg-emerald-300 dark:bg-emerald-950' : 'bg-red-300 dark:bg-red-950';
                $textColorSaldo = $isPositif ? 'dark:text-emerald-200' : 'dark:text-red-200';
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card: Arus Kas Masuk -->
                <div class="bg-blue-300 dark:bg-blue-950 p-5 rounded-none border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] transition-all duration-150 hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)]">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black dark:text-blue-200">Total Arus Kas Masuk</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-800 text-black dark:text-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] rounded-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-black text-black dark:text-white">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</div>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t-2 border-black dark:border-white text-[10px] text-black dark:text-blue-200 font-extrabold">
                            <span>Total penerimaan dari tagihan lunas</span>
                        </div>
                    </div>
                </div>

                <!-- Card: Arus Kas Keluar -->
                <div class="bg-rose-300 dark:bg-rose-950 p-5 rounded-none border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] transition-all duration-150 hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)]">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black dark:text-rose-200">Total Arus Kas Keluar</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-800 text-black dark:text-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] rounded-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-black text-black dark:text-white">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</div>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t-2 border-black dark:border-white text-[10px] text-black dark:text-rose-200 font-extrabold">
                            <span>Total pengeluaran operasional & lainnya</span>
                        </div>
                    </div>
                </div>

                <!-- Card: Saldo Bersih Akhir -->
                <div class="{{ $cardBgSaldo }} p-5 rounded-none border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] transition-all duration-150 hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)]">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black {{ $textColorSaldo }}">Saldo Bersih Akhir</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-800 text-black dark:text-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] rounded-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-black text-black dark:text-white">Rp {{ number_format($saldoBersih, 0, ',', '.') }}</div>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t-2 border-black dark:border-white text-[10px] text-black {{ $textColorSaldo }} font-extrabold">
                            <span>Sisa kas (Pemasukan - Pengeluaran)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="admin-card">
                <div class="mb-6 border-b border-slate-100 dark:border-slate-700/50 pb-4">
                    <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100">Riwayat Keuangan Kost</h3>
                    <p class="admin-subtitle">Data transaksi keuangan yang masuk ke sistem sesuai filter yang aktif.</p>
                </div>

                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead class="admin-table-thead">
                            <tr>
                                <th class="admin-table-th">Order ID</th>
                                <th class="admin-table-th">Periode</th>
                                <th class="admin-table-th">Penyewa</th>
                                <th class="admin-table-th">Nomor Kamar</th>
                                <th class="admin-table-th text-right">Total Tagihan</th>
                                <th class="admin-table-th">Metode</th>
                                <th class="admin-table-th">Status</th>
                            </tr>
                        </thead>
                        <tbody class="admin-table-tbody">
                            @forelse($tagihan as $itemTagihan)
                                <tr class="admin-table-tr">
                                    <td class="admin-table-td font-extrabold text-slate-800 dark:text-slate-100">{{ $itemTagihan->order_id }}</td>
                                    <td class="admin-table-td text-slate-500 dark:text-slate-400 font-semibold">
                                        {{ sprintf('%02d/%d', $itemTagihan->periode_bulan, $itemTagihan->periode_tahun) }}
                                    </td>
                                    <td class="admin-table-td text-slate-500 dark:text-slate-400 font-semibold">{{ $itemTagihan->penyewa?->user?->nama ?? '-' }}</td>
                                    <td class="admin-table-td text-slate-800 dark:text-slate-100 font-bold">Kamar {{ $itemTagihan->penyewa?->kamar?->nomor_kamar ?? '-' }}</td>
                                    <td class="admin-table-td text-right font-extrabold text-slate-800 dark:text-slate-100">
                                        Rp {{ number_format($itemTagihan->nominal_total, 0, ',', '.') }}
                                    </td>
                                    <td class="admin-table-td text-slate-500 dark:text-slate-400 capitalize font-medium">{{ $itemTagihan->metode_pembayaran ?: '-' }}</td>
                                    <td class="admin-table-td">
                                        @php
                                            $badgeClass = match($itemTagihan->status) {
                                                'lunas' => 'admin-badge-success',
                                                'pending' => 'admin-badge-warning',
                                                'terlambat' => 'admin-badge-danger',
                                                'gagal' => 'admin-badge-danger',
                                                'kadaluarsa' => 'admin-badge-neutral',
                                                default => 'admin-badge-neutral',
                                            };
                                        @endphp
                                        <span class="admin-badge {{ $badgeClass }}">
                                            {{ strtoupper($itemTagihan->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 italic">Tidak ada data tagihan untuk filter ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($tagihan->hasPages())
                    <div class="mt-6">
                        {{ $tagihan->links('vendor.pagination.neo-brutalist') }}
                    </div>
                @endif
            </div>

            <!-- Table Pengeluaran -->
            <div class="admin-card">
                <div class="mb-6 border-b border-slate-100 dark:border-slate-700/50 pb-4">
                    <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100">Rekapitulasi Pengeluaran Operasional</h3>
                    <p class="admin-subtitle">Data transaksi pengeluaran kas kost sesuai filter periode yang aktif.</p>
                </div>

                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead class="admin-table-thead">
                            <tr>
                                <th class="admin-table-th">Tanggal</th>
                                <th class="admin-table-th">Nama Pengeluaran</th>
                                <th class="admin-table-th">Kategori</th>
                                <th class="admin-table-th text-right">Nominal</th>
                                <th class="admin-table-th">Bukti Nota</th>
                                <th class="admin-table-th">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="admin-table-tbody">
                            @forelse($pengeluaran as $itemPengeluaran)
                                <tr class="admin-table-tr">
                                    <td class="admin-table-td text-slate-500 dark:text-slate-400 font-semibold">
                                        {{ $itemPengeluaran->tanggal_pengeluaran ? $itemPengeluaran->tanggal_pengeluaran->format('d M Y') : '-' }}
                                    </td>
                                    <td class="admin-table-td font-bold text-slate-800 dark:text-slate-100">
                                        {{ $itemPengeluaran->nama_pengeluaran }}
                                    </td>
                                    <td class="admin-table-td">
                                        @php
                                            $kategoriBadge = match($itemPengeluaran->kategori) {
                                                'maintenance' => 'admin-badge-warning',
                                                'utilitas' => 'admin-badge-info',
                                                'operasional' => 'admin-badge-success',
                                                'lainnya' => 'admin-badge-neutral',
                                                default => 'admin-badge-neutral',
                                            };
                                        @endphp
                                        <span class="admin-badge {{ $kategoriBadge }}">
                                            {{ ucfirst($itemPengeluaran->kategori ?? '-') }}
                                        </span>
                                    </td>
                                    <td class="admin-table-td text-right font-extrabold text-slate-800 dark:text-slate-100">
                                        Rp {{ number_format($itemPengeluaran->nominal, 0, ',', '.') }}
                                    </td>
                                    <td class="admin-table-td">
                                        @if($itemPengeluaran->bukti_nota)
                                            <a href="{{ asset('storage/' . $itemPengeluaran->bukti_nota) }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center gap-1 font-semibold text-xs">
                                                🖼️ Lihat Nota
                                            </a>
                                        @else
                                            <span class="text-slate-400 dark:text-slate-500 italic text-xs">Tidak ada</span>
                                        @endif
                                    </td>
                                    <td class="admin-table-td text-slate-500 dark:text-slate-400 text-xs truncate max-w-[200px]" title="{{ e($itemPengeluaran->keterangan) }}">
                                        {{ $itemPengeluaran->keterangan ?: '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 italic">Tidak ada data pengeluaran untuk filter ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($pengeluaran->hasPages())
                    <div class="mt-6">
                        {{ $pengeluaran->links('vendor.pagination.neo-brutalist') }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
