<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Buat Keluhan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">

                    <!-- Header -->
                    <div class="mb-8 border-b-4 border-black dark:border-white pb-4">
                        <h3 class="text-lg font-black text-slate-800 dark:text-slate-100 uppercase tracking-wide">
                            Laporkan Kerusakan / Masalah
                        </h3>
                        <p class="admin-subtitle">Silakan isi formulir di bawah ini dengan data yang valid untuk mempercepat penanganan.</p>
                    </div>

                    <!-- Form dengan Alpine.js State Management -->
                    <form action="{{ route('penyewa.keluhan.store') }}" 
                          method="POST" 
                          enctype="multipart/form-data" 
                          class="space-y-6"
                          x-data="imagePreviewHandler()">
                        @csrf

                        <!-- Judul -->
                        <div>
                            <label for="judul" class="admin-label">Judul Keluhan / Masalah</label>
                            <input type="text" name="judul" id="judul" value="{{ old('judul') }}" 
                                class="admin-input @error('judul') border-red-500 @enderror" 
                                placeholder="Contoh: Kran Kamar Mandi Bocor, Lampu Koridor Mati" required>
                            @error('judul')
                                <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kategori -->
                        <div>
                            <label for="kategori" class="admin-label">Kategori Keluhan</label>
                            <select name="kategori" id="kategori" class="admin-select w-full @error('kategori') border-red-500 @enderror" required>
                                <option value="" disabled {{ old('kategori') ? '' : 'selected' }}>Pilih Kategori</option>
                                @foreach([
                                    'kamar' => 'Masalah Kamar',
                                    'fasilitas_bersama' => 'Fasilitas Bersama',
                                    'kebersihan' => 'Kebersihan',
                                    'keamanan' => 'Keamanan',
                                    'lainnya' => 'Lainnya'
                                ] as $value => $label)
                                    <option value="{{ $value }}" {{ old('kategori') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kategori')
                                <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label for="deskripsi" class="admin-label">Deskripsi Masalah / Kerusakan</label>
                            <textarea name="deskripsi" id="deskripsi" rows="5" 
                                class="admin-input @error('deskripsi') border-red-500 @enderror" 
                                placeholder="Jelaskan detail masalah, lokasi tepat, dan kondisi kerusakan..." required>{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Foto Bukti -->
                        <div>
                            <label class="admin-label">Foto Bukti Kerusakan (Opsional, Maks 2MB)</label>
                            <div class="mt-2 flex items-center gap-4">
                                <label for="foto_bukti" class="cursor-pointer inline-flex items-center px-4 py-2.5 bg-slate-800 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 text-white text-xs font-extrabold border-2 border-black rounded-none shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[-1px] hover:translate-y-[-1px] hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-[0px] active:translate-y-[0px] active:shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] transition-all duration-150 uppercase tracking-wider">
                                    Pilih Gambar Bukti
                                </label>
                                <input type="file" name="foto_bukti" id="foto_bukti" class="hidden" accept="image/*" @change="handleFileSelect($event)">
                                <span class="text-xs text-slate-500 dark:text-slate-400 font-bold" x-text="fileName"></span>
                            </div>
                            
                            <!-- Client Error Message (Menggantikan Alert) -->
                            <p x-show="errorMessage" x-text="errorMessage" class="text-red-500 text-xs mt-1 font-semibold" style="display: none;"></p>
                            
                            @error('foto_bukti')
                                <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror

                            <!-- Image Preview Area -->
                            <div x-show="previewUrl" class="mt-4" style="display: none;">
                                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-2">Pratinjau Foto:</p>
                                <div class="relative w-full max-w-sm aspect-[4/3] border-4 border-black dark:border-white rounded-none overflow-hidden bg-slate-50/50 dark:bg-slate-900/30 shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff]">
                                    <img :src="previewUrl" alt="Preview" class="w-full h-full object-cover">
                                    <button type="button" @click="removeImage()" class="absolute top-2 right-2 p-1 bg-red-500 hover:bg-red-600 text-white border-2 border-black rounded-none shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:translate-x-[-1px] hover:translate-y-[-1px] hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all duration-150 cursor-pointer">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-6 border-t-4 border-black dark:border-white">
                            <a href="{{ route('penyewa.keluhan.index') }}" class="admin-btn-secondary">
                                Batal
                            </a>
                            <button type="submit" class="admin-btn-primary">
                                Kirim Laporan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js Component Script -->
    <script>
        function imagePreviewHandler() {
            return {
                fileName: 'Tidak ada berkas dipilih',
                previewUrl: null,
                errorMessage: '',
                handleFileSelect(event) {
                    const file = event.target.files[0];
                    this.errorMessage = '';
                    
                    if (!file) {
                        this.removeImage();
                        return;
                    }

                    if (file.size > 2 * 1024 * 1024) {
                        this.errorMessage = 'Ukuran gambar maksimal adalah 2MB. Silakan pilih gambar yang lebih kecil.';
                        this.removeImage();
                        return;
                    }

                    this.fileName = file.name;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.previewUrl = e.target.result;
                    };
                    reader.readAsDataURL(file);
                },
                removeImage() {
                    const input = document.getElementById('foto_bukti');
                    if (input) input.value = '';
                    this.fileName = 'Tidak ada berkas dipilih';
                    this.previewUrl = null;
                }
            };
        }
    </script>
</x-app-layout>
