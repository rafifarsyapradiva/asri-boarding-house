@csrf

<!-- Judul -->
<div>
    <label for="judul" class="admin-label">Judul Foto <span class="text-red-500">*</span></label>
    <input type="text" 
           id="judul" 
           name="judul" 
           value="{{ old('judul', $gallery->judul ?? '') }}" 
           placeholder="Contoh: Suasana Kamar VIP" 
           class="admin-input @error('judul') !border-red-500 @enderror"
           data-testid="input-judul"
           required>
    @error('judul')
        <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
    @enderror
</div>

<!-- Deskripsi -->
<div>
    <label for="deskripsi" class="admin-label">Deskripsi / Keterangan</label>
    <textarea id="deskripsi" 
              name="deskripsi" 
              placeholder="Masukkan deskripsi singkat tentang foto ini..." 
              class="admin-textarea @error('deskripsi') !border-red-500 @enderror !h-24"
              data-testid="input-deskripsi">{{ old('deskripsi', $gallery->deskripsi ?? '') }}</textarea>
    @error('deskripsi')
        <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
    @enderror
</div>

<!-- Preview Foto Saat Ini (Jika Mode Edit) -->
@if(isset($gallery) && $gallery->foto_url)
<div>
    <label class="admin-label">Foto Saat Ini</label>
    <div class="mt-2 mb-4">
        <img src="{{ $gallery->foto_url }}" 
             alt="{{ $gallery->judul }}" 
             class="w-48 h-32 object-cover rounded-none border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]"
             data-testid="img-current-preview">
    </div>
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Foto Upload -->
    <div>
        <label for="foto" class="admin-label">
            {{ isset($gallery) ? 'Ganti Foto (File)' : 'Unggah Foto (File)' }} 
            <span class="text-xs text-slate-400 font-normal">(Opsional jika URL diisi)</span>
        </label>
        <input type="file" 
               id="foto" 
               name="foto" 
               class="admin-input @error('foto') !border-red-500 @enderror" 
               accept="image/*"
               data-testid="input-foto-file">
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1.5">Format: JPG, PNG, WEBP. Maksimal 2MB.</p>
        @error('foto')
            <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
        @enderror
    </div>

    <!-- Foto URL -->
    <div>
        <label for="foto_url" class="admin-label">
            Atau {{ isset($gallery) ? 'Ganti dengan' : 'Gunakan' }} URL Foto 
            <span class="text-xs text-slate-400 font-normal">(Opsional)</span>
        </label>
        <input type="url" 
               id="foto_url" 
               name="foto_url" 
               value="{{ old('foto_url', (isset($gallery) && str_starts_with($gallery->foto ?? '', 'http')) ? $gallery->foto : '') }}" 
               placeholder="https://example.com/image.jpg" 
               class="admin-input @error('foto_url') !border-red-500 @enderror"
               data-testid="input-foto-url">
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1.5">Gunakan link gambar eksternal (misal dari Unsplash).</p>
        @error('foto_url')
            <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
        @enderror
    </div>
</div>

<!-- Urutan -->
<div>
    <label for="urutan" class="admin-label">Urutan Tampil <span class="text-red-500">*</span></label>
    <input type="number" 
           id="urutan" 
           name="urutan" 
           value="{{ old('urutan', $gallery->urutan ?? 0) }}" 
           min="0" 
           placeholder="Contoh: 1 (Urutan tampil paling kiri/awal)" 
           class="admin-input @error('urutan') !border-red-500 @enderror"
           data-testid="input-urutan"
           required>
    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1.5">Mengatur urutan foto. Angka lebih kecil akan tampil lebih dulu.</p>
    @error('urutan')
        <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
    @enderror
</div>

<!-- Status Aktif -->
<div class="flex items-center gap-3 py-2">
    <input type="checkbox" 
           id="is_active" 
           name="is_active" 
           value="1" 
           @checked(old('is_active', $gallery->is_active ?? true)) 
           class="admin-checkbox"
           data-testid="checkbox-is-active">
    <label for="is_active" class="text-sm font-bold text-slate-700 dark:text-slate-200 cursor-pointer">
        Aktif / Tampilkan di Landing Page
    </label>
</div>

<!-- Action Buttons -->
<div class="flex items-center justify-end gap-3 pt-4 border-t-4 border-black dark:border-white">
    <a href="{{ route('admin.gallery.index') }}" class="admin-btn-secondary" data-testid="btn-cancel">
        Batal
    </a>
    <button type="submit" class="admin-btn-primary" data-testid="btn-submit">
        {{ isset($gallery) ? 'Simpan Perubahan' : 'Simpan Galeri' }}
    </button>
</div>
