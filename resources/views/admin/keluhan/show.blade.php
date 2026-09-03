<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Tanggapi Keluhan') }}
        </h2>
    </x-slot>

    <!-- Include Toast Notifications -->
    <x-toast />

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">

                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 pb-6 border-b border-slate-100 dark:border-slate-800 gap-4">
                        <div>
                            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                                Detail Laporan #{{ $keluhan->id }}
                            </span>
                            <h3 class="text-xl font-extrabold text-slate-800 dark:text-slate-100 mt-1">
                                {{ $keluhan->judul }}
                            </h3>
                        </div>
                        <div>
                            <span class="admin-badge {{ $keluhan->status_badge_class }} text-sm py-1 px-3">
                                {{ strtoupper($keluhan->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                        <!-- Left Side: Detail & Description -->
                        <div class="md:col-span-2 space-y-6">
                            <div>
                                <h4 class="text-xs font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">
                                    Informasi Pelapor & Keluhan
                                </h4>
                                <div class="bg-slate-50 dark:bg-slate-900 rounded-none p-5 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] grid grid-cols-2 gap-4 text-sm font-bold">
                                    <div>
                                        <p class="text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase">Nama Penyewa</p>
                                        <p class="text-slate-900 dark:text-slate-100 mt-0.5">
                                            {{ $keluhan->penyewa?->user?->nama ?? '-' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase">Kamar</p>
                                        <p class="text-slate-900 dark:text-slate-100 mt-0.5">
                                            Kamar {{ $keluhan->penyewa?->kamar?->nomor_kamar ?? '-' }} 
                                            @if($keluhan->penyewa?->kamar?->lantai)
                                                (Lantai {{ $keluhan->penyewa->kamar->lantai }})
                                            @endif
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase">Kategori</p>
                                        <p class="text-slate-900 dark:text-slate-100 mt-0.5 capitalize">
                                            {{ $keluhan->kategori_label }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase">Tanggal Lapor</p>
                                        <p class="text-slate-900 dark:text-slate-100 mt-0.5">
                                            {{ $keluhan->created_at?->format('d M Y H:i') ?? '-' }}
                                        </p>
                                    </div>
                                    @if($keluhan->tanggal_selesai)
                                        <div class="col-span-2 border-t-2 border-black dark:border-white pt-3">
                                            <p class="text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase">Tanggal Selesai</p>
                                            <p class="text-slate-900 dark:text-slate-100 mt-0.5">
                                                {{ $keluhan->tanggal_selesai->format('d M Y H:i') }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <h4 class="text-xs font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">
                                    Deskripsi Keluhan
                                </h4>
                                <div class="bg-slate-50 dark:bg-slate-900 rounded-none p-5 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100 whitespace-pre-line leading-relaxed">
                                        {{ $keluhan->deskripsi }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side: Foto Bukti -->
                        <div class="space-y-6">
                            <h4 class="text-xs font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                                Foto Bukti Kerusakan
                            </h4>
                            @if($keluhan->foto_bukti)
                                <div class="border-4 border-black dark:border-white rounded-none overflow-hidden shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] bg-slate-50 dark:bg-slate-900">
                                    <a href="{{ asset('storage/' . $keluhan->foto_bukti) }}" target="_blank" rel="noopener noreferrer" title="Klik untuk memperbesar">
                                        <img src="{{ asset('storage/' . $keluhan->foto_bukti) }}" alt="Bukti Masalah" class="w-full h-auto object-cover max-h-72 hover:scale-105 transition duration-300">
                                    </a>
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center p-8 border-4 border-dashed border-black dark:border-white rounded-none text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-900">
                                    <svg class="w-8 h-8 mb-2 text-black dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-xs font-extrabold text-black dark:text-white">Tidak ada foto bukti</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Form Tanggapan Admin -->
                    <div class="mt-8 pt-8 border-t border-slate-100 dark:border-slate-800">
                        <h4 class="text-lg font-extrabold text-slate-800 dark:text-slate-100 mb-4">
                            Berikan Tanggapan & Perbarui Status
                        </h4>

                        <form action="{{ route('admin.keluhan.update', $keluhan) }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <!-- Status Dropdown -->
                            <div class="max-w-xs">
                                <label for="status" class="admin-label">Status Tindak Lanjut</label>
                                <select name="status" id="status" class="admin-select w-full @error('status') border-red-500 @enderror" required>
                                    @if($keluhan->status === 'pending')
                                        <option value="pending" {{ old('status', $keluhan->status) === 'pending' ? 'selected' : '' }}>PENDING</option>
                                    @endif
                                    <option value="diproses" {{ old('status', $keluhan->status) === 'diproses' ? 'selected' : '' }}>DIPROSES</option>
                                    <option value="selesai" {{ old('status', $keluhan->status) === 'selesai' ? 'selected' : '' }}>SELESAI</option>
                                </select>
                                @error('status')
                                    <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tanggapan Admin Textarea -->
                            <div>
                                <label for="tanggapan_admin" class="admin-label">Tanggapan / Respon Pengelola</label>
                                <textarea name="tanggapan_admin" id="tanggapan_admin" rows="4" 
                                    class="admin-input @error('tanggapan_admin') border-red-500 @enderror" 
                                    placeholder="Jelaskan tindakan yang akan atau telah dilakukan (contoh: kran toilet rusak sedang diproses teknisi atau telah diperbaiki)..." required>{{ old('tanggapan_admin', $keluhan->tanggapan_admin) }}</textarea>
                                @error('tanggapan_admin')
                                    <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                                <a href="{{ route('admin.keluhan.index') }}" class="admin-btn-secondary">
                                    Batal
                                </a>
                                <button type="submit" class="admin-btn-primary">
                                    Simpan Tanggapan
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
