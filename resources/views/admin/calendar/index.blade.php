<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-black leading-tight uppercase tracking-wider">
            {{ __('Kalender Kontrol Visual') }}
        </h2>
    </x-slot>

    <div class="py-8 text-black dark:text-white" x-data="calendarController('{{ route('admin.calendar.events') }}')">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Title Info Panel -->
            <div class="bg-cyan-300 dark:bg-cyan-950 p-6 rounded-none border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                <h3 class="text-lg font-black uppercase tracking-wider text-black dark:text-cyan-200">Dashboard Visual Control Center</h3>
                <p class="text-xs font-bold text-black/80 dark:text-cyan-300/80 mt-1">
                    Radar Jatuh Tempo Pembayaran & Timeline Hunian. Pantau tagihan bulanan (generate tgl 1, jatuh tempo tgl 10), eskalasi denda keterlambatan sewa (Bulan 1, 2, atau 3+), jadwal check-in/check-out, serta survei lokasi dari calon penyewa secara real-time.
                </p>
            </div>

            <!-- Legends Panel -->
            <div class="bg-white dark:bg-slate-900 border-4 border-black dark:border-white p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] rounded-none">
                <div class="text-xs font-black uppercase tracking-widest text-slate-500 mb-3 border-b-2 border-dashed border-slate-300 dark:border-slate-700 pb-1.5">
                    Legenda Status & Aturan Denda Bisnis
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3 text-[10px] font-black uppercase">
                    <div class="flex items-center gap-1.5 p-1.5 bg-blue-200 text-blue-900 border border-black">
                        <span>🔑</span> Check-in
                    </div>
                    <div class="flex items-center gap-1.5 p-1.5 bg-purple-200 text-purple-900 border border-black">
                        <span>🚪</span> Check-out
                    </div>
                    <div class="flex items-center gap-1.5 p-1.5 bg-amber-200 text-amber-900 border border-black">
                        <span>📍</span> Survei Lokasi
                    </div>
                    <div class="flex items-center gap-1.5 p-1.5 bg-slate-200 text-slate-900 border border-black">
                        <span>💸</span> Tagihan Normal
                    </div>
                    <div class="flex items-center gap-1.5 p-1.5 bg-green-300 text-green-900 border border-black">
                        <span>🟢</span> Telat M1 (Reminder)
                    </div>
                    <div class="flex items-center gap-1.5 p-1.5 bg-yellow-300 text-yellow-900 border border-black">
                        <span>🟡</span> Telat M2 (WA Wali)
                    </div>
                    <div class="flex items-center gap-1.5 p-1.5 bg-red-400 text-red-900 border border-black">
                        <span>🔴</span> Telat M3+ (Denda 5%)
                    </div>
                    <div class="flex items-center gap-1.5 p-1.5 bg-sky-200 text-sky-950 border border-black">
                        <span>📆</span> Auto-Pin Radar
                    </div>
                </div>
            </div>

            <!-- Controls Panel -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-900 border-4 border-black dark:border-white p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] rounded-none">
                <div class="flex items-center gap-3">
                    <button @click="prevMonth()" 
                            class="bg-yellow-400 hover:bg-yellow-500 text-black font-black p-3 border-2 border-black shadow-[3px_3px_0px_0px_#000000] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none hover:shadow-[4px_4px_0px_0px_#000000] hover:-translate-y-[1px] transition-all cursor-pointer rounded-none">
                        ◀
                    </button>
                    <span class="text-xl font-black uppercase text-black dark:text-white px-2 tracking-wider min-w-[200px] text-center" x-text="monthNames[currentMonth] + ' ' + currentYear"></span>
                    <button @click="nextMonth()" 
                            class="bg-yellow-400 hover:bg-yellow-500 text-black font-black p-3 border-2 border-black shadow-[3px_3px_0px_0px_#000000] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none hover:shadow-[4px_4px_0px_0px_#000000] hover:-translate-y-[1px] transition-all cursor-pointer rounded-none">
                        ▶
                    </button>

                    <!-- Loading Spinner -->
                    <div x-show="isLoading" class="ml-4 flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-widest animate-pulse" x-cloak>
                        <svg class="animate-spin h-5 w-5 text-black dark:text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Sinkronisasi data...</span>
                    </div>
                </div>
                
                <!-- Filters -->
                <div class="flex flex-wrap items-center gap-3 text-[10px] font-black uppercase">
                    <span class="text-slate-500 mr-1">Filter Tampilan:</span>
                    <label class="flex items-center gap-2 cursor-pointer bg-blue-100 dark:bg-blue-950 p-2 border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff] hover:-translate-y-0.5 transition-all">
                        <input type="checkbox" x-model="filters['check-in']" class="rounded-none border-2 border-black text-blue-600 focus:ring-0">
                        <span class="text-blue-900 dark:text-blue-200">🔑 Check-in</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer bg-purple-100 dark:bg-purple-950 p-2 border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff] hover:-translate-y-0.5 transition-all">
                        <input type="checkbox" x-model="filters['check-out']" class="rounded-none border-2 border-black text-purple-600 focus:ring-0">
                        <span class="text-purple-900 dark:text-purple-200">🚪 Check-out</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer bg-amber-100 dark:bg-amber-950 p-2 border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff] hover:-translate-y-0.5 transition-all">
                        <input type="checkbox" x-model="filters['survey']" class="rounded-none border-2 border-black text-amber-600 focus:ring-0">
                        <span class="text-amber-900 dark:text-amber-200">📍 Survei</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer bg-slate-100 dark:bg-slate-800 p-2 border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff] hover:-translate-y-0.5 transition-all">
                        <input type="checkbox" x-model="filters['tagihan']" class="rounded-none border-2 border-black text-slate-600 focus:ring-0">
                        <span class="text-slate-900 dark:text-slate-200">💸 Tagihan</span>
                    </label>
                </div>
            </div>

            <!-- Calendar Matrix Wrapper -->
            <div class="relative">
                <!-- Brutalist Loading Overlay -->
                <div x-show="isLoading" x-cloak
                     class="absolute inset-0 bg-yellow-300/90 dark:bg-yellow-950/90 z-50 flex items-center justify-center p-4 border-4 border-black dark:border-white shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,1)]">
                     <div class="bg-yellow-400 text-black border-4 border-black p-6 font-black text-base md:text-lg uppercase tracking-wider shadow-[6px_6px_0px_0px_#000000] select-none text-center">
                         ⚡ SEDANG MENARIK DATA KOST...
                     </div>
                </div>

                <!-- Calendar Month Grid Container (42 Cells Matrix) -->
                <div class="grid grid-cols-7 border-2 border-black dark:border-white shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,1)] bg-white dark:bg-slate-900 rounded-none overflow-hidden text-black dark:text-white">
                    
                    <!-- Day Headers -->
                    <template x-for="dayName in daysOfWeek" :key="dayName">
                        <div class="bg-black text-white font-black rounded-none border-2 border-black dark:border-white text-center py-3 text-xs tracking-widest select-none uppercase">
                            <span x-text="dayName"></span>
                        </div>
                    </template>
                    
                    <!-- Day Grid Cells (42 cells matrix) -->
                    <template x-for="day in daysGrid" :key="day.dateString">
                        <div :class="[
                                 day.isCurrentMonth 
                                     ? 'bg-white text-black border-2 border-black dark:bg-slate-900 dark:text-white dark:border-white' 
                                     : 'bg-gray-100 border-dashed border-2 border-black text-gray-400 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-500',
                                 getVisibleEvents(day.dateString).length > 0
                                     ? 'cursor-pointer hover:bg-yellow-50/20 dark:hover:bg-slate-800'
                                     : 'cursor-default'
                             ]"
                             class="min-h-[120px] max-h-[140px] p-2 relative flex flex-col justify-between group overflow-y-auto transition-all select-none"
                             @click="getVisibleEvents(day.dateString).length > 0 && openDayModal(day.dateString)">
                             
                             <!-- Cell Header: Date Number -->
                             <div class="flex justify-between items-center mb-1.5">
                                 <span :class="isToday(day.dateString) ? 'bg-black text-white dark:bg-white dark:text-black px-1.5 py-0.5 rounded-none shadow-[1px_1px_0px_0px_#000000] dark:shadow-[1px_1px_0px_0px_#ffffff]' : 'text-slate-800 dark:text-slate-200'" 
                                       class="text-sm font-black"
                                       x-text="day.dayNum"></span>
                             </div>
                             
                             <!-- Visible Event badges (up to 2 maximum) -->
                             <div class="flex-1 flex flex-col gap-1 overflow-hidden">
                                 <template x-for="event in getVisibleEvents(day.dateString).slice(0, 2)" :key="event.id">
                                     <a :href="event.url || '#'"
                                        @click.stop="if (event.url) { window.location.href = event.url; } else { openDayModal(day.dateString); }"
                                        :class="event.color"
                                        class="px-1.5 py-0.5 border border-black text-[9px] font-black truncate rounded-none shadow-[1px_1px_0px_0px_#000000] dark:shadow-[1px_1px_0px_0px_#ffffff] hover:opacity-85 transition-opacity block cursor-pointer"
                                        :title="event.title"
                                        x-text="event.title">
                                     </a>
                                 </template>
                                 
                                 <!-- Overflow Counter indicator -->
                                 <template x-if="getVisibleEvents(day.dateString).length > 2">
                                     <div @click.stop="openDayModal(day.dateString)"
                                          class="bg-black text-white text-[9px] font-black uppercase rounded-none cursor-pointer px-1.5 py-0.5 text-center mt-1 block select-none">
                                         <span x-text="'+' + (getVisibleEvents(day.dateString).length - 2) + ' agenda lainnya'"></span>
                                     </div>
                                 </template>
                             </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Custom Modal Detail Event -->
            <div x-show="modalOpen" 
                 x-cloak
                 class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
                 style="display: none;"
                 @keydown.escape.window="modalOpen = false">
                <div class="w-full max-w-lg bg-white dark:bg-slate-900 border-4 border-black dark:border-white shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,1)] p-6 rounded-none relative flex flex-col max-h-[85vh]"
                     @click.away="modalOpen = false">
                     
                    <!-- Close button -->
                    <button @click="modalOpen = false" 
                            class="absolute top-4 right-4 text-black dark:text-white font-black hover:bg-slate-100 dark:hover:bg-slate-800 p-2 border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none cursor-pointer rounded-none transition-all">
                        ✕
                    </button>
                    
                    <!-- Modal Header -->
                    <h3 class="text-base font-black text-black dark:text-white uppercase tracking-wider mb-4 border-b-4 border-black dark:border-white pb-3 pr-10 flex items-center gap-2">
                        <span>📅</span> Agenda Kost - <span x-text="formatHumanDate(selectedDate)"></span>
                    </h3>
                    
                    <!-- Modal Events List Container -->
                    <div class="flex-1 overflow-y-auto pr-1 space-y-4 max-h-[50vh]">
                        <template x-if="selectedDateEvents.length === 0">
                            <p class="text-xs font-bold text-slate-500 italic text-center py-8">Tidak ada agenda atau tagihan pada tanggal ini.</p>
                        </template>
                        
                        <template x-for="event in selectedDateEvents" :key="event.id">
                            <div class="p-4 border-2 border-black dark:border-white bg-slate-50 dark:bg-slate-950 shadow-[3px_3px_0px_0px_#000000] dark:shadow-[3px_3px_0px_0px_#ffffff] rounded-none flex flex-col gap-2">
                                <div class="flex items-start justify-between gap-2">
                                    <span :class="event.color" 
                                          class="px-2 py-0.5 border border-black text-[9px] font-black uppercase rounded-none shadow-[1px_1px_0px_0px_#000000] dark:shadow-[1px_1px_0px_0px_#ffffff]"
                                          x-text="event.title"></span>
                                    <span class="text-[9px] font-black uppercase text-slate-500" x-text="event.status"></span>
                                </div>
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-1 leading-relaxed" x-text="event.description"></p>
                                
                                <!-- Meta fields details -->
                                <template x-if="event.type !== 'static-pin'">
                                    <div class="text-[10px] font-bold text-slate-500 dark:text-slate-400 grid grid-cols-2 gap-2 mt-2 pt-2 border-t border-dashed border-slate-300 dark:border-slate-800">
                                        <div>Penyewa: <span class="text-slate-800 dark:text-slate-200 font-extrabold" x-text="event.metadata.penyewa_nama || '-'"></span></div>
                                        <div>Nomor Kamar: <span class="text-slate-800 dark:text-slate-200 font-extrabold" x-text="event.metadata.kamar_nomor || '-'"></span></div>
                                        <template x-if="event.metadata.status_tagihan">
                                            <div>Status Tagihan: <span class="text-slate-800 dark:text-slate-200 font-extrabold capitalize" x-text="event.metadata.status_tagihan"></span></div>
                                        </template>
                                        <template x-if="event.metadata.status_reservasi">
                                            <div>Status Reservasi: <span class="text-slate-800 dark:text-slate-200 font-extrabold capitalize" x-text="event.metadata.status_reservasi"></span></div>
                                        </template>
                                    </div>
                                </template>
                                
                                <!-- Action button links to Laravel Show view -->
                                <template x-if="event.metadata && event.metadata.url">
                                    <div class="mt-2.5 flex justify-end">
                                        <a :href="event.metadata.url" 
                                           class="inline-flex items-center justify-center bg-yellow-400 hover:bg-yellow-500 text-black text-[9px] font-black uppercase py-1.5 px-3 border-2 border-black shadow-[2px_2px_0px_0px_#000000] hover:translate-x-[-1px] hover:translate-y-[-1px] hover:shadow-[3px_3px_0px_0px_#000000] active:translate-x-[0px] active:translate-y-[0px] active:shadow-[1px_1px_0px_0px_#000000] transition-all rounded-none cursor-pointer">
                                            Lihat Detail Rinci &rarr;
                                        </a>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                    
                    <!-- Modal Footer Actions -->
                    <div class="mt-4 border-t-2 border-black dark:border-white pt-4 flex justify-end">
                        <button @click="modalOpen = false" 
                                class="inline-flex items-center justify-center bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200 font-black py-2 px-5 border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff] hover:translate-x-[-1px] hover:translate-y-[-1px] hover:shadow-[3px_3px_0px_0px_#000000] dark:hover:shadow-[3px_3px_0px_0px_#ffffff] active:translate-x-[0px] active:translate-y-[0px] active:shadow-[1px_1px_0px_0px_#000000] transition-all text-xs uppercase tracking-wider cursor-pointer rounded-none">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        function calendarController(eventsApiUrl) {
            const TOTAL_GRID_CELLS = 42;

            return {
                apiUrl: eventsApiUrl,
                currentYear: new Date().getFullYear(),
                currentMonth: new Date().getMonth(), // 0-indexed (0 = Jan, 11 = Des)
                monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                daysOfWeek: ['MIN', 'SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB'],
                daysGrid: [], 
                rawEventsMap: {}, 
                filters: {
                    'check-in': true,
                    'check-out': true,
                    'survey': true,
                    'tagihan': true,
                },
                selectedDate: '',
                selectedDateEvents: [],
                modalOpen: false,
                isLoading: false,

                init() {
                    this.generateGrid();
                },

                generateGrid() {
                    this.daysGrid = [];
                    
                    // Hari pertama dari bulan aktif
                    const firstDayIndex = new Date(this.currentYear, this.currentMonth, 1).getDay();
                    
                    // Jumlah hari di bulan aktif
                    const totalDaysCurrentMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                    
                    // Jumlah hari di bulan sebelumnya
                    const totalDaysPrevMonth = new Date(this.currentYear, this.currentMonth, 0).getDate();

                    // 1. Isi hari-hari sisa dari bulan sebelumnya
                    for (let i = firstDayIndex - 1; i >= 0; i--) {
                        const dayNum = totalDaysPrevMonth - i;
                        const d = new Date(this.currentYear, this.currentMonth - 1, dayNum);
                        this.daysGrid.push({
                            date: d,
                            dateString: this.formatDateString(d),
                            dayNum: dayNum,
                            isCurrentMonth: false
                        });
                    }

                    // 2. Isi hari-hari bulan berjalan
                    for (let i = 1; i <= totalDaysCurrentMonth; i++) {
                        const d = new Date(this.currentYear, this.currentMonth, i);
                        this.daysGrid.push({
                            date: d,
                            dateString: this.formatDateString(d),
                            dayNum: i,
                            isCurrentMonth: true
                        });
                    }

                    // 3. Isi hari-hari sisa untuk melengkapi grid 42 cells (6 baris x 7 kolom)
                    const remainingCells = TOTAL_GRID_CELLS - this.daysGrid.length;
                    for (let i = 1; i <= remainingCells; i++) {
                        const d = new Date(this.currentYear, this.currentMonth + 1, i);
                        this.daysGrid.push({
                            date: d,
                            dateString: this.formatDateString(d),
                            dayNum: i,
                            isCurrentMonth: false
                        });
                    }

                    // Hitung rentang start dan end date dari seluruh grid yang digambar
                    const startDate = this.daysGrid[0].dateString;
                    const endDate = this.daysGrid[this.daysGrid.length - 1].dateString;
                    
                    this.fetchEvents(startDate, endDate);
                },

                fetchEvents(startDate, endDate) {
                    this.isLoading = true;
                    fetch(`${this.apiUrl}?start_date=${startDate}&end_date=${endDate}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Response API error');
                            }
                            return response.json();
                        })
                        .then(data => {
                            const grouped = {};
                            data.forEach(event => {
                                if (!grouped[event.date]) {
                                    grouped[event.date] = [];
                                }
                                grouped[event.date].push(event);
                            });
                            this.rawEventsMap = grouped;
                            this.isLoading = false;
                        })
                        .catch(err => {
                            console.error('Gagal memuat event kalender:', err);
                            this.isLoading = false;
                            if (typeof window.showToast === 'function') {
                                window.showToast('Gagal memuat data agenda kalender', 'error');
                            }
                        });
                },

                getVisibleEvents(dateStr) {
                    const list = [];

                    // Menambahkan Pin Radar Jatuh Tempo Otomatis (Tanggal 1 dan 10)
                    const parts = dateStr.split('-');
                    const dayNum = parseInt(parts[2], 10);
                    
                    if (dayNum === 1) {
                        list.push({
                            id: `static-pin-${dateStr}-generate`,
                            title: '📆 Radar: Generate Tagihan',
                            type: 'static-pin',
                            color: 'bg-sky-200 text-sky-950 border-sky-400',
                            status: 'Sistem Terjadwal',
                            description: 'Radar Jatuh Tempo: Tanggal generate invoice sewa bulanan secara massal otomatis.',
                            metadata: {}
                        });
                    } else if (dayNum === 10) {
                        list.push({
                            id: `static-pin-${dateStr}-due`,
                            title: '⏰ Batas Jatuh Tempo',
                            type: 'static-pin',
                            color: 'bg-rose-200 text-rose-950 border-rose-400',
                            status: 'Sistem Terjadwal',
                            description: 'Radar Jatuh Tempo: Batas akhir pelunasan pembayaran sewa sebelum denda berjalan.',
                            metadata: {}
                        });
                    }

                    // Menambahkan data agenda dari database
                    const dayEvents = this.rawEventsMap[dateStr] || [];
                    dayEvents.forEach(evt => {
                        if (this.filters[evt.type] === true) {
                            list.push(evt);
                        }
                    });

                    return list;
                },

                prevMonth() {
                    if (this.currentMonth === 0) {
                        this.currentMonth = 11;
                        this.currentYear--;
                    } else {
                        this.currentMonth--;
                    }
                    this.generateGrid();
                },

                nextMonth() {
                    if (this.currentMonth === 11) {
                        this.currentMonth = 0;
                        this.currentYear++;
                    } else {
                        this.currentMonth++;
                    }
                    this.generateGrid();
                },

                formatDateString(date) {
                    const y = date.getFullYear();
                    const m = String(date.getMonth() + 1).padStart(2, '0');
                    const d = String(date.getDate()).padStart(2, '0');
                    return `${y}-${m}-${d}`;
                },

                openDayModal(dateStr) {
                    const visibleEvents = this.getVisibleEvents(dateStr);
                    if (visibleEvents.length === 0) {
                        return; // Empty state protection
                    }
                    this.selectedDate = dateStr;
                    this.selectedDateEvents = visibleEvents;
                    this.modalOpen = true;
                },

                formatHumanDate(dateStr) {
                    if (!dateStr) return '';
                    const parts = dateStr.split('-');
                    const d = parseInt(parts[2], 10);
                    const m = this.monthNames[parseInt(parts[1], 10) - 1];
                    const y = parts[0];
                    return `${d} ${m} ${y}`;
                },

                isToday(dateStr) {
                    const today = this.formatDateString(new Date());
                    return today === dateStr;
                }
            };
        }
    </script>
    @endpush
</x-app-layout>

