<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Detail Reservasi') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showCancelModal: false, showDeleteModal: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Alert Notifications Component -->
            <x-flash-message />

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Section Data Calon Penyewa -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="admin-card">
                        <div class="flex justify-between items-center mb-6 border-b-2 border-black dark:border-white pb-4">
                            <div>
                                <h3 class="text-lg font-black text-black dark:text-white uppercase tracking-wider">Informasi Reservasi</h3>
                                <p class="admin-subtitle">Rincian detail booking kamar yang diajukan calon penyewa.</p>
                            </div>
                            <a href="{{ route('admin.reservasi.index') }}" class="admin-btn-secondary !py-1 px-3 !text-xs">&larr; Kembali</a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <span class="text-xs text-slate-400 font-medium uppercase">Nama Lengkap</span>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ $reservasi->user?->nama ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 font-medium uppercase">Email</span>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ $reservasi->user?->email ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 font-medium uppercase">No HP</span>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ $reservasi->user?->no_hp ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 font-medium uppercase">Kamar</span>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">Kamar {{ $reservasi->kamar?->nomor_kamar ?? '-' }} (Lantai {{ $reservasi->kamar?->lantai ?? '-' }})</p>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 font-medium uppercase">Tipe Sewa & Durasi</span>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-100 capitalize mt-0.5">{{ $reservasi->tipe_sewa }} ({{ $reservasi->durasi }} Bulan)</p>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 font-medium uppercase">Rentang Sewa</span>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">
                                    {{ $reservasi->tanggal_mulai ? $reservasi->tanggal_mulai->format('d M Y') : '-' }} s/d 
                                    {{ $reservasi->tanggal_selesai ? $reservasi->tanggal_selesai->format('d M Y') : '-' }}
                                </p>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 font-medium uppercase">Total Harga</span>
                                <p class="text-sm font-extrabold text-green-600 dark:text-green-400 mt-0.5">Rp {{ number_format($reservasi->total_harga, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 font-medium uppercase">Status Transaksi</span>
                                <div class="mt-1">
                                    <x-reservation-status-badge :status="$reservasi->status" />
                                </div>
                            </div>
                            @if($reservasi->is_dp)
                                <div>
                                    <span class="text-xs text-slate-400 font-medium uppercase">Skema Pembayaran</span>
                                    <p class="text-sm font-bold text-blue-600 dark:text-blue-400 mt-0.5">Uang Muka (DP): Rp {{ number_format($reservasi->nominal_dp, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <span class="text-xs text-slate-400 font-medium uppercase">Sisa Pelunasan</span>
                                    <p class="text-sm font-bold text-red-600 dark:text-red-400 mt-0.5">Sisa Tagihan: Rp {{ number_format($reservasi->nominal_sisa, 0, ',', '.') }}</p>
                                </div>
                            @else
                                <div class="sm:col-span-2">
                                    <span class="text-xs text-slate-400 font-medium uppercase">Skema Pembayaran</span>
                                    <p class="text-sm font-bold text-green-600 dark:text-green-400 mt-0.5">Pelunasan Penuh (Full Payment)</p>
                                </div>
                            @endif
                        </div>

                        @if($reservasi->catatan_user)
                            <div class="bg-slate-50 dark:bg-slate-900 border-2 border-black dark:border-white p-4 rounded-none mt-6 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]">
                                <span class="admin-label">Catatan Calon Penyewa</span>
                                <p class="text-sm text-slate-700 dark:text-slate-300 mt-1 leading-relaxed font-bold">{{ $reservasi->catatan_user }}</p>
                            </div>
                        @endif

                        @if($reservasi->catatan_admin)
                            <div class="bg-cyan-50 dark:bg-cyan-950/30 border-2 border-black dark:border-white p-4 rounded-none mt-6 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]">
                                <span class="admin-label !text-cyan-800 dark:!text-cyan-400 font-black">Catatan Admin</span>
                                <p class="text-sm text-cyan-900 dark:text-cyan-200 mt-1 leading-relaxed font-bold">{{ $reservasi->catatan_admin }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Obrolan Chat Section -->
                    <div>
                        @include('reservasi.chat-box')
                    </div>
                </div>

                <!-- Section Formulir Konfirmasi -->
                <div>
                    <div class="admin-card">
                        <h3 class="text-lg font-black text-black dark:text-white uppercase tracking-wider mb-6 border-b-2 border-black dark:border-white pb-2">Aksi Verifikasi</h3>
                        
                        @if(in_array(strtolower($reservasi->status), ['dp', 'lunas']))
                            <form action="{{ route('admin.reservasi.konfirmasi', $reservasi->id) }}" method="POST" class="space-y-5" data-confirm="Apakah Anda yakin data verifikasi sudah benar? Tindakan ini akan mengaktifkan penyewa dan mengubah status kamar menjadi Terisi." data-title="Konfirmasi Verifikasi" data-confirm-danger="false">
                                @csrf
                                
                                <div>
                                    <label for="nik" class="admin-label">NIK Calon Penyewa (16 Digit)</label>
                                    <input type="text" name="nik" id="nik" value="{{ old('nik', $reservasi->user?->nik ?? '') }}" required maxlength="16" minlength="16" class="admin-input">
                                    @error('nik')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="nama_wali" class="admin-label">Nama Wali / Orang Tua</label>
                                    <input type="text" name="nama_wali" id="nama_wali" value="{{ old('nama_wali', $reservasi->user?->nama_wali ?? '') }}" required class="admin-input">
                                    @error('nama_wali')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="no_wali" class="admin-label">No HP Wali / Orang Tua</label>
                                    <input type="text" name="no_wali" id="no_wali" value="{{ old('no_wali', $reservasi->user?->no_wali ?? '') }}" required placeholder="Contoh: 0812XXXXXXXX" class="admin-input">
                                    @error('no_wali')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="deposit" class="admin-label">Uang Jaminan / Deposit (Opsional)</label>
                                    <input type="number" name="deposit" id="deposit" value="{{ old('deposit', 0) }}" min="0" placeholder="Contoh: 500000" class="admin-input">
                                    @error('deposit')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="catatan_admin" class="admin-label">Catatan Tambahan Admin</label>
                                    <textarea name="catatan_admin" id="catatan_admin" class="admin-textarea !h-20">{{ old('catatan_admin') }}</textarea>
                                    @error('catatan_admin')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <button type="submit" class="admin-btn-success w-full mt-2">
                                    Konfirmasi & Aktifkan Penyewa
                                </button>
                            </form>

                            <div class="mt-4 pt-4 border-t-2 border-black dark:border-white">
                                <button type="button" @click="showCancelModal = true"
                                    class="w-full admin-btn-danger">
                                    Batalkan Reservasi
                                </button>
                            </div>
                        @elseif(strtolower($reservasi->status) === 'pending')
                            <div class="space-y-4">
                                <div class="text-center py-5 px-4 text-slate-400 bg-slate-50 dark:bg-slate-950 border-4 border-black dark:border-white rounded-none shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                                    <div class="text-3xl mb-1">⏳</div>
                                    <p class="text-sm font-black text-black dark:text-white uppercase tracking-wider">Menunggu Pembayaran</p>
                                    <p class="text-xs mt-1 text-slate-500 dark:text-slate-400 font-bold">Calon penyewa belum melakukan pembayaran DP / lunas penuh.</p>
                                </div>

                                <button type="button" @click="showCancelModal = true"
                                    class="w-full admin-btn-danger">
                                    Batalkan Reservasi
                                </button>
                            </div>
                        @else
                            <div class="text-center py-8 px-4 text-slate-400 bg-slate-50 dark:bg-slate-950 border-4 border-black dark:border-white rounded-none shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                                <div class="text-3xl mb-2">ℹ️</div>
                                <p class="text-sm font-black text-black dark:text-white uppercase tracking-wider">Tidak Ada Aksi Verifikasi</p>
                                <p class="text-xs mt-1 text-slate-500 dark:text-slate-400 font-bold {{ strtolower($reservasi->status) === 'batal' ? 'mb-4' : '' }}">
                                    Reservasi saat ini berstatus <span class="font-black uppercase text-black dark:text-white">{{ $reservasi->status }}</span>.
                                </p>
                                @if(strtolower($reservasi->status) === 'batal')
                                    <button type="button" @click="showDeleteModal = true" class="w-full admin-btn-danger !py-2 !text-xs">
                                        Hapus Permanen Data Ini
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Single Shared Cancel Modal Backdrop -->
        <div x-show="showCancelModal" x-cloak style="display: none;"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-in fade-in duration-150">
            
            <!-- Modal Content Container -->
            <div class="bg-white dark:bg-slate-800 p-6 sm:p-8 max-w-md w-full rounded-none border-4 border-black dark:border-white shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,1)] relative text-left">
                <!-- Header -->
                <div class="text-center mb-6">
                    <span class="text-3xl block mb-2">⚠️</span>
                    <h3 class="text-xl font-black text-black dark:text-white uppercase tracking-wider">Konfirmasi Pembatalan</h3>
                </div>

                <!-- Body -->
                <p class="text-sm text-slate-700 dark:text-slate-300 mb-6 leading-relaxed text-center font-bold">
                    Apakah Anda yakin ingin membatalkan reservasi ini? Tindakan ini tidak dapat dibatalkan.
                </p>

                <!-- Actions Footer -->
                <div class="flex gap-4">
                    <!-- Cancel button -->
                    <button type="button" @click="showCancelModal = false"
                        class="flex-1 admin-btn-secondary !py-2">
                        Tidak, Kembali
                    </button>
                    <!-- Confirm button -->
                    <form action="{{ route('admin.reservasi.batal', $reservasi->id) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit"
                            class="w-full admin-btn-danger !py-2">
                            Ya, Batalkan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Single Shared Delete Modal Backdrop -->
        <div x-show="showDeleteModal" x-cloak style="display: none;"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-in fade-in duration-150">
            
            <!-- Modal Content Container -->
            <div class="bg-white dark:bg-slate-800 p-6 sm:p-8 max-w-md w-full rounded-none border-4 border-black dark:border-white shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,1)] relative text-left">
                <!-- Header -->
                <div class="text-center mb-6">
                    <span class="text-3xl block mb-2">⚠️</span>
                    <h3 class="text-xl font-black text-black dark:text-white uppercase tracking-wider">Konfirmasi Hapus</h3>
                </div>

                <!-- Body -->
                <p class="text-sm text-slate-700 dark:text-slate-300 mb-6 leading-relaxed text-center font-bold">
                    Apakah Anda yakin ingin menghapus data reservasi ini secara permanen dari database?
                </p>

                <!-- Actions Footer -->
                <div class="flex gap-4">
                    <!-- Cancel button -->
                    <button type="button" @click="showDeleteModal = false"
                        class="flex-1 admin-btn-secondary !py-2">
                        Tidak, Batal
                    </button>
                    <!-- Confirm button -->
                    <form action="{{ route('admin.reservasi.destroy', $reservasi->id) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full admin-btn-danger !py-2">
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
