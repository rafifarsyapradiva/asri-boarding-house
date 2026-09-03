<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Notifikasi Saya') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ 
        selectedLog: null,
        modalOpen: false,
        openDetail(log) {
            this.selectedLog = log;
            this.modalOpen = true;
        },
        closeDetail() {
            this.modalOpen = false;
        }
    }" @keydown.escape.window="closeDetail()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card text-gray-900 dark:text-gray-100">
                <div class="mb-6 border-b-4 border-black dark:border-white pb-4">
                    <h3 class="text-lg font-black text-slate-900 dark:text-slate-100 uppercase tracking-wide">Riwayat Notifikasi Saya</h3>
                    <p class="admin-subtitle">Daftar semua pemberitahuan resmi yang dikirimkan oleh pengelola kost ke akun Anda.</p>
                </div>

                <!-- Table Container -->
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead class="admin-table-thead">
                            <tr>
                                <th scope="col" class="admin-table-th text-center">Saluran</th>
                                <th scope="col" class="admin-table-th">Jenis Pemberitahuan</th>
                                <th scope="col" class="admin-table-th text-center">Status Kirim</th>
                                <th scope="col" class="admin-table-th">Cuplikan Pesan</th>
                                <th scope="col" class="admin-table-th text-center">Tanggal Diterima</th>
                                <th scope="col" class="admin-table-th">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="admin-table-tbody">
                            @forelse($logs as $log)
                                <tr class="admin-table-tr">
                                    <!-- Saluran -->
                                    <td class="admin-table-td text-center">
                                        @if($log->channel === 'whatsapp')
                                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 border-2 border-emerald-800 text-[10px] font-black uppercase">WhatsApp</span>
                                        @elseif($log->channel === 'email')
                                            <span class="px-2 py-0.5 bg-sky-100 text-sky-800 border-2 border-sky-800 text-[10px] font-black uppercase">Email</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-slate-100 text-slate-800 border-2 border-slate-800 text-[10px] font-black uppercase">Sistem</span>
                                        @endif
                                    </td>
                                    <!-- Event -->
                                    <td class="admin-table-td font-bold text-xs text-slate-600 dark:text-slate-300 uppercase">
                                        {{ $log->formatted_event }}
                                    </td>
                                    <!-- Status -->
                                    <td class="admin-table-td text-center">
                                        @if($log->status === 'sukses')
                                            <span class="admin-badge admin-badge-success">Diterima</span>
                                        @else
                                            <span class="admin-badge admin-badge-danger">Gagal</span>
                                        @endif
                                    </td>
                                    <!-- Pesan (Truncated) -->
                                    <td class="admin-table-td text-slate-500 dark:text-slate-400 font-semibold max-w-[250px] truncate" title="{{ $log->pesan_display }}">
                                        {{ $log->pesan_display }}
                                    </td>
                                    <!-- Tanggal Kirim -->
                                    <td class="admin-table-td text-center text-xs text-slate-400 dark:text-slate-500 font-extrabold">
                                        {{ $log->formatted_created_at }}
                                    </td>
                                    <!-- Aksi -->
                                    <td class="admin-table-td">
                                        <!-- Detail Button -->
                                        <button @click="openDetail(@js($log->toModalPayload()))"
                                                type="button"
                                                class="inline-flex items-center px-2 py-1 bg-cyan-300 hover:bg-cyan-400 text-black border-2 border-black dark:border-white font-black text-xs uppercase shadow-[1.5px_1.5px_0px_0px_#000] hover:translate-y-[-1px] transition-all cursor-pointer">
                                            Baca Pesan
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 italic">Belum ada riwayat notifikasi yang dikirimkan untuk Anda.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                @if($logs instanceof \Illuminate\Pagination\LengthAwarePaginator && $logs->hasPages())
                    <div class="mt-6">
                        {{ $logs->links('vendor.pagination.neo-brutalist') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- MODAL DETAIL NOTIFIKASI (Brutalist Style) -->
        <div x-show="modalOpen"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             role="dialog"
             aria-modal="true"
             aria-labelledby="modal-notif-title"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60">
            
            <!-- Modal Body -->
            <div class="bg-white dark:bg-slate-900 border-4 border-black dark:border-white w-full max-w-xl p-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,1)]"
                 @click.away="closeDetail()">
                
                <div class="flex justify-between items-start border-b-4 border-black dark:border-white pb-3 mb-4">
                    <h4 id="modal-notif-title" class="text-base font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">
                        ✉️ Detail Pemberitahuan Kost
                    </h4>
                    <button @click="closeDetail()" 
                            type="button" 
                            aria-label="Tutup modal"
                            class="text-slate-400 hover:text-black dark:hover:text-white font-black text-xl cursor-pointer p-1">&times;</button>
                </div>

                <div class="space-y-4 text-xs font-bold text-slate-800 dark:text-slate-200">
                    <div class="grid grid-cols-3 gap-2 border-b border-slate-200 dark:border-slate-800 pb-2">
                        <span class="text-slate-400 uppercase font-black">Saluran & Event</span>
                        <span class="col-span-2 capitalize font-black" x-text="selectedLog ? selectedLog.channel + ' — ' + selectedLog.event : ''"></span>
                    </div>

                    <div class="grid grid-cols-3 gap-2 border-b border-slate-200 dark:border-slate-800 pb-2">
                        <span class="text-slate-400 uppercase font-black">Waktu & Status</span>
                        <span class="col-span-2 flex items-center gap-2">
                            <span :class="selectedLog && selectedLog.status === 'sukses' ? 'bg-emerald-300 text-black border border-black' : 'bg-red-400 text-white border border-black'"
                                  class="px-2 py-0.5 text-[10px] font-black uppercase" x-text="selectedLog && selectedLog.status === 'sukses' ? 'Diterima' : 'Gagal'"></span>
                            <span class="text-slate-500 font-extrabold" x-text="selectedLog ? selectedLog.waktu : ''"></span>
                        </span>
                    </div>

                    <!-- Full Message Text -->
                    <div class="flex flex-col gap-2 bg-slate-50 dark:bg-slate-950 p-4 border-2 border-black dark:border-white">
                        <span class="text-slate-400 uppercase text-[9px] font-black">Isi Pesan</span>
                        <p class="font-bold text-xs whitespace-pre-line text-slate-800 dark:text-slate-300 leading-relaxed" x-text="selectedLog ? selectedLog.pesan : ''"></p>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="flex justify-end border-t-4 border-black dark:border-white pt-4 mt-6">
                    <button @click="closeDetail()" 
                            type="button"
                            class="px-4 py-2 border-2 border-black dark:border-white font-black text-xs uppercase hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
