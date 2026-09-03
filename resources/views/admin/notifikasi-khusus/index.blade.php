<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Pusat Pemantauan & Notifikasi Khusus') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ 
        selectedLog: null,
        modalOpen: false,
        openDetail(logData) {
            this.selectedLog = logData;
            this.modalOpen = true;
        }
    }" @keydown.escape.window="modalOpen = false">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Flash Alerts -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-300 dark:bg-emerald-950 text-black dark:text-white border-4 border-black dark:border-white rounded-none shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                    <div class="flex items-center">
                        <span class="mr-2 font-black">✅</span>
                        <span class="font-black text-sm">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Page Title Card -->
            <div class="mb-6 p-6 border-4 border-black dark:border-white bg-sky-100 dark:bg-slate-800/80 rounded-none shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-black text-black dark:text-white uppercase tracking-wider">🚨 MONITOR AKTIVITAS SISTEM</h1>
                        <p class="mt-2 text-xs font-bold text-slate-700 dark:text-slate-300 leading-relaxed max-w-2xl">
                            Pantau riwayat eksekusi pengiriman notifikasi otomatis WhatsApp dan Email. Log di bawah ini mencatat payload lengkap untuk audit keamanan dan pemecahan masalah (troubleshooting).
                        </p>
                    </div>
                    @if($notifikasi->count() > 0)
                        <form action="{{ route('admin.notifikasi-khusus.clear') }}" method="POST" data-confirm="Apakah Anda yakin ingin menghapus seluruh riwayat audit log notifikasi khusus?" data-title="Bersihkan Semua Log" data-confirm-danger="true">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white border-2 border-black dark:border-white font-black text-xs uppercase tracking-wider shadow-[3px_3px_0px_0px_#000] dark:shadow-[3px_3px_0px_0px_#fff] hover:translate-y-[-2px] active:translate-y-0 transition-all cursor-pointer">
                                🗑️ Bersihkan Semua Log
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Search, Tabs & Filter Panel -->
            <div class="mb-6 p-6 border-4 border-black dark:border-white bg-white dark:bg-slate-900 rounded-none shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                
                <!-- Filter Tabs -->
                <div class="flex flex-wrap gap-2 mb-6 border-b-4 border-black dark:border-slate-800 pb-4">
                    <a href="{{ route('admin.notifikasi-khusus.index', request()->except(['sumber', 'page'])) }}" 
                       class="px-4 py-2 font-black text-xs uppercase tracking-wider border-2 border-black transition-all {{ !request()->has('sumber') ? 'bg-yellow-400 text-black shadow-[2px_2px_0px_0px_#000]' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800' }}">
                        🌐 Semua Sumber ({{ $counts['semua'] }})
                    </a>
                    <a href="{{ route('admin.notifikasi-khusus.index', array_merge(request()->except('page'), ['sumber' => 'reservasi'])) }}" 
                       class="px-4 py-2 font-black text-xs uppercase tracking-wider border-2 border-black transition-all {{ request('sumber') === 'reservasi' ? 'bg-emerald-400 text-black shadow-[2px_2px_0px_0px_#000]' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800' }}">
                        🟢 User Reservasi ({{ $counts['reservasi'] }})
                    </a>
                    <a href="{{ route('admin.notifikasi-khusus.index', array_merge(request()->except('page'), ['sumber' => 'tagihan'])) }}" 
                       class="px-4 py-2 font-black text-xs uppercase tracking-wider border-2 border-black transition-all {{ request('sumber') === 'tagihan' ? 'bg-amber-400 text-black shadow-[2px_2px_0px_0px_#000]' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800' }}">
                        🟡 User Tagihan ({{ $counts['tagihan'] }})
                    </a>
                    <a href="{{ route('admin.notifikasi-khusus.index', array_merge(request()->except('page'), ['sumber' => 'admin'])) }}" 
                       class="px-4 py-2 font-black text-xs uppercase tracking-wider border-2 border-black transition-all {{ request('sumber') === 'admin' ? 'bg-purple-400 text-white shadow-[2px_2px_0px_0px_#000] border-black dark:border-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800' }}">
                        🟣 Admin Aksi ({{ $counts['admin'] }})
                    </a>
                </div>

                <!-- Search Form -->
                <form action="{{ route('admin.notifikasi-khusus.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @if(request()->has('sumber'))
                        <input type="hidden" name="sumber" value="{{ request('sumber') }}">
                    @endif

                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-black uppercase text-slate-500">Kata Kunci Deskripsi</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari isi aktivitas..." 
                               class="border-4 border-black p-2.5 text-xs bg-white dark:bg-slate-900 text-black dark:text-white font-bold shadow-[2px_2px_0px_0px_#000] focus:ring-0 focus:border-black">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-black uppercase text-slate-500">Mulai Tanggal</label>
                        <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"
                               class="border-4 border-black p-2.5 text-xs bg-white dark:bg-slate-900 text-black dark:text-white font-bold shadow-[2px_2px_0px_0px_#000] focus:ring-0 focus:border-black">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-black uppercase text-slate-500">Sampai Tanggal</label>
                        <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}"
                               class="border-4 border-black p-2.5 text-xs bg-white dark:bg-slate-900 text-black dark:text-white font-bold shadow-[2px_2px_0px_0px_#000] focus:ring-0 focus:border-black">
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 p-2.5 bg-black dark:bg-white text-white dark:text-black border-2 border-black dark:border-white font-black text-xs uppercase shadow-[2.5px_2.5px_0px_0px_#000] dark:shadow-[2.5px_2.5px_0px_0px_#fff] hover:translate-y-[-2px] transition-all cursor-pointer">
                            Filter Data
                        </button>
                        @if(request()->anyFilled(['search', 'tanggal_mulai', 'tanggal_selesai']))
                            <a href="{{ route('admin.notifikasi-khusus.index', request()->only('sumber')) }}" class="p-2.5 bg-slate-200 dark:bg-slate-800 text-black dark:text-white font-black text-xs uppercase border-2 border-black dark:border-slate-700 shadow-[2.5px_2.5px_0px_0px_#000] dark:shadow-[2.5px_2.5px_0px_0px_#fff] hover:translate-y-[-2px] transition-all text-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Monitoring Logs List -->
            <div class="p-6 border-4 border-black dark:border-white bg-white dark:bg-slate-900 rounded-none shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead class="admin-table-thead">
                            <tr>
                                <th scope="col" class="admin-table-th text-center w-12">No.</th>
                                <th scope="col" class="admin-table-th text-center w-24">Sumber</th>
                                <th scope="col" class="admin-table-th w-36">Aktivitas</th>
                                <th scope="col" class="admin-table-th">Deskripsi Kejadian</th>
                                <th scope="col" class="admin-table-th w-36">Pemicu</th>
                                <th scope="col" class="admin-table-th text-center w-36">Waktu Kejadian</th>
                                <th scope="col" class="admin-table-th text-center w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="admin-table-tbody">
                            @forelse($notifikasi as $log)
                                <tr class="admin-table-tr hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                    <td class="admin-table-td text-center font-bold text-slate-500 dark:text-slate-400">
                                        {{ $loop->iteration + $notifikasi->firstItem() - 1 }}
                                    </td>
                                    
                                    <!-- Sumber Badge via Accessor -->
                                    <td class="admin-table-td text-center">
                                        <span class="px-2 py-1 text-[10px] font-black uppercase tracking-wider border-2 {{ $log->sumber_badge_class }}">
                                            {{ ucfirst($log->sumber) }}
                                        </span>
                                    </td>

                                    <!-- Tipe Aktivitas via Accessor -->
                                    <td class="admin-table-td font-black text-xs text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                                        {{ $log->formatted_tipe_aktivitas }}
                                    </td>

                                    <td class="admin-table-td text-slate-700 dark:text-slate-300 font-bold text-xs leading-relaxed">
                                        {{ $log->deskripsi }}
                                    </td>

                                    <!-- User Pemicu via Accessor -->
                                    <td class="admin-table-td font-black text-xs text-slate-800 dark:text-slate-100">
                                        @if($log->user)
                                            <div>{{ $log->user->nama }}</div>
                                            <div class="text-[9px] text-slate-400 dark:text-slate-500 font-bold uppercase">{{ $log->user->role }}</div>
                                        @else
                                            <span class="text-slate-400 dark:text-slate-400 italic">System / Webhook</span>
                                        @endif
                                    </td>

                                    <td class="admin-table-td text-center text-xs text-slate-500 dark:text-slate-400 font-extrabold">
                                        {{ $log->created_at->format('d M Y H:i') }}
                                        <div class="text-[9px] text-slate-400 dark:text-slate-550 font-medium">{{ $log->created_at->diffForHumans() }}</div>
                                    </td>

                                    <!-- Action Buttons -->
                                    <td class="admin-table-td text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- Detail Button menggunakan \Illuminate\Support\Js::from -->
                                            <button @click="openDetail({{ \Illuminate\Support\Js::from($log->toDetailPayload()) }})"
                                                    class="px-2.5 py-1 bg-cyan-300 hover:bg-cyan-400 text-black border-2 border-black font-black text-[10px] uppercase shadow-[1.5px_1.5px_0px_0px_#000] hover:translate-y-[-1px] transition-all cursor-pointer"
                                                    title="Lihat Data Detail">
                                                🔍 Detail
                                            </button>

                                            <form action="{{ route('admin.notifikasi-khusus.destroy', $log->id) }}" method="POST" data-confirm="Apakah Anda yakin ingin menghapus entri log monitoring ini?" data-title="Hapus Log Aktivitas" data-confirm-danger="true">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2.5 py-1 bg-red-400 hover:bg-red-500 text-white border-2 border-black dark:border-white font-black text-[10px] uppercase shadow-[1.5px_1.5px_0px_0px_#000] dark:shadow-[1.5px_1.5px_0px_0px_#fff] hover:translate-y-[-1px] transition-all cursor-pointer">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-400 dark:text-slate-550 italic font-bold">
                                        🚫 Belum ada data audit log yang terekam untuk kriteria filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($notifikasi->hasPages())
                    <div class="mt-6">
                        {{ $notifikasi->links('vendor.pagination.neo-brutalist') }}
                    </div>
                @endif
            </div>

            <!-- MODAL DETAIL AUDIT LOG (Accessible Brutalist Modal) -->
            <div x-show="modalOpen" 
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60" 
                 x-cloak 
                 style="display: none;"
                 role="dialog"
                 aria-modal="true"
                 aria-labelledby="modal-title">
                
                <div class="bg-white dark:bg-slate-900 border-4 border-black dark:border-white w-full max-w-2xl p-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,1)]"
                     @click.away="modalOpen = false">
                    
                    <div class="flex justify-between items-start border-b-4 border-black dark:border-white pb-3 mb-4">
                        <h4 id="modal-title" class="text-base font-black uppercase tracking-wider text-black dark:text-white">
                            🔍 DATA DETAIL AUDIT LOG
                        </h4>
                        <button @click="modalOpen = false" class="text-slate-500 hover:text-black dark:hover:text-white font-black text-xl cursor-pointer p-1" aria-label="Tutup Modal">&times;</button>
                    </div>

                    <div class="space-y-3 text-xs font-bold text-slate-800 dark:text-slate-200">
                        <div class="grid grid-cols-3 gap-2 border-b border-slate-200 dark:border-slate-800 pb-2">
                            <span class="text-slate-400 dark:text-slate-500 uppercase">Sumber / Aktivitas</span>
                            <span class="col-span-2 font-black" x-text="selectedLog ? selectedLog.sumber + ' — ' + selectedLog.tipe : ''"></span>
                        </div>

                        <div class="grid grid-cols-3 gap-2 border-b border-slate-200 dark:border-slate-800 pb-2">
                            <span class="text-slate-400 dark:text-slate-500 uppercase">Waktu Kejadian</span>
                            <span class="col-span-2 font-black" x-text="selectedLog ? selectedLog.waktu : ''"></span>
                        </div>

                        <div class="grid grid-cols-3 gap-2 border-b border-slate-200 dark:border-slate-800 pb-2">
                            <span class="text-slate-400 dark:text-slate-500 uppercase">User Pemicu</span>
                            <span class="col-span-2 font-black" x-text="selectedLog ? selectedLog.user : ''"></span>
                        </div>

                        <div class="flex flex-col gap-2 bg-yellow-50 dark:bg-yellow-950/20 p-4 border-2 border-black text-black dark:text-yellow-100">
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Deskripsi Kejadian</span>
                            <p class="font-bold text-xs whitespace-pre-line leading-relaxed" x-text="selectedLog ? selectedLog.deskripsi : ''"></p>
                        </div>

                        <div class="flex flex-col gap-2">
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Payload Data JSON</span>
                            <div class="bg-slate-900 text-emerald-400 p-4 border-2 border-black overflow-x-auto rounded-none max-h-60 scrollbar-thin">
                                <pre class="font-mono text-[11px]" x-text="selectedLog && selectedLog.detail ? JSON.stringify(selectedLog.detail, null, 4) : 'Tidak ada data detail tambahan.'"></pre>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end border-t-4 border-black dark:border-white pt-4 mt-6">
                        <button @click="modalOpen = false" class="px-5 py-2 border-2 border-black font-black text-xs uppercase bg-slate-100 dark:bg-slate-800 text-black dark:text-white hover:bg-slate-200 dark:hover:bg-slate-700 shadow-[2px_2px_0px_0px_#000] cursor-pointer">
                            Tutup Detail
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
