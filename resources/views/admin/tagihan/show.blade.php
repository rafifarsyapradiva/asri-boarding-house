<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Detail Tagihan: ' . $tagihan->order_id) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Alert Notifications -->
            <x-flash-message />

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left panel: Detail Tagihan & Detail Penyewa -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Detail Tagihan Card -->
                    <div class="admin-card">
                        <div class="flex justify-between items-center mb-6 border-b-2 border-black dark:border-white pb-4">
                            <div>
                                <h3 class="text-lg font-black text-black dark:text-white uppercase tracking-wider">Informasi Tagihan</h3>
                                <p class="admin-subtitle">Rincian detail tagihan bulanan yang diterbitkan sistem.</p>
                            </div>
                            <a href="{{ route('admin.tagihan.index') }}" class="admin-btn-secondary !py-1 px-3 !text-xs">&larr; Kembali</a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <span class="text-xs text-slate-400 font-medium uppercase">Order ID</span>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ $tagihan->order_id }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 font-medium uppercase">Periode Tagihan</span>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ sprintf('%02d/%d', $tagihan->periode_bulan, $tagihan->periode_tahun) }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 font-medium uppercase">Tanggal Terbit</span>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ $tagihan->tanggal_tagihan ? $tagihan->tanggal_tagihan->format('d M Y') : '-' }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 font-medium uppercase">Jatuh Tempo</span>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ $tagihan->tanggal_jatuh_tempo ? $tagihan->tanggal_jatuh_tempo->format('d M Y') : '-' }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 font-medium uppercase">Keterlambatan</span>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ $tagihan->bulan_keterlambatan }} Bulan</p>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 font-medium uppercase">Status Pembayaran</span>
                                <div class="mt-1">
                                    <span class="admin-badge {{ $tagihan->status_badge_class }}">
                                        {{ strtoupper($tagihan->computed_status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline Eskalasi Keterlambatan (Bulan 1, Bulan 2, Bulan 3+) -->
                        <div class="mt-6 border-4 border-black dark:border-white p-4 rounded-none bg-white dark:bg-slate-950 shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff]">
                            <span class="admin-label font-black mb-3">Tingkat Eskalasi Keterlambatan</span>
                            <div class="grid grid-cols-3 gap-2">
                                <!-- Tahap 1: Bulan 1 -->
                                <div class="border-2 border-black p-2 rounded-none text-center {{ $tagihan->bulan_keterlambatan >= 1 ? 'bg-yellow-300 text-black font-black' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500' }}">
                                    <div class="text-[10px] uppercase font-black">Bulan 1</div>
                                    <div class="text-[9px] font-bold">Reminder WA</div>
                                </div>
                                <!-- Tahap 2: Bulan 2 -->
                                <div class="border-2 border-black p-2 rounded-none text-center {{ $tagihan->bulan_keterlambatan >= 2 ? 'bg-orange-400 text-black font-black' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500' }}">
                                    <div class="text-[10px] uppercase font-black">Bulan 2</div>
                                    <div class="text-[9px] font-bold">Notifikasi Wali</div>
                                </div>
                                <!-- Tahap 3: Bulan 3+ -->
                                <div class="border-2 border-black p-2 rounded-none text-center {{ $tagihan->bulan_keterlambatan >= 3 ? 'bg-red-500 text-white font-black' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500' }}">
                                    <div class="text-[10px] uppercase font-black">Bulan 3+</div>
                                    <div class="text-[9px] font-bold">Denda 5% / Bln</div>
                                </div>
                            </div>
                            
                            @if($tagihan->bulan_keterlambatan > 0)
                                <p class="text-[10px] text-slate-600 dark:text-slate-300 mt-3 font-bold leading-relaxed">
                                    * Status saat ini: 
                                    @if($tagihan->bulan_keterlambatan == 1)
                                        Terlambat 1 bulan. Tagihan dibebaskan dari denda. Reminder dikirimkan langsung ke penyewa.
                                    @elseif($tagihan->bulan_keterlambatan == 2)
                                        Terlambat 2 bulan. Tagihan dibebaskan dari denda. Pesan peringatan eskalasi dikirimkan ke Wali ({{ $tagihan->penyewa?->nama_wali ?? '-' }}).
                                    @else
                                        Terlambat {{ $tagihan->bulan_keterlambatan }} bulan. Denda aktif akumulatif sebesar 5% per bulan keterlambatan (Total denda: Rp {{ number_format($tagihan->nominal_denda, 0, ',', '.') }}).
                                    @endif
                                </p>
                            @else
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-3 font-semibold italic">
                                    * Tagihan belum mengalami keterlambatan pembayaran.
                                </p>
                            @endif
                        </div>

                        <div class="mt-8 border-t-2 border-black dark:border-white pt-6 space-y-3 bg-slate-50 dark:bg-slate-900 border-2 border-black dark:border-white p-5 rounded-none shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500 dark:text-slate-400 font-extrabold uppercase text-xs">Harga Pokok Kamar:</span>
                                <span class="font-black text-slate-800 dark:text-slate-100">Rp {{ number_format($tagihan->nominal_pokok, 0, ',', '.') }}</span>
                            </div>
                            @if($tagihan->nominal_deposit > 0)
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-slate-500 dark:text-slate-400 font-extrabold uppercase text-xs">Uang Deposit Jaminan:</span>
                                    <span class="font-black text-slate-800 dark:text-slate-100">+ Rp {{ number_format($tagihan->nominal_deposit, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500 dark:text-slate-400 font-extrabold uppercase text-xs">Denda Keterlambatan:</span>
                                <span class="font-black text-red-600 dark:text-red-400">+ Rp {{ number_format($tagihan->nominal_denda, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center border-t-2 border-black dark:border-white pt-3 text-base font-black">
                                <span class="text-slate-800 dark:text-slate-100 uppercase tracking-wider text-sm">Total Tagihan:</span>
                                <span class="text-blue-600 dark:text-blue-400 text-lg">Rp {{ number_format($tagihan->nominal_total, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        @if($tagihan->keterangan)
                            <div class="mt-6 p-4 bg-slate-50 dark:bg-slate-900 border-2 border-black dark:border-white rounded-none shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]">
                                <span class="admin-label font-black">Keterangan Tambahan</span>
                                <p class="text-sm text-slate-700 dark:text-slate-300 mt-1 leading-relaxed font-bold">{{ $tagihan->keterangan }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Detail Penyewa Card -->
                    <div class="admin-card">
                        <h3 class="text-lg font-black text-black dark:text-white uppercase tracking-wider mb-6 border-b-2 border-black dark:border-white pb-4">Informasi Penyewa</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <span class="text-xs text-slate-400 font-medium uppercase">Nama Penyewa</span>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ $tagihan->penyewa?->user?->nama ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 font-medium uppercase">Kamar & Tipe</span>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">Kamar {{ $tagihan->penyewa?->kamar?->nomor_kamar ?? '-' }} ({{ ucfirst($tagihan->penyewa?->kamar?->tipe ?? '-') }})</p>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 font-medium uppercase">Email</span>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ $tagihan->penyewa?->user?->email ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 font-medium uppercase">No. HP</span>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ $tagihan->penyewa?->user?->no_hp ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 font-medium uppercase">Nama Wali</span>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ $tagihan->penyewa?->nama_wali ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 font-medium uppercase">No. HP Wali</span>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ $tagihan->penyewa?->no_wali ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right panel: Aksi Konfirmasi / Detail Pembayaran -->
                <div>
                    @if($tagihan->canBeConfirmedManually())
                        <!-- Form Konfirmasi Cash -->
                        <div class="admin-card bg-amber-100 dark:bg-amber-950/40">
                            <h3 class="text-lg font-black text-black dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2 border-b-2 border-black dark:border-white pb-2">
                                <span class="text-amber-500">💵</span>
                                Konfirmasi Cash
                            </h3>
                            <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed mb-6 font-bold">
                                Gunakan form ini jika penyewa membayar sewa kost secara tunai langsung kepada admin.
                            </p>
                            <form action="{{ route('admin.tagihan.konfirmasiCash', $tagihan) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="catatan" class="admin-label">Catatan Internal (Opsional)</label>
                                    <textarea id="catatan" name="catatan" class="admin-textarea !h-24" placeholder="Contoh: Pembayaran cash diterima oleh penjaga kost, lunas tanpa denda.">{{ old('catatan') }}</textarea>
                                    @error('catatan')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <button type="submit" class="admin-btn-success w-full mt-2">
                                    Konfirmasi Pembayaran Cash
                                </button>
                            </form>
                        </div>
                    @elseif($tagihan->computed_status === 'lunas')
                        <!-- Informasi Pembayaran Sukses -->
                        <div class="admin-card bg-emerald-100 dark:bg-emerald-950/40">
                            <h3 class="text-lg font-black text-emerald-800 dark:text-emerald-400 mb-6 flex items-center gap-2 border-b-2 border-black dark:border-white pb-2">
                                <span>✅</span>
                                Pembayaran Lunas
                            </h3>

                            @forelse($tagihan->pembayaran as $p)
                                <div class="space-y-4 text-sm border-t-2 border-black dark:border-white pt-4 mt-4 first:border-t-0 first:pt-0 first:mt-0 font-bold">
                                    <div>
                                        <span class="text-xs text-slate-400 font-bold uppercase">ID Transaksi</span>
                                        <p class="text-sm font-black text-slate-800 dark:text-slate-100 mt-0.5">{{ $p->transaction_id }}</p>
                                    </div>
                                    <div>
                                        <span class="text-xs text-slate-400 font-bold uppercase">Tipe Pembayaran</span>
                                        <p class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase mt-0.5">{{ $p->payment_type ?: '-' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-xs text-slate-400 font-bold uppercase">Jumlah Dibayar</span>
                                        <p class="text-sm font-black text-slate-800 dark:text-slate-100 mt-0.5">Rp {{ number_format($p->nominal, 0, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <span class="text-xs text-slate-400 font-bold uppercase">Tanggal Bayar</span>
                                        <p class="text-sm font-black text-slate-800 dark:text-slate-100 mt-0.5">{{ $p->tanggal_bayar ? ($p->tanggal_bayar instanceof \Carbon\Carbon ? $p->tanggal_bayar : \Carbon\Carbon::parse($p->tanggal_bayar))->format('d M Y H:i') : '-' }}</p>
                                    </div>
                                    @if($p->payment_type === 'cash')
                                        <div>
                                            <span class="text-xs text-slate-400 font-bold uppercase">Dikonfirmasi Oleh</span>
                                            <p class="text-sm font-black text-slate-800 dark:text-slate-100 mt-0.5">{{ $p->dikonfirmasiOleh->nama ?? 'Admin' }}</p>
                                        </div>
                                    @else
                                        @if($p->bank)
                                            <div>
                                                <span class="text-xs text-slate-400 font-bold uppercase">Bank</span>
                                                <p class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase mt-0.5">{{ $p->bank }}</p>
                                            </div>
                                        @endif
                                        @if($p->va_number)
                                            <div>
                                                <span class="text-xs text-slate-400 font-bold uppercase">VA Number</span>
                                                <p class="text-sm font-black text-slate-800 dark:text-slate-100 mt-0.5">{{ $p->va_number }}</p>
                                            </div>
                                        @endif
                                    @endif
                                    @if($p->pdf_path)
                                        <div class="pt-2">
                                            <a href="{{ Storage::url($p->pdf_path) }}" target="_blank" rel="noopener noreferrer" class="admin-btn-secondary w-full gap-2">
                                                <span>📄</span> Download Nota PDF
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-sm text-slate-400 italic font-bold">Pembayaran lunas via modifikasi admin.</p>
                            @endforelse
                        </div>
                    @else
                        <!-- Status Lainnya (Gagal, Kadaluarsa) -->
                        <div class="admin-card bg-red-100 dark:bg-red-950/40">
                            <h3 class="text-lg font-black text-red-800 dark:text-red-400 mb-2 border-b-2 border-black dark:border-white pb-2">Status: {{ ucfirst($tagihan->computed_status) }}</h3>
                            <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed font-bold">
                                Tagihan ini sudah kadaluarsa atau gagal diproses oleh sistem payment gateway. Silakan hubungi penyewa untuk melakukan generate tagihan ulang atau konfirmasi pembayaran manual jika diperlukan.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
