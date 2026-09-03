<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Manajemen Notifikasi & Pengumuman Global') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="notifikasiPageManager({
        defaultTab: '{{ request()->hasAny(['page', 'search', 'channel', 'status']) ? 'logs' : 'broadcast' }}'
    })">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Global Flash Alerts -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-300 dark:bg-emerald-950 text-black dark:text-white border-4 border-black dark:border-white rounded-none shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                    <div class="flex items-center">
                        <span class="mr-2 font-black">✅</span>
                        <span class="font-black text-sm">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-300 dark:bg-red-950 text-black dark:text-white border-4 border-black dark:border-white rounded-none shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                    <div class="flex items-center">
                        <span class="mr-2 font-black">❌</span>
                        <span class="font-black text-sm">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Navigation Tabs (Neo-Brutalism Style) -->
            <div class="flex flex-wrap gap-2 mb-6">
                <!-- Tab: Broadcast -->
                <button @click="activeTab = 'broadcast'" 
                        :class="activeTab === 'broadcast' ? 'bg-yellow-400 text-black border-4 border-black shadow-[3px_3px_0px_0px_#000]' : 'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 border-2 border-slate-300 dark:border-slate-700 hover:border-black dark:hover:border-white shadow-none'"
                        class="px-5 py-2.5 font-extrabold text-sm uppercase tracking-wider transition-all duration-150 cursor-pointer">
                    📣 Kirim Broadcast / Pengumuman
                </button>

                <!-- Tab: Pengumuman Web -->
                <button @click="activeTab = 'pengumuman'" 
                        :class="activeTab === 'pengumuman' ? 'bg-yellow-400 text-black border-4 border-black shadow-[3px_3px_0px_0px_#000]' : 'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 border-2 border-slate-300 dark:border-slate-700 hover:border-black dark:hover:border-white shadow-none'"
                        class="px-5 py-2.5 font-extrabold text-sm uppercase tracking-wider transition-all duration-150 cursor-pointer">
                    🖥️ Pengumuman Web ({{ $pengumumanList->count() }})
                </button>

                <!-- Tab: Log Riwayat -->
                <button @click="activeTab = 'logs'" 
                        :class="activeTab === 'logs' ? 'bg-yellow-400 text-black border-4 border-black shadow-[3px_3px_0px_0px_#000]' : 'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 border-2 border-slate-300 dark:border-slate-700 hover:border-black dark:hover:border-white shadow-none'"
                        class="px-5 py-2.5 font-extrabold text-sm uppercase tracking-wider transition-all duration-150 cursor-pointer">
                    📁 Log Riwayat Notifikasi
                </button>
            </div>

            <!-- Tab Content Container -->
            <div class="admin-card text-gray-900 dark:text-gray-100">

                <!-- TAB 1: FORM BROADCAST -->
                <div x-show="activeTab === 'broadcast'" class="space-y-6">
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100">Kirim Notifikasi & Pengumuman Massal</h3>
                        <p class="admin-subtitle">Kirim pesan pengumuman kustom secara langsung ke penyewa melalui saluran WhatsApp, Email, dan/atau posting langsung ke Dashboard Web Penyewa.</p>
                    </div>

                    <form action="{{ route('admin.notifikasi.broadcast') }}" method="POST" class="space-y-5">
                        @csrf

                        <!-- Field: Target Penerima -->
                        <div class="space-y-2">
                            <label for="target" class="block text-sm font-black uppercase tracking-wider text-slate-700 dark:text-slate-300">Target Penerima</label>
                            <select name="target" id="target" required class="w-full border-4 border-black p-3 bg-white dark:bg-slate-900 text-black dark:text-white font-extrabold shadow-[2px_2px_0px_0px_#000] focus:ring-0 focus:border-black">
                                <option value="all">Semua Penyewa Aktif ({{ $penyewaAktif->count() }} Penyewa)</option>
                                @foreach($penyewaAktif as $p)
                                    <option value="{{ $p->id }}">Kamar {{ $p->kamar->nomor_kamar ?? '-' }} - {{ $p->user->nama ?? 'Penyewa' }}</option>
                                @endforeach
                            </select>
                            @error('target')
                                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Field: Saluran Pengiriman -->
                        <div class="space-y-3">
                            <label class="block text-sm font-black uppercase tracking-wider text-slate-700 dark:text-slate-300">Saluran Pengiriman</label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Checkbox: WhatsApp -->
                                <label class="flex items-center gap-3 p-4 border-2 border-black bg-slate-50 dark:bg-slate-900/40 cursor-pointer shadow-[2px_2px_0px_0px_#000]">
                                    <input type="checkbox" name="send_wa" value="1" checked class="w-5 h-5 border-2 border-black text-black focus:ring-0">
                                    <div class="text-xs font-black uppercase tracking-wider">🟢 WhatsApp (Fonnte API)</div>
                                </label>

                                <!-- Checkbox: Email -->
                                <label class="flex items-center gap-3 p-4 border-2 border-black bg-slate-50 dark:bg-slate-900/40 cursor-pointer shadow-[2px_2px_0px_0px_#000]">
                                    <input type="checkbox" name="send_email" value="1" class="w-5 h-5 border-2 border-black text-black focus:ring-0">
                                    <div class="text-xs font-black uppercase tracking-wider">🔵 Email (Laravel Mail)</div>
                                </label>

                                <!-- Checkbox: Web Posting -->
                                <label class="flex items-center gap-3 p-4 border-2 border-black bg-slate-50 dark:bg-slate-900/40 cursor-pointer shadow-[2px_2px_0px_0px_#000]">
                                    <input type="checkbox" name="post_to_web" value="1" x-model="postToWeb" class="w-5 h-5 border-2 border-black text-black focus:ring-0">
                                    <div class="text-xs font-black uppercase tracking-wider">🟡 Posting Web (Dashboard)</div>
                                </label>
                            </div>
                        </div>

                        <!-- Field: Judul Pengumuman (Hanya jika Posting Web aktif) -->
                        <div class="space-y-2" x-show="postToWeb" x-cloak x-transition>
                            <label for="judul" class="block text-sm font-black uppercase tracking-wider text-slate-700 dark:text-slate-300">Judul Pengumuman Web</label>
                            <input type="text" name="judul" id="judul" placeholder="Contoh: Pengumuman Perbaikan Saluran Air Bersih" :required="postToWeb"
                                   class="w-full border-4 border-black p-3 bg-white dark:bg-slate-900 text-black dark:text-white font-extrabold shadow-[2px_2px_0px_0px_#000] focus:ring-0 focus:border-black">
                            @error('judul')
                                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Field: Isi Pesan -->
                        <div class="space-y-2">
                            <label for="pesan" class="block text-sm font-black uppercase tracking-wider text-slate-700 dark:text-slate-300">Isi Pengumuman / Pesan</label>
                            <textarea name="pesan" id="pesan" rows="6" required placeholder="Tulis isi pengumuman Anda di sini..."
                                      class="w-full border-4 border-black p-3 bg-white dark:bg-slate-900 text-black dark:text-white font-bold shadow-[2px_2px_0px_0px_#000] focus:ring-0 focus:border-black"></textarea>
                            @error('pesan')
                                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit" class="admin-btn-primary w-full md:w-auto px-8 py-3 bg-yellow-400 text-black border-4 border-black font-extrabold text-sm uppercase tracking-widest shadow-[4px_4px_0px_0px_#000] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#000] active:translate-y-0 active:shadow-[2px_2px_0px_0px_#000] transition-all cursor-pointer">
                                🚀 Kirim Broadcast Sekarang
                            </button>
                        </div>
                    </form>
                </div>

                <!-- TAB 2: PENGUMUMAN WEB -->
                <div x-show="activeTab === 'pengumuman'" class="space-y-6">
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100">Daftar Pengumuman Web Aktif</h3>
                        <p class="admin-subtitle">Kelola pengumuman yang saat ini tampil di dashboard web masing-masing portal penyewa.</p>
                    </div>

                    <!-- Table component -->
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead class="admin-table-thead">
                                <tr>
                                    <th scope="col" class="admin-table-th">Judul</th>
                                    <th scope="col" class="admin-table-th">Isi Pengumuman</th>
                                    <th scope="col" class="admin-table-th text-center">Tanggal Posting</th>
                                    <th scope="col" class="admin-table-th text-center">Status</th>
                                    <th scope="col" class="admin-table-th">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="admin-table-tbody">
                                @forelse($pengumumanList as $p)
                                    <tr class="admin-table-tr">
                                        <td class="admin-table-td font-black text-slate-800 dark:text-slate-100 max-w-[200px] truncate" title="{{ $p->judul }}">
                                            {{ $p->judul }}
                                        </td>
                                        <td class="admin-table-td text-slate-500 dark:text-slate-400 font-bold max-w-[350px] truncate" title="{{ $p->isi }}">
                                            {{ $p->isi }}
                                        </td>
                                        <td class="admin-table-td text-center text-slate-500 dark:text-slate-400 font-semibold text-xs">
                                            {{ $p->created_at?->format('d M Y H:i') ?? '-' }}
                                        </td>
                                        <td class="admin-table-td text-center">
                                            <span class="admin-badge admin-badge-success">Aktif</span>
                                        </td>
                                        <td class="admin-table-td">
                                            <form action="{{ route('admin.notifikasi.destroyPengumuman', $p->id) }}" method="POST" class="inline" data-confirm="Apakah Anda yakin ingin menghapus postingan pengumuman ini?" data-title="Hapus Pengumuman" data-confirm-danger="true">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-400 hover:bg-red-500 text-white border-2 border-black dark:border-white font-black text-xs uppercase shadow-[1.5px_1.5px_0px_0px_#000] dark:shadow-[1.5px_1.5px_0px_0px_#fff] hover:translate-y-[-1px] hover:shadow-[2px_2px_0px_0px_#000] transition-all cursor-pointer">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 italic">Belum ada pengumuman web aktif yang diposting.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 3: RIWAYAT AUDIT LOG -->
                <div x-show="activeTab === 'logs'" class="space-y-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100">Log Riwayat Notifikasi</h3>
                            <p class="admin-subtitle">Pantau seluruh notifikasi yang terkirim dari sistem secara otomatis maupun siaran manual.</p>
                        </div>
                    </div>

                    <!-- Search & Filter Card -->
                    <div class="p-4 border-2 border-black bg-slate-50 dark:bg-slate-900/60 shadow-[2px_2px_0px_0px_#000]">
                        <form action="{{ route('admin.notifikasi.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Search nama -->
                            <div class="flex flex-col gap-1">
                                <label for="search_nama" class="text-[10px] font-black uppercase text-slate-500">Nama Penyewa</label>
                                <input type="text" id="search_nama" name="search" value="{{ request('search') }}" placeholder="Cari nama..." 
                                       class="border-2 border-black p-2 text-xs bg-white dark:bg-slate-900 text-black dark:text-white font-bold focus:ring-0 focus:border-black">
                            </div>

                            <!-- Filter Channel -->
                            <div class="flex flex-col gap-1">
                                <label for="filter_channel" class="text-[10px] font-black uppercase text-slate-500">Saluran</label>
                                <select id="filter_channel" name="channel" class="border-2 border-black p-2 text-xs bg-white dark:bg-slate-900 text-black dark:text-white font-bold focus:ring-0 focus:border-black">
                                    <option value="">Semua Saluran</option>
                                    <option value="whatsapp" @selected(request('channel') === 'whatsapp')>WhatsApp</option>
                                    <option value="email" @selected(request('channel') === 'email')>Email</option>
                                    <option value="system" @selected(request('channel') === 'system')>Sistem</option>
                                </select>
                            </div>

                            <!-- Filter Status -->
                            <div class="flex flex-col gap-1">
                                <label for="filter_status" class="text-[10px] font-black uppercase text-slate-500">Status</label>
                                <select id="filter_status" name="status" class="border-2 border-black p-2 text-xs bg-white dark:bg-slate-900 text-black dark:text-white font-bold focus:ring-0 focus:border-black">
                                    <option value="">Semua Status</option>
                                    <option value="sukses" @selected(request('status') === 'sukses')>Sukses</option>
                                    <option value="gagal" @selected(request('status') === 'gagal')>Gagal</option>
                                </select>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-end gap-2">
                                <button type="submit" class="flex-1 p-2 bg-slate-950 text-white font-black text-xs uppercase border-2 border-black shadow-[1.5px_1.5px_0px_0px_#000] hover:translate-y-[-1px] transition-all cursor-pointer">
                                    Filter
                                </button>
                                @if(request()->hasAny(['search', 'channel', 'status']))
                                    <a href="{{ route('admin.notifikasi.index') }}" class="p-2 bg-slate-200 text-black font-black text-xs uppercase border-2 border-black shadow-[1.5px_1.5px_0px_0px_#000] hover:translate-y-[-1px] transition-all text-center">
                                        Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Log Table -->
                    <div class="admin-table-container mt-4">
                        <table class="admin-table">
                            <thead class="admin-table-thead">
                                <tr>
                                    <th scope="col" class="admin-table-th text-center w-12">No.</th>
                                    <th scope="col" class="admin-table-th">Penyewa / Kamar</th>
                                    <th scope="col" class="admin-table-th text-center">Saluran</th>
                                    <th scope="col" class="admin-table-th">Jenis Event</th>
                                    <th scope="col" class="admin-table-th text-center">Status</th>
                                    <th scope="col" class="admin-table-th">Cuplikan Pesan</th>
                                    <th scope="col" class="admin-table-th text-center">Waktu Kirim</th>
                                    <th scope="col" class="admin-table-th">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="admin-table-tbody">
                                @forelse($logs as $log)
                                    <tr class="admin-table-tr">
                                        <!-- No. -->
                                        <td class="admin-table-td text-center font-bold text-slate-500 dark:text-slate-400">
                                            {{ $loop->iteration + $logs->firstItem() - 1 }}
                                        </td>
                                        <!-- Penerima -->
                                        <td class="admin-table-td font-bold text-slate-800 dark:text-slate-100">
                                            <div>{{ $log->penyewa->user->nama ?? '-' }}</div>
                                            <div class="text-[10px] text-slate-400 dark:text-slate-500 font-extrabold uppercase">Kamar {{ $log->penyewa->kamar->nomor_kamar ?? '-' }}</div>
                                        </td>
                                        <!-- Saluran -->
                                        <td class="admin-table-td text-center">
                                            @if($log->channel === 'whatsapp')
                                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 border-2 border-emerald-800 text-[10px] font-black uppercase">WA</span>
                                            @elseif($log->channel === 'email')
                                                <span class="px-2 py-0.5 bg-sky-100 text-sky-800 border-2 border-sky-800 text-[10px] font-black uppercase">Email</span>
                                            @else
                                                <span class="px-2 py-0.5 bg-slate-100 text-slate-800 border-2 border-slate-800 text-[10px] font-black uppercase">System</span>
                                            @endif
                                        </td>
                                        <!-- Event -->
                                        <td class="admin-table-td font-bold text-xs text-slate-600 dark:text-slate-400 uppercase">
                                            {{ str_replace('_', ' ', $log->event) }}
                                        </td>
                                        <!-- Status -->
                                        <td class="admin-table-td text-center">
                                            @if($log->status === 'sukses')
                                                <span class="admin-badge admin-badge-success">Sukses</span>
                                            @else
                                                <span class="admin-badge admin-badge-danger">Gagal</span>
                                            @endif
                                        </td>
                                        <!-- Pesan (Truncated) -->
                                        <td class="admin-table-td text-slate-500 dark:text-slate-400 font-semibold max-w-[200px] truncate" title="{{ $log->pesan }}">
                                            {{ $log->pesan ?? 'Notifikasi otomatis (Isi pesan dinamis)' }}
                                        </td>
                                        <!-- Waktu Kirim -->
                                        <td class="admin-table-td text-center text-xs text-slate-400 dark:text-slate-500 font-extrabold">
                                            {{ $log->created_at?->format('d M Y H:i') ?? '-' }}
                                        </td>
                                        <!-- Aksi -->
                                        <td class="admin-table-td">
                                            <div class="flex items-center gap-2">
                                                <!-- Button Detail (triggers Alpine Modal via safe method) -->
                                                <button type="button" 
                                                        @click="showDetail(@js([
                                                            'nama' => $log->penyewa->user->nama ?? 'Penyewa',
                                                            'kamar' => $log->penyewa->kamar->nomor_kamar ?? '-',
                                                            'channel' => $log->channel,
                                                            'event' => str_replace('_', ' ', $log->event),
                                                            'status' => $log->status,
                                                            'pesan' => $log->pesan ?? 'Notifikasi otomatis sistem (Isi pesan dinamis)',
                                                            'error_msg' => $log->error_msg,
                                                            'waktu' => $log->created_at?->format('d M Y H:i') ?? '-'
                                                        ]))"
                                                        class="inline-flex items-center px-2 py-1 bg-cyan-300 hover:bg-cyan-400 text-black border-2 border-black dark:border-white font-black text-xs uppercase shadow-[1.5px_1.5px_0px_0px_#000] hover:translate-y-[-1px] transition-all cursor-pointer">
                                                    Detail
                                                </button>
                                                <!-- Button Kirim Ulang (Retry) if failed -->
                                                @if($log->status === 'gagal')
                                                    <form action="{{ route('admin.notifikasi.retry', $log->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center px-2 py-1 bg-yellow-300 hover:bg-yellow-400 text-black border-2 border-black dark:border-white font-black text-xs uppercase shadow-[1.5px_1.5px_0px_0px_#000] hover:translate-y-[-1px] transition-all cursor-pointer">
                                                            Retry
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 italic">Belum ada riwayat pengiriman notifikasi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Links -->
                    <div class="mt-4">
                        {{ $logs->links('vendor.pagination.neo-brutalist') }}
                    </div>
                </div>

            </div>

            <!-- MODAL DETAIL AUDIT LOG (Brutalist Style) -->
            <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60" x-cloak style="display: none;">
                <!-- Modal Body -->
                <div class="bg-white dark:bg-slate-900 border-4 border-black dark:border-white w-full max-w-xl p-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,1)]"
                     @click.away="modalOpen = false">
                    
                    <div class="flex justify-between items-start border-b-4 border-black dark:border-white pb-3 mb-4">
                        <h4 class="text-base font-black uppercase tracking-wider text-slate-800 dark:text-slate-100">
                            🔍 Detail Riwayat Audit Notifikasi
                        </h4>
                        <button @click="modalOpen = false" class="text-slate-400 hover:text-black dark:hover:text-white font-black text-xl cursor-pointer p-1">&times;</button>
                    </div>

                    <div class="space-y-4 text-xs font-bold text-slate-800 dark:text-slate-200">
                        <div class="grid grid-cols-3 gap-2 border-b border-slate-200 dark:border-slate-800 pb-2">
                            <span class="text-slate-400 uppercase">Penerima</span>
                            <span class="col-span-2 font-black" x-text="selectedLog ? selectedLog.nama + ' (Kamar ' + selectedLog.kamar + ')' : ''"></span>
                        </div>

                        <div class="grid grid-cols-3 gap-2 border-b border-slate-200 dark:border-slate-800 pb-2">
                            <span class="text-slate-400 uppercase">Saluran & Event</span>
                            <span class="col-span-2 capitalize font-black" x-text="selectedLog ? selectedLog.channel + ' — ' + selectedLog.event : ''"></span>
                        </div>

                        <div class="grid grid-cols-3 gap-2 border-b border-slate-200 dark:border-slate-800 pb-2">
                            <span class="text-slate-400 uppercase">Status & Waktu</span>
                            <span class="col-span-2 flex items-center gap-2">
                                <span :class="selectedLog && selectedLog.status === 'sukses' ? 'bg-emerald-300 text-black border border-black' : 'bg-red-400 text-white border border-black'"
                                      class="px-2 py-0.5 text-[10px] font-black uppercase" x-text="selectedLog ? selectedLog.status : ''"></span>
                                <span class="text-slate-500 font-extrabold" x-text="selectedLog ? selectedLog.waktu : ''"></span>
                            </span>
                        </div>

                        <!-- Full Message Text -->
                        <div class="flex flex-col gap-2 bg-slate-50 dark:bg-slate-950 p-4 border-2 border-black">
                            <span class="text-slate-400 uppercase text-[10px]">Isi Pesan Lengkap</span>
                            <p class="font-black text-xs whitespace-pre-line text-slate-800 dark:text-slate-100 leading-relaxed" x-text="selectedLog ? selectedLog.pesan : ''"></p>
                        </div>

                        <!-- Error Message (If Failed) -->
                        <template x-if="selectedLog && selectedLog.error_msg">
                            <div class="flex flex-col gap-2 bg-red-50 dark:bg-red-950/20 p-4 border-2 border-red-500 text-red-600 dark:text-red-400">
                                <span class="uppercase text-[10px] font-black">Detail Kegagalan Error</span>
                                <p class="font-extrabold text-xs" x-text="selectedLog.error_msg"></p>
                            </div>
                        </template>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex justify-end gap-2 border-t-4 border-black dark:border-white pt-4 mt-6">
                        <button @click="modalOpen = false" class="px-4 py-2 border-2 border-black font-black text-xs uppercase hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Alpine State Component Manager Script -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('notifikasiPageManager', (config) => ({
                activeTab: config.defaultTab || 'broadcast',
                postToWeb: false,
                selectedLog: null,
                modalOpen: false,

                showDetail(logData) {
                    this.selectedLog = logData;
                    this.modalOpen = true;
                }
            }));
        });
    </script>
</x-app-layout>

