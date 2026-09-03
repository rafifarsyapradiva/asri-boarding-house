<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Summary Cards Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Card 1: Total Kamar -->
                <div data-testid="stat-total-kamar" class="bg-cyan-300 dark:bg-cyan-950 p-5 rounded-none border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] transition-all duration-150 hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)]">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black dark:text-cyan-200">Total Kamar</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-800 text-black dark:text-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] rounded-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-3xl font-black text-black dark:text-white">{{ $totalKamar }} Unit</div>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t-2 border-black dark:border-white text-[10px] text-black dark:text-cyan-200 font-extrabold">
                            <span>{{ $kamarTerisi }} Terisi</span>
                            <span class="px-1.5 py-0.5 border border-black dark:border-white bg-white dark:bg-slate-800 text-black dark:text-white font-black">{{ $occupancyRate }}% Hunian</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Kamar Tersedia -->
                <div data-testid="stat-kamar-tersedia" class="bg-emerald-300 dark:bg-emerald-950 p-5 rounded-none border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] transition-all duration-150 hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)]">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black dark:text-emerald-200">Kamar Tersedia</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-800 text-black dark:text-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] rounded-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-3xl font-black text-black dark:text-white">{{ $kamarTersedia }} Unit</div>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t-2 border-black dark:border-white text-[10px] text-black dark:text-emerald-200 font-extrabold">
                            <span class="text-black dark:text-emerald-100 font-black">Siap disewakan</span>
                            <span>{{ $kamarMaintenance }} Maintenance</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Total Pemasukan -->
                <div data-testid="stat-pemasukan" class="bg-yellow-300 dark:bg-yellow-950 p-5 rounded-none border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] transition-all duration-150 hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)]">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black dark:text-yellow-200">Total Pemasukan</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-800 text-black dark:text-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] rounded-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-xl font-black text-black dark:text-white leading-none">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t-2 border-black dark:border-white text-[10px] text-black dark:text-yellow-200 font-extrabold">
                            <span>Bulan berjalan</span>
                            <span class="text-black dark:text-yellow-100 font-black">Pemasukan</span>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Total Pengeluaran -->
                <div data-testid="stat-pengeluaran" class="bg-rose-300 dark:bg-rose-950 p-5 rounded-none border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] transition-all duration-150 hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)]">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black dark:text-rose-200">Total Pengeluaran</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-800 text-black dark:text-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] rounded-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-xl font-black text-black dark:text-white leading-none">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t-2 border-black dark:border-white text-[10px] text-black dark:text-rose-200 font-extrabold">
                            <span>Bulan berjalan</span>
                            <span class="text-black dark:text-rose-100 font-black">Pengeluaran</span>
                        </div>
                    </div>
                </div>

                <!-- Card 5: Keuntungan Bersih -->
                <div data-testid="stat-keuntungan-bersih" class="bg-violet-300 dark:bg-violet-950 p-5 rounded-none border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] transition-all duration-150 hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)]">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black dark:text-violet-200">Keuntungan Bersih</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-800 text-black dark:text-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] rounded-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-xl font-black text-black dark:text-white leading-none">Rp {{ number_format($keuntunganBersih, 0, ',', '.') }}</div>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t-2 border-black dark:border-white text-[10px] text-black dark:text-violet-200 font-extrabold">
                            <span>Bulan berjalan</span>
                            <span class="text-black dark:text-violet-100 font-black">Laba Bersih</span>
                        </div>
                    </div>
                </div>

                <!-- Card 6: Tagihan Belum Lunas -->
                <div data-testid="stat-tagihan-belum-lunas" class="bg-orange-300 dark:bg-orange-950 p-5 rounded-none border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] transition-all duration-150 hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)]">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black dark:text-orange-200">Belum Lunas</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-800 text-black dark:text-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] rounded-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-black text-black dark:text-white leading-none">{{ $tagihanBelumLunas }} Tagihan</div>
                        <div class="mt-3 pt-2 border-t-2 border-black dark:border-white text-[10px] text-black dark:text-orange-100 grid grid-cols-2 gap-1.5 font-extrabold">
                            <div class="flex items-center justify-between bg-white/40 dark:bg-black/20 px-1.5 py-0.5 border border-black dark:border-white">
                                <span>Pending:</span>
                                <span class="text-black dark:text-white font-black">{{ $breakdownTerlambat['pending'] ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between bg-white/40 dark:bg-black/20 px-1.5 py-0.5 border border-black dark:border-white">
                                <span>L1 (1B):</span>
                                <span class="text-black dark:text-white font-black">{{ $breakdownTerlambat['1_bulan'] ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between bg-white/40 dark:bg-black/20 px-1.5 py-0.5 border border-black dark:border-white">
                                <span>L2 (2B):</span>
                                <span class="text-black dark:text-white font-black">{{ $breakdownTerlambat['2_bulan'] ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between bg-white/40 dark:bg-black/20 px-1.5 py-0.5 border border-black dark:border-white">
                                <span>L3+ (3B+):</span>
                                <span class="text-red-700 dark:text-red-400 font-black">{{ $breakdownTerlambat['3_bulan_plus'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 7: Reservasi Masuk -->
                <div data-testid="stat-reservasi" class="bg-teal-300 dark:bg-teal-950 p-5 rounded-none border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] transition-all duration-150 hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)]">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black dark:text-teal-200">Reservasi Masuk</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-800 text-black dark:text-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] rounded-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-black text-black dark:text-white leading-none">{{ $reservasiPendingKonfirmasi }} Pending</div>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t-2 border-black dark:border-white text-[10px] text-black dark:text-teal-200 font-extrabold">
                            <span class="text-black dark:text-teal-100 font-black">Bulan berjalan</span>
                            <span>{{ $reservasiDikonfirmasiBulanIni }} Dikonfirmasi</span>
                        </div>
                    </div>
                </div>

                <!-- Card 8: Klik WhatsApp (Conversion) -->
                <div data-testid="stat-wa-clicks" class="bg-indigo-300 dark:bg-indigo-950 p-5 rounded-none border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] transition-all duration-150 hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)] relative group">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black dark:text-indigo-200">Klik WhatsApp</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-800 text-black dark:text-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] rounded-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-3xl font-black text-black dark:text-white">{{ $totalWaClicksBulanIni }} Klik</div>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t-2 border-black dark:border-white text-[10px] text-black dark:text-indigo-200 font-extrabold">
                            <span>Bulan berjalan</span>
                            <span class="underline cursor-help" title="Detail sumber klik">Hover detail &rarr;</span>
                        </div>
                    </div>

                    <!-- Popover Tooltip for Breakdown -->
                    <div class="absolute bottom-full left-0 mb-2 w-56 p-3 bg-white dark:bg-slate-900 border-4 border-black dark:border-white text-black dark:text-white text-[10px] shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] hidden group-hover:block z-50">
                        <div class="font-black uppercase border-b-2 border-black dark:border-white pb-1.5 mb-1.5 tracking-wider">Breakdown Sumber:</div>
                        @forelse($waClicksBreakdown as $breakdownItem)
                            <div class="flex justify-between font-bold py-0.5">
                                <span class="capitalize">{{ str_replace('_', ' ', $breakdownItem->source) }}:</span>
                                <span class="font-black">{{ $breakdownItem->count }}</span>
                            </div>
                        @empty
                            <div class="text-slate-400 dark:text-slate-500 font-semibold italic text-center py-1">Belum ada klik terdeteksi.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Chart.js Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Bar Chart: Pemasukan 12 Bulan Terakhir -->
                <div class="lg:col-span-2 admin-card">
                    <h3 class="text-base font-extrabold text-slate-800 dark:text-slate-100 mb-4">Tren Pemasukan (12 Bulan Terakhir)</h3>
                    <div class="relative" style="height: 250px;">
                        <canvas id="chartPemasukan" data-testid="chart-pemasukan"></canvas>
                    </div>
                </div>

                <!-- Doughnut Chart: Status Kamar -->
                <div class="admin-card">
                    <h3 class="text-base font-extrabold text-slate-800 dark:text-slate-100 mb-4">Status Kamar</h3>
                    <div class="relative" style="height: 250px;">
                        <canvas id="chartStatusKamar" data-testid="chart-status-kamar"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tabel Tagihan Terbaru Section -->
            <div class="admin-card">
                <div class="mb-6 border-b border-slate-100 dark:border-slate-700/50 pb-4">
                    <h3 class="text-base font-extrabold text-slate-800 dark:text-slate-100">Tagihan Terbaru</h3>
                    <p class="admin-subtitle">Daftar transaksi tagihan terbaru sistem.</p>
                </div>
                
                <div class="admin-table-container">
                    <table class="admin-table" data-testid="table-latest-tagihan">
                        <thead class="admin-table-thead">
                            <tr>
                                <th class="admin-table-th">Periode</th>
                                <th class="admin-table-th">Penyewa</th>
                                <th class="admin-table-th">Nomor Kamar</th>
                                <th class="admin-table-th text-right">Nominal Total</th>
                                <th class="admin-table-th">Metode</th>
                                <th class="admin-table-th">Status</th>
                            </tr>
                        </thead>
                        <tbody class="admin-table-tbody">
                            @forelse($latestTagihan as $tagihan)
                                <tr class="admin-table-tr" data-testid="row-tagihan-{{ $tagihan->id }}">
                                    <td class="admin-table-td text-slate-500 dark:text-slate-400 font-semibold">
                                        {{ sprintf('%02d/%d', $tagihan->periode_bulan, $tagihan->periode_tahun) }}
                                    </td>
                                    <td class="admin-table-td text-slate-500 dark:text-slate-400 font-semibold">
                                        {{ $tagihan->penyewa?->user?->nama ?? '-' }}
                                    </td>
                                    <td class="admin-table-td text-slate-800 dark:text-slate-100 font-bold">
                                        Kamar {{ $tagihan->penyewa?->kamar?->nomor_kamar ?? '-' }}
                                    </td>
                                    <td class="admin-table-td text-right font-extrabold text-slate-800 dark:text-slate-100">
                                        Rp {{ number_format($tagihan->nominal_total, 0, ',', '.') }}
                                    </td>
                                    <td class="admin-table-td text-slate-500 dark:text-slate-400 capitalize font-medium">
                                        {{ $tagihan->metode_pembayaran ?: '-' }}
                                    </td>
                                    <td class="admin-table-td">
                                        <span class="admin-badge {{ $tagihan->status_badge_class }}">
                                            {{ strtoupper($tagihan->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 italic">Belum ada transaksi tagihan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $latestTagihan->links('vendor.pagination.neo-brutalist') }}
                </div>
            </div>

        </div>
    </div>

    <!-- Chart.js Initialization Script via Asset Stack -->
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartData = {
                labelsBulan: @json($labelsBulan),
                dataPemasukan: @json($dataPemasukan),
                dataPengeluaran: @json($dataPengeluaran),
                statusKamar: @json([$kamarTersedia, $kamarTerisi, $kamarMaintenance])
            };

            const isDark = document.documentElement.classList.contains('dark');
            const chartBorderColor = isDark ? '#ffffff' : '#000000';
            const chartGridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.15)';
            const chartTextColor = isDark ? '#ffffff' : '#000000';
            const fontSettings = {
                family: "'Space Grotesk', sans-serif",
                size: 11,
                weight: 'bold'
            };

            // [1] Grouped Bar Chart — Pemasukan & Pengeluaran 12 Bulan Terakhir
            const ctxPemasukan = document.getElementById('chartPemasukan');
            if (ctxPemasukan) {
                new Chart(ctxPemasukan, {
                    type: 'bar',
                    data: {
                        labels: chartData.labelsBulan,
                        datasets: [
                            {
                                label: 'Pemasukan (Rp)',
                                data: chartData.dataPemasukan,
                                backgroundColor: '#60A5FA',
                                borderColor: chartBorderColor,
                                borderWidth: 3
                            },
                            {
                                label: 'Pengeluaran (Rp)',
                                data: chartData.dataPengeluaran,
                                backgroundColor: '#F87171',
                                borderColor: chartBorderColor,
                                borderWidth: 3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                grid: {
                                    color: chartGridColor,
                                    tickColor: chartBorderColor,
                                    width: 2
                                },
                                ticks: {
                                    color: chartTextColor,
                                    font: fontSettings
                                },
                                border: {
                                    color: chartBorderColor,
                                    width: 3
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: chartGridColor,
                                    tickColor: chartBorderColor,
                                    width: 2
                                },
                                ticks: {
                                    color: chartTextColor,
                                    font: fontSettings,
                                    callback: function(value) {
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    }
                                },
                                border: {
                                    color: chartBorderColor,
                                    width: 3
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                labels: {
                                    color: chartTextColor,
                                    font: fontSettings
                                }
                            },
                            tooltip: {
                                backgroundColor: isDark ? '#1e293b' : '#ffffff',
                                titleColor: chartTextColor,
                                bodyColor: chartTextColor,
                                borderColor: chartBorderColor,
                                borderWidth: 3,
                                borderRadius: 0,
                                titleFont: {
                                    family: "'Space Grotesk', sans-serif",
                                    size: 12,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    family: "'Plus Jakarta Sans', sans-serif",
                                    size: 11,
                                    weight: 'bold'
                                },
                                padding: 10,
                                displayColors: true,
                                boxPadding: 4,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // [2] Doughnut Chart — Status Kamar (tersedia/terisi/maintenance)
            const ctxStatusKamar = document.getElementById('chartStatusKamar');
            if (ctxStatusKamar) {
                new Chart(ctxStatusKamar, {
                    type: 'doughnut',
                    data: { 
                        labels: ['Tersedia', 'Terisi', 'Maintenance'],
                        datasets: [{ 
                            data: chartData.statusKamar,
                            backgroundColor: ['#34D399', '#60A5FA', '#FBBF24'],
                            borderColor: chartBorderColor,
                            borderWidth: 3
                        }] 
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: chartTextColor,
                                    font: fontSettings
                                }
                            },
                            tooltip: {
                                backgroundColor: isDark ? '#1e293b' : '#ffffff',
                                titleColor: chartTextColor,
                                bodyColor: chartTextColor,
                                borderColor: chartBorderColor,
                                borderWidth: 3,
                                borderRadius: 0,
                                titleFont: {
                                    family: "'Space Grotesk', sans-serif",
                                    size: 12,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    family: "'Plus Jakarta Sans', sans-serif",
                                    size: 11,
                                    weight: 'bold'
                                },
                                padding: 10,
                                displayColors: true,
                                boxPadding: 4
                            }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>

