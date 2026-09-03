<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Detail Penyewa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">

                    <!-- Header -->
                    <div class="flex justify-between items-center mb-8 border-b border-slate-100 dark:border-slate-700/50 pb-4">
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100">Detail Profil Penyewa</h3>
                            <p class="admin-subtitle">Informasi lengkap data diri, kamar sewa, dan wali penyewa.</p>
                        </div>
                        <a href="{{ route('admin.penyewa.index') }}" class="text-sm font-bold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition duration-150">&larr; Kembali ke Daftar</a>
                    </div>

                    <!-- Main Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                        <!-- Left Column: Data Diri & Akun -->
                        <div class="space-y-6">
                            <div>
                                <h4 class="font-bold text-xs text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-3">1. Data Diri & Akun</h4>
                                <div class="bg-white dark:bg-slate-900 border-2 border-black dark:border-white p-5 rounded-none space-y-4 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]">
                                    <div>
                                        <span class="text-xs text-slate-400 font-medium">Nama Lengkap</span>
                                        <div class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ $penyewa->user->nama ?? '-' }}</div>
                                    </div>
                                    <div>
                                        <span class="text-xs text-slate-400 font-medium">NIK (KTP)</span>
                                        <div class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ $penyewa->nik }}</div>
                                    </div>
                                    <div>
                                        <span class="text-xs text-slate-400 font-medium">Alamat Email</span>
                                        <div class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ $penyewa->user->email ?? '-' }}</div>
                                    </div>
                                    <div>
                                        <span class="text-xs text-slate-400 font-medium">Nomor HP / WhatsApp</span>
                                        <div class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ $penyewa->user->no_hp ?? '-' }}</div>
                                    </div>
                                    <div>
                                        <span class="text-xs text-slate-400 font-medium">Status Akun</span>
                                        <div class="mt-1.5">
                                            @if($penyewa->status === 'aktif')
                                                <span class="admin-badge admin-badge-success">Aktif</span>
                                            @else
                                                <span class="admin-badge admin-badge-danger">Nonaktif / Keluar</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Wali -->
                            <div>
                                <h4 class="font-bold text-xs text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-3">2. Wali / Orang Tua</h4>
                                <div class="bg-white dark:bg-slate-900 border-2 border-black dark:border-white p-5 rounded-none space-y-4 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]">
                                    <div>
                                        <span class="text-xs text-slate-400 font-medium">Nama Wali</span>
                                        <div class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ $penyewa->nama_wali }}</div>
                                    </div>
                                    <div>
                                        <span class="text-xs text-slate-400 font-medium">Nomor HP Wali</span>
                                        <div class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">{{ $penyewa->no_wali }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Kamar & Detail Sewa -->
                        <div class="space-y-6">
                            <div>
                                <h4 class="font-bold text-xs text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-3">3. Informasi Kamar & Kontrak</h4>
                                <div class="bg-white dark:bg-slate-900 border-2 border-black dark:border-white p-5 rounded-none space-y-4 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]">
                                    <div>
                                        <span class="text-xs text-slate-400 font-medium">Nomor Kamar</span>
                                        <div class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">
                                            @if($penyewa->kamar)
                                                Kamar {{ $penyewa->kamar->nomor_kamar }} (Lantai {{ $penyewa->kamar->lantai }})
                                            @else
                                                <span class="text-slate-400 italic">Belum ditentukan</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <span class="text-xs text-slate-400 font-medium">Tipe & Harga Pokok Kamar</span>
                                        <div class="text-sm font-bold text-slate-800 dark:text-slate-100 capitalize mt-0.5">
                                            @if($penyewa->kamar)
                                                {{ $penyewa->kamar->tipe }} - Rp {{ number_format($penyewa->kamar->harga_bulan, 0, ',', '.') }}/bln
                                            @else
                                                <span class="text-slate-400 italic">-</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <span class="text-xs text-slate-400 font-medium">Harga Sewa Aktif Penyewa</span>
                                        <div class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">
                                            Rp {{ number_format($penyewa->harga_sewa ?? ($penyewa->kamar ? $penyewa->kamar->harga_bulan : 0), 0, ',', '.') }}/bln
                                        </div>
                                    </div>
                                    <div>
                                        <span class="text-xs text-slate-400 font-medium">Tipe Sewa & Durasi</span>
                                        <div class="text-sm font-bold text-slate-800 dark:text-slate-100 capitalize mt-0.5">
                                            {{ $penyewa->durasi_formatted ?? ($penyewa->tipe_sewa . ' (' . $penyewa->durasi . ')') }}
                                        </div>
                                    </div>
                                    <div>
                                        <span class="text-xs text-slate-400 font-medium">Tanggal Masuk</span>
                                        <div class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">
                                            {{ \Carbon\Carbon::parse($penyewa->tanggal_masuk)->format('d F Y') }}
                                        </div>
                                    </div>
                                    <div>
                                        <span class="text-xs text-slate-400 font-medium">Tanggal Keluar (Jika Sudah Checkout)</span>
                                        <div class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5">
                                            {{ $penyewa->tanggal_keluar ? \Carbon\Carbon::parse($penyewa->tanggal_keluar)->format('d F Y') : 'Masih aktif' }}
                                        </div>
                                    </div>
                                    <div>
                                        <span class="text-xs text-slate-400 font-medium">Uang Jaminan (Deposit)</span>
                                        <div class="text-sm font-extrabold text-blue-600 dark:text-blue-400 mt-0.5">
                                            Rp {{ number_format($penyewa->deposit, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Catatan -->
                            <div>
                                <h4 class="font-bold text-xs text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-3">Catatan Internal</h4>
                                <div class="bg-white dark:bg-slate-900 border-2 border-black dark:border-white p-5 rounded-none text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-bold">
                                    {{ $penyewa->catatan ?: 'Tidak ada catatan tambahan.' }}
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="flex justify-end gap-4 mt-10 pt-6 border-t-2 border-black dark:border-white">
                        @if(($penyewa->tagihan_count ?? 0) == 0)
                            <form action="{{ route('admin.penyewa.destroy', $penyewa->id) }}" method="POST" data-confirm="Apakah Anda yakin ingin menghapus permanen penyewa dan akun loginnya?" data-title="Hapus Penyewa" data-confirm-danger="true">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="admin-btn-danger !bg-red-600 hover:!bg-red-700">
                                    Hapus Penyewa
                                </button>
                            </form>
                        @endif
                        @if($penyewa->status === 'aktif')
                            <a href="{{ route('admin.penyewa.checkout.form', $penyewa->id) }}" class="admin-btn-danger inline-flex items-center justify-center">
                                Checkout Penyewa
                            </a>
                        @endif
                        <a href="{{ route('admin.penyewa.edit', $penyewa->id) }}" class="admin-btn-primary">
                            Edit Data
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
