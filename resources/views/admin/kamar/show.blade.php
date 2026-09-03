<x-app-layout>
    <x-toast />
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Detail Kamar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">

                    <!-- Header -->
                    <div class="flex justify-between items-center mb-8 border-b border-slate-100 dark:border-slate-700/50 pb-4">
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100">Detail Unit Kamar</h3>
                            <p class="admin-subtitle">Informasi spesifikasi teknis, fasilitas terpasang, dan status ketersediaan Kamar {{ $kamar->nomor_kamar }}.</p>
                        </div>
                        <a href="{{ route('admin.kamar.index') }}" class="text-sm font-bold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition duration-150">&larr; Kembali ke Daftar</a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Left: Foto Kamar -->
                        <div>
                            <img src="{{ $kamar->foto_url }}" alt="Foto Kamar {{ $kamar->nomor_kamar }}" class="w-full h-64 object-cover rounded-none shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] border-4 border-black dark:border-white">
                        </div>

                        <!-- Right: Spesifikasi & Status -->
                        <div class="space-y-6">
                            <div>
                                <h4 class="font-bold text-xs text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-3">Spesifikasi Unit</h4>
                                <div class="bg-slate-50/50 dark:bg-slate-900/30 border-2 border-black dark:border-white p-5 rounded-none space-y-4 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]">
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-slate-400 font-medium">Nomor Kamar</span>
                                        <span class="font-bold text-slate-800 dark:text-slate-100">Kamar {{ $kamar->nomor_kamar }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-slate-400 font-medium">Lantai</span>
                                        <span class="font-bold text-slate-800 dark:text-slate-100">Lantai {{ $kamar->lantai }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-slate-400 font-medium">Tipe Kamar</span>
                                        <span class="font-bold text-slate-800 dark:text-slate-100 capitalize">{{ $kamar->tipe }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-slate-400 font-medium">Luas Kamar</span>
                                        <span class="font-bold text-slate-800 dark:text-slate-100">{{ $kamar->luas_m2 }} m²</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-slate-400 font-medium">Harga per Bulan</span>
                                        <span class="font-extrabold text-blue-600 dark:text-blue-400">Rp {{ number_format($kamar->harga_bulan, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-slate-400 font-medium">Status Kamar</span>
                                        <div>
                                            @if($kamar->status === 'tersedia')
                                                <span class="admin-badge admin-badge-success">Tersedia</span>
                                            @elseif($kamar->status === 'terisi')
                                                <span class="admin-badge admin-badge-info">Terisi</span>
                                            @else
                                                <span class="admin-badge admin-badge-warning">Maintenance</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($kamar->penyewaAktif)
                                        <div class="flex justify-between items-center text-sm border-t border-slate-200 dark:border-slate-700 pt-4 mt-2">
                                            <span class="text-slate-400 font-medium">Penyewa Aktif</span>
                                            <span class="font-bold">
                                                <a href="{{ route('admin.penyewa.show', $kamar->penyewaAktif->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline font-black">
                                                    {{ $kamar->penyewaAktif?->user?->nama ?? 'N/A' }}
                                                </a>
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Fasilitas -->
                            <div>
                                <h4 class="font-bold text-xs text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-3">Fasilitas Kamar</h4>
                                <div class="flex flex-wrap gap-2">
                                    @forelse($kamar->fasilitas as $f)
                                        <span class="px-3 py-1 text-xs font-black bg-blue-100 dark:bg-slate-800 text-black dark:text-white border-2 border-black dark:border-white rounded-none shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">
                                            {{ $f->nama }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-slate-400 italic">Belum ada fasilitas khusus terpasang.</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="md:col-span-2">
                            <h4 class="font-bold text-xs text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-3">Deskripsi Kamar</h4>
                            <div class="bg-white dark:bg-slate-900 border-2 border-black dark:border-white p-5 rounded-none text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-bold">
                                {{ $kamar->deskripsi ?: 'Tidak ada deskripsi tambahan.' }}
                            </div>
                        </div>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="flex justify-end gap-4 mt-10 pt-6 border-t-2 border-black dark:border-white">
                        <form action="{{ route('admin.kamar.destroy', $kamar->id) }}" method="POST" data-confirm="Apakah Anda yakin ingin menghapus kamar ini secara permanen?" data-title="Hapus Kamar">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="admin-btn-danger">
                                Hapus Kamar
                            </button>
                        </form>
                        <a href="{{ route('admin.kamar.edit', $kamar->id) }}" class="admin-btn-primary">
                            Edit Kamar
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
