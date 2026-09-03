<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Tagihan Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Summary Cards Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Card 1: Belum Lunas -->
                <div class="bg-amber-300 dark:bg-amber-950 p-5 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] transition-all duration-200 hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#000000] dark:hover:shadow-[6px_6px_0px_0px_#ffffff]">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black dark:text-slate-200">Belum Lunas</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-900 text-black dark:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-black text-black dark:text-white tracking-tight">{{ $unpaidCount }} Tagihan</div>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t-2 border-black dark:border-white text-[10px] text-black dark:text-slate-300 font-extrabold">
                            <span>Total Tunggakan:</span>
                            <span>Rp {{ number_format($unpaidNominal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Tagihan Lunas -->
                <div class="bg-emerald-300 dark:bg-emerald-950 p-5 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] transition-all duration-200 hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#000000] dark:hover:shadow-[6px_6px_0px_0px_#ffffff]">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black dark:text-slate-200">Tagihan Lunas</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-900 text-black dark:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-black text-black dark:text-white tracking-tight">{{ $paidCount }} Tagihan</div>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t-2 border-black dark:border-white text-[10px] text-black dark:text-slate-300 font-extrabold">
                            <span>Status Pembayaran:</span>
                            <span class="px-1.5 py-0.5 border border-black dark:border-white bg-white text-black text-[9px] uppercase font-black">Lancar</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Total Pengeluaran -->
                <div class="bg-teal-300 dark:bg-teal-950 p-5 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] transition-all duration-200 hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#000000] dark:hover:shadow-[6px_6px_0px_0px_#ffffff]">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black dark:text-slate-200">Total Pengeluaran</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-900 text-black dark:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-black text-black dark:text-white tracking-tight">Rp {{ number_format($paidNominal, 0, ',', '.') }}</div>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t-2 border-black dark:border-white text-[10px] text-black dark:text-slate-300 font-extrabold">
                            <span>Akumulasi Terbayar</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">

                    <!-- Header Section -->
                    <div class="mb-8 border-b-4 border-black dark:border-white pb-4">
                        <h3 class="text-lg font-black text-slate-800 dark:text-slate-100 uppercase tracking-wide">Riwayat Tagihan Sewa</h3>
                        <p class="admin-subtitle">Daftar semua tagihan sewa kamar Anda di Asri Boarding House.</p>
                    </div>

                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead class="admin-table-thead">
                                <tr>
                                    <th class="admin-table-th">Order ID</th>
                                    <th class="admin-table-th">Periode</th>
                                    <th class="admin-table-th">Jatuh Tempo</th>
                                    <th class="admin-table-th text-right">Total Tagihan</th>
                                    <th class="admin-table-th">Status</th>
                                    <th class="admin-table-th">Metode</th>
                                    <th class="admin-table-th">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="admin-table-tbody">
                                @forelse ($tagihan as $item)
                                    <tr class="admin-table-tr">
                                        <td class="admin-table-td font-extrabold text-slate-800 dark:text-slate-100">
                                            #{{ $item->order_id }}
                                        </td>
                                        <td class="admin-table-td text-slate-500 dark:text-slate-400 font-semibold">
                                            {{ $item->periode_formatted }}
                                        </td>
                                        <td class="admin-table-td text-slate-500 dark:text-slate-400 font-semibold">
                                            {{ $item->tanggal_jatuh_tempo?->format('d M Y') ?? '-' }}
                                        </td>
                                        <td class="admin-table-td text-right font-extrabold text-slate-800 dark:text-slate-100">
                                            Rp {{ number_format($item->nominal_total, 0, ',', '.') }}
                                        </td>
                                        <td class="admin-table-td">
                                            <span class="admin-badge {{ $item->status_badge_class }}">
                                                {{ strtoupper($item->computed_status) }}
                                            </span>
                                        </td>
                                        <td class="admin-table-td text-slate-500 dark:text-slate-400 font-medium capitalize">
                                            {{ $item->metode_pembayaran ?: '-' }}
                                        </td>
                                        <td class="admin-table-td">
                                            <a href="{{ route('penyewa.tagihan.show', $item) }}" class="text-xs font-extrabold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition duration-150 underline decoration-2">
                                                Detail / Bayar
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 italic font-bold">
                                            Tidak ada data tagihan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(method_exists($tagihan, 'hasPages') && $tagihan->hasPages())
                        <div class="mt-6">
                            {{ $tagihan->links('vendor.pagination.neo-brutalist') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
