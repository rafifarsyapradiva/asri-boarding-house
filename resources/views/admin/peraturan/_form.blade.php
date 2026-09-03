@csrf

<!-- Judul Peraturan -->
<div>
    <label for="judul" class="admin-label">Judul Peraturan <span class="text-red-500">*</span></label>
    <input type="text" id="judul" name="judul" 
           value="{{ old('judul', $peraturan->judul ?? '') }}" 
           placeholder="Contoh: Kebersihan & Kerapian" 
           class="admin-input @error('judul') !border-red-500 @enderror" required>
    @error('judul')
        <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
    @enderror
</div>

<!-- Ikon Peraturan -->
<div>
    <label for="ikon" class="admin-label">Ikon / Visual Indikator <span class="text-red-500">*</span></label>
    <select id="ikon" name="ikon" class="admin-select @error('ikon') !border-red-500 @enderror" required>
        <option value="" disabled {{ old('ikon', $peraturan->ikon ?? '') == '' ? 'selected' : '' }}>Pilih Ikon</option>
        @foreach($icons as $key => $label)
            <option value="{{ $key }}" {{ old('ikon', $peraturan->ikon ?? '') == $key ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
    @error('ikon')
        <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
    @enderror
</div>

<!-- Urutan Tampilan -->
<div>
    <label for="urutan" class="admin-label">Nomor Urutan Tampilan <span class="text-red-500">*</span></label>
    <input type="number" id="urutan" name="urutan" 
           value="{{ old('urutan', $peraturan->urutan ?? 1) }}" 
           min="1" placeholder="Masukkan nomor urut (misal: 1, 2, 3)" 
           class="admin-input @error('urutan') !border-red-500 @enderror" required>
    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1.5">Digunakan untuk menyortir urutan kartu aturan di halaman penyewa.</p>
    @error('urutan')
        <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
    @enderror
</div>

<!-- Deskripsi Peraturan -->
<div>
    <label for="deskripsi" class="admin-label">Deskripsi Peraturan <span class="text-red-500">*</span></label>
    <textarea id="deskripsi" name="deskripsi" 
              placeholder="Masukkan deskripsi detail atau penjabaran tata tertib..." 
              class="admin-textarea @error('deskripsi') !border-red-500 @enderror !h-36" required>{{ old('deskripsi', $peraturan->deskripsi ?? '') }}</textarea>
    @error('deskripsi')
        <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
    @enderror
</div>

<!-- Action Buttons -->
<div class="flex items-center justify-end gap-3 pt-4 border-t-4 border-black dark:border-white">
    <a href="{{ route('admin.peraturan.index') }}" class="admin-btn-secondary">
        Batal
    </a>
    <button type="submit" class="admin-btn-primary">
        {{ $submitLabel ?? (isset($peraturan) ? 'Perbarui Peraturan' : 'Simpan Peraturan') }}
    </button>
</div>
