<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Dashboard Penyewa') }}
        </h2>
    </x-slot>



    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Announcement Banners (System Notifications) -->
            @if(isset($pengumuman) && $pengumuman->isNotEmpty())
                <div class="space-y-4">
                    @foreach($pengumuman as $p)
                        <div class="bg-yellow-200 dark:bg-yellow-950/40 border-4 border-black dark:border-white p-5 text-black dark:text-yellow-200 shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] transition-all duration-200 hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#000000] dark:hover:shadow-[6px_6px_0px_0px_#ffffff]">
                            <div class="flex items-start gap-4">
                                <span class="text-2xl bg-white dark:bg-slate-900 border-2 border-black dark:border-white p-2 shrink-0 shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff]">
                                    📢
                                </span>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between gap-4">
                                        <h3 class="text-sm font-black uppercase tracking-wider text-slate-900 dark:text-slate-100">{{ $p->judul }}</h3>
                                        <span class="text-[9px] font-extrabold text-slate-500 dark:text-slate-400">{{ $p->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-300 mt-2 whitespace-pre-line leading-relaxed">
                                        {{ $p->isi }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

             <!-- Summary Cards Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">
                <!-- Card 1: Kamar Saya -->
                <div class="bg-blue-300 dark:bg-blue-950 p-5 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] transition-all duration-200 hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#000000] dark:hover:shadow-[6px_6px_0px_0px_#ffffff]">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black dark:text-slate-200">Kamar Saya</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-900 text-black dark:text-white animate-pulse">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-black text-black dark:text-white tracking-tight">Kamar {{ $nomorKamar }}</div>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t-2 border-black dark:border-white text-[10px] text-black dark:text-slate-300 font-extrabold">
                            <span>Lantai {{ $lantaiKamar }}</span>
                            <span class="px-1.5 py-0.5 border-2 border-black dark:border-white bg-white text-black text-[9px] uppercase font-black">Aktif</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Tipe Kamar -->
                <div class="bg-emerald-300 dark:bg-emerald-950 p-5 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] transition-all duration-200 hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#000000] dark:hover:shadow-[6px_6px_0px_0px_#ffffff]">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black dark:text-slate-200">Tipe Kamar</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-900 text-black dark:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-2xl font-black text-black dark:text-white tracking-tight capitalize">{{ $tipeKamar }}</div>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t-2 border-black dark:border-white text-[10px] text-black dark:text-slate-300 font-extrabold">
                            <span class="font-black">Rp {{ number_format($hargaSewa, 0, ',', '.') }}/bln</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Uang Jaminan -->
                <div class="bg-purple-300 dark:bg-purple-950 p-5 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] transition-all duration-200 hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#000000] dark:hover:shadow-[6px_6px_0px_0px_#ffffff]">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black dark:text-slate-200">Uang Jaminan</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-900 text-black dark:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-xl font-black text-black dark:text-white tracking-tight leading-none">Rp {{ number_format($depositAmount, 0, ',', '.') }}</div>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t-2 border-black dark:border-white text-[10px] text-black dark:text-slate-300 font-extrabold">
                            <span>Status: Lunas</span>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Belum Lunas -->
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
                        <div class="text-2xl font-black text-black dark:text-white tracking-tight">{{ $tagihanBelumLunas }} Tagihan</div>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t-2 border-black dark:border-white text-[9px] text-black dark:text-slate-300 font-extrabold">
                            <span>{{ $activeTagihan ? 'Tempo: ' . $activeTagihan->tanggal_jatuh_tempo->format('d/m') : 'Semua Lunas' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Card 5: Total Terbayar -->
                <div class="bg-teal-300 dark:bg-teal-950 p-5 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] transition-all duration-200 hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#000000] dark:hover:shadow-[6px_6px_0px_0px_#ffffff]">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-black uppercase tracking-wider text-black dark:text-slate-200">Total Terbayar</div>
                        <div class="p-1.5 border-2 border-black dark:border-white bg-white dark:bg-slate-900 text-black dark:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-xl font-black text-black dark:text-white tracking-tight leading-none">{{ $lunasCount }} Invoice</div>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t-2 border-black dark:border-white text-[10px] text-black dark:text-slate-300 font-extrabold">
                            <span>Rp {{ number_format($totalPaidAmount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Active Tagihan Visual Alert Banner -->
            @if($activeTagihan)
                @php $banner = $activeTagihan->banner_status; @endphp
                <div class="admin-card {{ $banner['color'] }}">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="flex items-start gap-4">
                            <span class="w-12 h-12 border-2 border-black dark:border-white bg-white dark:bg-slate-900 shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff] flex items-center justify-center text-xl shrink-0">
                                💳
                            </span>
                            <div>
                                <h3 class="text-base font-black text-slate-900 dark:text-slate-100 uppercase tracking-wide">{{ $banner['title'] }} (#{{ $activeTagihan->order_id }})</h3>
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-300 mt-1">
                                    {{ $banner['message'] }} Jatuh tempo pada: <strong>{{ $activeTagihan->tanggal_jatuh_tempo ? $activeTagihan->tanggal_jatuh_tempo->format('d M Y') : '-' }}</strong>
                                </p>
                                @if($activeTagihan->nominal_denda > 0)
                                    <p class="text-xs font-bold text-red-600 dark:text-red-400 mt-1">
                                        Rincian: Pokok Rp {{ number_format($activeTagihan->nominal_pokok, 0, ',', '.') }} + Denda Rp {{ number_format($activeTagihan->nominal_denda, 0, ',', '.') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <span class="text-lg font-black text-slate-900 dark:text-slate-100 mr-2">
                                Rp {{ number_format($activeTagihan->nominal_total, 0, ',', '.') }}
                            </span>
                            <a href="{{ route('penyewa.tagihan.show', $activeTagihan) }}" class="admin-btn-secondary">
                                Detail
                            </a>
                            <button id="btn-bayar-dashboard" class="admin-btn-primary">
                                Bayar Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Help / Complaint Card -->
            <div class="bg-cyan-300 dark:bg-cyan-800 border-4 border-black dark:border-white p-6 text-black dark:text-white shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)] flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <span class="text-3xl bg-white dark:bg-slate-900 border-2 border-black dark:border-white p-2 shrink-0">🛠️</span>
                    <div>
                        <h3 class="text-lg font-black uppercase tracking-wider">Ada Fasilitas Rusak atau Bermasalah?</h3>
                        <p class="text-xs font-bold mt-1 text-slate-800 dark:text-slate-200">Laporkan kran bocor, wifi mati, kebersihan, atau masalah keamanan langsung ke pengelola.</p>
                    </div>
                </div>
                <div class="shrink-0">
                    <a href="{{ route('penyewa.keluhan.create') }}" class="admin-btn-primary bg-yellow-400 text-black">
                        Buat Laporan Keluhan
                    </a>
                </div>
            </div>

            <!-- Chart.js Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Bar Chart: Pengeluaran 12 Bulan Terakhir -->
                <div class="lg:col-span-2 admin-card">
                    <h3 class="text-base font-black text-slate-900 dark:text-slate-100 mb-4 uppercase tracking-wide">Tren Pengeluaran Sewa (12 Bulan Terakhir)</h3>
                    <div class="relative" style="height: 250px;">
                        <canvas id="chartPemasukan"></canvas>
                    </div>
                </div>

                <!-- Doughnut Chart: Status Tagihan -->
                <div class="admin-card">
                    <h3 class="text-base font-black text-slate-900 dark:text-slate-100 mb-4 uppercase tracking-wide">Status Tagihan</h3>
                    <div class="relative" style="height: 250px;">
                        <canvas id="chartStatusKamar"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tabel Tagihan Terbaru Section -->
            <div class="admin-card">
                <div class="mb-6 border-b-4 border-black dark:border-white pb-4">
                    <h3 class="text-base font-black text-slate-900 dark:text-slate-100 uppercase tracking-wide">Tagihan Terbaru</h3>
                    <p class="admin-subtitle">Daftar transaksi tagihan Anda.</p>
                </div>
                
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead class="admin-table-thead">
                            <tr>
                                <th class="admin-table-th">Periode</th>
                                <th class="admin-table-th">Order ID</th>
                                <th class="admin-table-th">Jatuh Tempo</th>
                                <th class="admin-table-th text-right">Nominal Total</th>
                                <th class="admin-table-th">Metode</th>
                                <th class="admin-table-th">Status</th>
                                <th class="admin-table-th">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="admin-table-tbody">
                            @forelse($latestTagihan as $t)
                                <tr class="admin-table-tr">
                                    <td class="admin-table-td text-slate-500 dark:text-slate-400 font-semibold">
                                        {{ sprintf('%02d/%d', $t->periode_bulan, $t->periode_tahun) }}
                                    </td>
                                    <td class="admin-table-td text-slate-500 dark:text-slate-400 font-bold">
                                        #{{ $t->order_id }}
                                    </td>
                                    <td class="admin-table-td text-slate-500 dark:text-slate-400 font-semibold">
                                        {{ $t->tanggal_jatuh_tempo ? $t->tanggal_jatuh_tempo->format('d M Y') : '-' }}
                                    </td>
                                    <td class="admin-table-td text-right font-extrabold text-slate-800 dark:text-slate-100">
                                        Rp {{ number_format($t->nominal_total, 0, ',', '.') }}
                                    </td>
                                    <td class="admin-table-td text-slate-500 dark:text-slate-400 capitalize font-medium">
                                        {{ $t->metode_pembayaran ?: '-' }}
                                    </td>
                                    <td class="admin-table-td">
                                        <span class="admin-badge {{ $t->status_badge_class }}">
                                            {{ strtoupper($t->status) }}
                                        </span>
                                    </td>
                                    <td class="admin-table-td">
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('penyewa.tagihan.show', $t) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                Detail
                                            </a>
                                            @if($t->status === 'lunas' && $t->pembayaran_terkonfirmasi)
                                                <a href="{{ route('penyewa.nota.cetak', $t->pembayaran_terkonfirmasi->id) }}" target="_blank" class="text-xs font-bold text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">
                                                    Nota PDF
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 italic">Belum ada transaksi tagihan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($latestTagihan instanceof \Illuminate\Pagination\LengthAwarePaginator && $latestTagihan->hasPages())
                    <div class="mt-6">
                        {{ $latestTagihan->links('vendor.pagination.neo-brutalist') }}
                    </div>
                @endif
            </div>

        </div>
    </div>

@push('scripts')
    <!-- Load Chart.js with defer/async -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    <!-- Chart.js Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // [1] Bar Chart — Pengeluaran 12 Bulan Terakhir
            new Chart(document.getElementById('chartPemasukan'), {
                type: 'bar',
                data: { 
                    labels: @json($labelsBulan), 
                    datasets: [{ 
                        label: 'Pengeluaran Sewa (Rp)',
                        data: @json($dataPemasukan), 
                        backgroundColor: '#3B82F6', // Bold Blue
                        borderColor: '#000000',
                        borderWidth: 3
                    }] 
                },
                options: { 
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            ticks: {
                                font: {
                                    family: 'Space Grotesk',
                                    weight: 'bold'
                                }
                            }
                        },
                        y: {
                            ticks: {
                                font: {
                                    family: 'Space Grotesk',
                                    weight: 'bold'
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: {
                                font: {
                                    family: 'Space Grotesk',
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                }
            });

            // [2] Doughnut Chart — Status Tagihan (Lunas/Belum Lunas/Lainnya)
            new Chart(document.getElementById('chartStatusKamar'), {
                type: 'doughnut',
                data: { 
                    labels: ['Lunas','Belum Lunas','Lainnya'],
                    datasets: [{ 
                        data: @json([$tersedia,$terisi,$maintenance]),
                        backgroundColor: ['#10B981', '#F59E0B', '#6B7280'], // Emerald, Amber, Gray
                        borderColor: '#000000',
                        borderWidth: 3
                    }] 
                },
                options: { 
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                font: {
                                    family: 'Space Grotesk',
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush

    <!-- Midtrans Snap Script integration if there is an active invoice -->
    @if ($activeTagihan)
        <script src="{{ config('midtrans.snap_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
        <script>
            (function() {
                let isProcessing = false;
                const btn = document.getElementById('btn-bayar-dashboard');
                if (btn) {
                    btn.addEventListener('click', async function(e) {
                        e.preventDefault();
                        if (isProcessing) return;
                        isProcessing = true;
                        
                        this.disabled = true;
                        this.textContent = 'Memproses...';
                        
                        try {
                            const response = await fetch('{{ route("penyewa.pembayaran.token", $activeTagihan) }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                }
                            });
                            
                            if (!response.ok) {
                                let errMsg = 'Gagal menghubungi server.';
                                try {
                                    const errData = await response.json();
                                    errMsg = errData.message || errMsg;
                                } catch (e) {}
                                throw new Error(errMsg);
                            }
                            
                            const data = await response.json();
                            
                            if (!data.snap_token) {
                                throw new Error(data.message || 'Token tidak diterima');
                            }
                            
                            window.snap.pay(data.snap_token, {
                                onSuccess: function(result) {
                                    window.location.reload();
                                },
                                onPending: function(result) {
                                    window.location.reload();
                                },
                                onError: function(result) {
                                    if (typeof window.showToast === 'function') window.showToast('Pembayaran gagal: ' + (result.status_message || 'Silakan coba lagi.'), 'error');
                                    window.location.reload();
                                },
                                onClose: function() {
                                    isProcessing = false;
                                    const button = document.getElementById('btn-bayar-dashboard');
                                    if (button) {
                                        button.disabled = false;
                                        button.textContent = 'Bayar Sekarang';
                                    }
                                }
                            });
                        } catch (error) {
                            if (typeof window.showToast === 'function') window.showToast('Terjadi kesalahan: ' + error.message, 'error');
                            isProcessing = false;
                            this.disabled = false;
                            this.textContent = 'Bayar Sekarang';
                        }
                    });
                }
            })();
        </script>
    @endif
</x-app-layout>
