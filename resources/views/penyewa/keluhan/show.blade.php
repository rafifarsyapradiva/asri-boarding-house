<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Detail Keluhan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">

                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 pb-6 border-b-4 border-black dark:border-white gap-4">
                        <div>
                            <span class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                                Keluhan #{{ $keluhan->id }}
                            </span>
                            <h3 class="text-xl font-black text-slate-800 dark:text-slate-100 mt-1 uppercase tracking-wide">
                                {{ $keluhan->judul ?? '-' }}
                            </h3>
                        </div>
                        <div>
                            <span class="admin-badge {{ $keluhan->status_badge_class ?? 'admin-badge-neutral' }} text-sm py-1.5 px-4 font-black">
                                {{ strtoupper($keluhan->status ?? 'N/A') }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Left Side: Detail & Description -->
                        <div class="md:col-span-2 space-y-6">
                            <div>
                                <h4 class="text-xs font-black text-slate-900 dark:text-slate-200 uppercase tracking-wider mb-2">
                                    Informasi Keluhan
                                </h4>
                                <div class="bg-white dark:bg-slate-900 rounded-none p-5 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] grid grid-cols-2 gap-4 text-sm font-semibold">
                                    <div>
                                        <p class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wide">Kategori</p>
                                        <p class="text-slate-800 dark:text-slate-200 mt-0.5 capitalize font-extrabold">
                                            {{ $keluhan->kategori_label ?? '-' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wide">Tanggal Lapor</p>
                                        <p class="text-slate-800 dark:text-slate-200 mt-0.5 font-extrabold">
                                            {{ $keluhan->created_at?->format('d M Y H:i') ?? '-' }}
                                        </p>
                                    </div>
                                    @if($keluhan->tanggal_selesai)
                                        <div class="col-span-2 border-t-2 border-black dark:border-white pt-3">
                                            <p class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wide">Tanggal Selesai</p>
                                            <p class="text-slate-800 dark:text-slate-200 mt-0.5 font-extrabold">
                                                {{ $keluhan->tanggal_selesai?->format('d M Y H:i') ?? '-' }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <h4 class="text-xs font-black text-slate-900 dark:text-slate-200 uppercase tracking-wider mb-2">
                                    Deskripsi Kerusakan / Keluhan
                                </h4>
                                <div class="bg-white dark:bg-slate-900 rounded-none p-5 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff]">
                                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200 whitespace-pre-line leading-relaxed">
                                        {{ $keluhan->deskripsi ?? 'Tidak ada deskripsi.' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side: Foto Bukti -->
                        <div class="space-y-6">
                            <h4 class="text-xs font-black text-slate-900 dark:text-slate-200 uppercase tracking-wider">
                                Foto Bukti
                            </h4>
                            @if($keluhan->foto_bukti)
                                <div class="border-4 border-black dark:border-white rounded-none overflow-hidden shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] bg-slate-50 dark:bg-slate-900/30">
                                    <a href="{{ asset('storage/' . $keluhan->foto_bukti) }}" target="_blank" rel="noopener noreferrer" title="Klik untuk memperbesar">
                                        <img src="{{ asset('storage/' . $keluhan->foto_bukti) }}" alt="Bukti Masalah" class="w-full aspect-[4/3] object-cover max-h-72 hover:scale-105 transition duration-300">
                                    </a>
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center p-8 border-4 border-dashed border-black dark:border-white rounded-none text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-900/10 shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff]">
                                    <svg class="w-8 h-8 mb-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-xs font-black">Tidak ada foto bukti</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Tanggapan Admin/Pengelola -->
                    <div class="mt-8 pt-8 border-t-4 border-black dark:border-white">
                        <h4 class="text-xs font-black text-slate-900 dark:text-slate-200 uppercase tracking-wider mb-3">
                            Tanggapan Pengelola / Admin
                        </h4>
                        @if($keluhan->tanggapan_admin)
                            <div class="p-5 bg-cyan-100 dark:bg-cyan-950 border-4 border-black dark:border-white text-black dark:text-white rounded-none shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff]">
                                <div class="flex items-start gap-3">
                                    <span class="text-xl shrink-0">🛠</span>
                                    <div class="flex-1">
                                        <p class="text-xs font-black uppercase opacity-75 mb-1 text-black dark:text-slate-200 tracking-wide">Respon Pengelola Kost:</p>
                                        <p class="text-sm font-bold leading-relaxed text-black dark:text-slate-100">
                                            {{ $keluhan->tanggapan_admin }}
                                        </p>
                                        <p class="text-[10px] font-extrabold text-slate-600 dark:text-slate-400 mt-2 uppercase tracking-wider">
                                            Ditanggapi pada {{ $keluhan->updated_at?->format('d M Y H:i') ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="p-5 bg-yellow-100 dark:bg-yellow-950/40 border-4 border-black dark:border-white text-black dark:text-white rounded-none shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff]">
                                <div class="flex items-center gap-3">
                                    <span class="text-xl shrink-0">⏳</span>
                                    <p class="text-sm font-extrabold">Keluhan belum ditanggapi oleh pengelola. Laporan Anda sedang dalam antrean.</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Footer Action -->
                    <div class="flex items-center justify-end mt-8 pt-6 border-t-4 border-black dark:border-white">
                        <a href="{{ route('penyewa.keluhan.index') }}" class="admin-btn-secondary">
                            Kembali ke Daftar
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
