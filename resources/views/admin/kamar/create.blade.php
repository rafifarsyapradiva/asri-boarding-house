<x-app-layout>
    <x-toast />
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Tambah Kamar Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">
                    
                    <div class="mb-8 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100">Form Tambah Kamar</h3>
                            <p class="admin-subtitle">Masukkan detail data unit kamar kost beserta fasilitas pendukungnya.</p>
                        </div>
                        <a href="{{ route('admin.kamar.index') }}" class="text-sm font-bold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition duration-150">&larr; Kembali</a>
                    </div>

                    <!-- Display Validation Errors -->
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-400 dark:bg-red-950 text-black dark:text-red-200 border-4 border-black dark:border-white rounded-none shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff]">
                            <div class="font-black text-sm mb-1 uppercase tracking-wider">⚠️ Terjadi kesalahan input:</div>
                            <ul class="list-disc pl-5 text-xs font-bold space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.kamar.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf

                        <!-- Section 1: Informasi Unit Kamar -->
                        <div class="border-b border-slate-100 dark:border-slate-700/50 pb-6">
                            <h4 class="admin-section-title">1. Spesifikasi Kamar</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nomor Kamar -->
                                <div>
                                    <label for="nomor_kamar" class="admin-label">Nomor Kamar</label>
                                    <input id="nomor_kamar" name="nomor_kamar" type="text" class="admin-input" value="{{ old('nomor_kamar') }}" placeholder="Contoh: 101, 102" required autofocus />
                                </div>
                                <!-- Lantai -->
                                <div>
                                    <label for="lantai" class="admin-label">Lantai</label>
                                    <input id="lantai" name="lantai" type="number" class="admin-input" value="{{ old('lantai', 1) }}" min="1" required />
                                </div>
                                <!-- Tipe Kamar -->
                                <div>
                                    <label for="tipe" class="admin-label">Tipe Kamar</label>
                                    <select id="tipe" name="tipe" class="admin-select" required>
                                        <option value="standar" {{ old('tipe') == 'standar' ? 'selected' : '' }}>Standar</option>
                                        <option value="deluxe" {{ old('tipe') == 'deluxe' ? 'selected' : '' }}>Deluxe</option>
                                        <option value="vip" {{ old('tipe') == 'vip' ? 'selected' : '' }}>VIP</option>
                                    </select>
                                </div>
                                <!-- Luas Kamar (m2) -->
                                <div>
                                    <label for="luas_m2" class="admin-label">Luas Kamar (m²)</label>
                                    <input id="luas_m2" name="luas_m2" type="number" step="0.01" class="admin-input" value="{{ old('luas_m2') }}" placeholder="Contoh: 12.5" required />
                                </div>
                                <!-- Harga/Bulan -->
                                <div>
                                    <label for="harga_bulan_display" class="admin-label">Harga per Bulan (Rp)</label>
                                    <input type="hidden" name="harga_bulan" id="harga_bulan" value="{{ old('harga_bulan') }}">
                                    <input id="harga_bulan_display" type="text" class="admin-input rupiah-input" data-target="harga_bulan" value="{{ old('harga_bulan') }}" placeholder="Contoh: 750.000" required />
                                </div>
                                <!-- Status Awal -->
                                <div>
                                    <label for="status" class="admin-label">Status Awal Kamar</label>
                                    <select id="status" name="status" class="admin-select">
                                        <option value="tersedia" {{ old('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                        <option value="terisi" {{ old('status') == 'terisi' ? 'selected' : '' }}>Terisi</option>
                                        <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Foto & Deskripsi -->
                        <div class="border-b border-slate-100 dark:border-slate-700/50 pb-6">
                            <h4 class="admin-section-title">2. Tampilan & Deskripsi</h4>
                            <div class="grid grid-cols-1 gap-6">
                                <!-- Upload Foto -->
                                <div>
                                    <label for="foto" class="admin-label">Foto Kamar (Maksimal 2MB - JPG/JPEG/PNG/WEBP)</label>
                                    <input id="foto" name="foto" type="file" class="w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-none file:border-2 file:border-black dark:file:border-white file:text-xs file:font-black file:bg-yellow-400 file:text-black hover:file:bg-yellow-500 cursor-pointer" accept="image/*" />
                                </div>
                                <!-- Deskripsi -->
                                <div>
                                    <label for="deskripsi" class="admin-label">Deskripsi Kamar</label>
                                    <textarea id="deskripsi" name="deskripsi" class="admin-textarea" placeholder="Detail kondisi kamar, fasilitas spesifik, dll...">{{ old('deskripsi') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Fasilitas Kamar -->
                        <div class="pb-6">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                <div>
                                    <h4 class="admin-section-title !mb-0">3. Fasilitas Terpasang</h4>
                                    <p class="text-xs text-slate-400 dark:text-slate-500">Centang fasilitas yang terpasang di kamar ini.</p>
                                </div>
                                <button type="button" id="btn-add-facility" class="admin-btn-primary !py-1.5 !px-3 text-xs">
                                    + Tambah Fasilitas Baru
                                </button>
                            </div>
                            
                            <div id="facilities-list-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                @include('admin.kamar.partials.facilities-checkboxes', ['fasilitas' => $fasilitas, 'kamar' => null])
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-4 mt-8">
                            <a href="{{ route('admin.kamar.index') }}" class="admin-btn-secondary">
                                Batal
                            </a>
                            <button type="submit" class="admin-btn-primary">
                                Simpan Kamar
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Reusable Partial Modal & Script -->
    @include('admin.kamar.partials.facility-modal', ['kamar' => null])
</x-app-layout>
