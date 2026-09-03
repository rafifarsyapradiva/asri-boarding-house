<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Detail Pencatatan Pengeluaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">

                    <!-- Header -->
                    <div class="flex justify-between items-center mb-8 border-b-2 border-black dark:border-white pb-4">
                        <div>
                            <h3 class="text-lg font-black text-black dark:text-white uppercase tracking-wider">Detail Pengeluaran Operasional</h3>
                            <p class="admin-subtitle">Rincian pencatatan kas keluar untuk operasional, maintenance, atau utilitas kost.</p>
                        </div>
                        <a href="{{ route('admin.pengeluaran.index') }}" class="admin-btn-secondary !py-1 px-3 !text-xs">&larr; Kembali ke Daftar</a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Left: Bukti Nota -->
                        <div>
                            <h4 class="font-black text-xs text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-3">Bukti Nota / Kuitansi</h4>
                            @if($pengeluaran->bukti_nota)
                                <div class="relative group rounded-none overflow-hidden border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] bg-slate-50 dark:bg-slate-900">
                                    <img src="{{ asset('storage/' . $pengeluaran->bukti_nota) }}" alt="Bukti Nota {{ $pengeluaran->nama_pengeluaran }}" class="w-full h-72 object-cover transition-transform duration-300">
                                    <div class="absolute inset-0 flex items-center justify-center bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ asset('storage/' . $pengeluaran->bukti_nota) }}" target="_blank" rel="noopener noreferrer" class="admin-btn-secondary !py-2 !px-4">
                                            Buka Gambar Penuh
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="w-full h-72 rounded-none border-4 border-dashed border-slate-300 dark:border-slate-700 flex flex-col items-center justify-center p-6 text-slate-400 dark:text-slate-500">
                                    <span class="text-4xl mb-2">🧾</span>
                                    <p class="text-xs font-bold uppercase tracking-wider">Tidak ada bukti nota</p>
                                </div>
                            @endif
                        </div>

                        <!-- Right: Rincian Pengeluaran -->
                        <div class="space-y-6">
                            <div>
                                <h4 class="font-black text-xs text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-3">Informasi Transaksi</h4>
                                <div class="bg-slate-50 dark:bg-slate-900 border-4 border-black dark:border-white p-5 rounded-none space-y-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                                    
                                    <div class="flex justify-between items-start text-sm">
                                        <span class="text-slate-500 dark:text-slate-400 font-extrabold uppercase text-xs shrink-0">Nama Pengeluaran</span>
                                        <span class="font-black text-slate-800 dark:text-slate-100 text-right ml-4">{{ $pengeluaran->nama_pengeluaran }}</span>
                                    </div>
                                    
                                    <div class="flex justify-between items-center text-sm border-t-2 border-black dark:border-white pt-3">
                                        <span class="text-slate-500 dark:text-slate-400 font-extrabold uppercase text-xs">Kategori</span>
                                        <div>
                                            @php
                                                $badgeClass = match($pengeluaran->kategori) {
                                                    'maintenance' => 'admin-badge-warning',
                                                    'utilitas' => 'admin-badge-info',
                                                    'operasional' => 'admin-badge-success',
                                                    default => 'admin-badge-neutral',
                                                };
                                            @endphp
                                            <span class="admin-badge {{ $badgeClass }}">
                                                {{ strtoupper($pengeluaran->kategori) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-center text-sm border-t-2 border-black dark:border-white pt-3">
                                        <span class="text-slate-500 dark:text-slate-400 font-extrabold uppercase text-xs">Tanggal Transaksi</span>
                                        <span class="font-black text-slate-800 dark:text-slate-100">
                                            {{ $pengeluaran->tanggal_pengeluaran->format('d F Y') }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center text-sm border-t-2 border-black dark:border-white pt-3">
                                        <span class="text-slate-500 dark:text-slate-400 font-extrabold uppercase text-xs">Nominal Transaksi</span>
                                        <span class="font-black text-lg text-slate-900 dark:text-white">
                                            Rp {{ number_format($pengeluaran->nominal, 0, ',', '.') }}
                                        </span>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Keterangan Tambahan -->
                        <div class="md:col-span-2">
                            <h4 class="font-black text-xs text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-3">Keterangan Tambahan</h4>
                            <div class="bg-slate-50 dark:bg-slate-900 border-4 border-black dark:border-white p-5 rounded-none text-sm text-slate-700 dark:text-slate-300 leading-relaxed min-h-[80px] shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] font-bold">
                                {!! nl2br(e($pengeluaran->keterangan)) ?: '<span class="text-slate-400 dark:text-slate-500 italic font-semibold">Tidak ada keterangan tambahan yang dicantumkan.</span>' !!}
                            </div>
                        </div>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="flex justify-between items-center mt-10 pt-6 border-t-2 border-black dark:border-white">
                        <form action="{{ route('admin.pengeluaran.destroy', $pengeluaran->id) }}" method="POST" data-confirm="Apakah Anda yakin ingin menghapus pencatatan pengeluaran ini secara permanen?" data-title="Hapus Pengeluaran" data-confirm-danger="true">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="admin-btn-danger">
                                Hapus Catatan
                            </button>
                        </form>
                        <div class="flex gap-3">
                            <a href="{{ route('admin.pengeluaran.index') }}" class="admin-btn-secondary">
                                Kembali
                            </a>
                            <a href="{{ route('admin.pengeluaran.edit', $pengeluaran->id) }}" class="admin-btn-primary">
                                Edit Pengeluaran
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
