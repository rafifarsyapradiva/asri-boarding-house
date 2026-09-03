@php
    $isEdit = isset($review);
    $ratingOptions = [
        5 => '⭐⭐⭐⭐⭐ (5 Bintang)',
        4 => '⭐⭐⭐⭐ (4 Bintang)',
        3 => '⭐⭐⭐ (3 Bintang)',
        2 => '⭐⭐ (2 Bintang)',
        1 => '⭐ (1 Bintang)',
    ];
@endphp

<!-- Nama Pelanggan -->
<div>
    <label for="nama" class="admin-label">Nama Pelanggan <span class="text-red-500">*</span></label>
    <input type="text" 
           id="nama" 
           name="nama" 
           value="{{ old('nama', $review->nama ?? '') }}" 
           placeholder="Masukkan nama lengkap pelanggan" 
           class="admin-input @error('nama') border-red-500 focus:ring-red-100 @enderror"
           required>
    @error('nama')
        <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
    @enderror
</div>

<!-- Pekerjaan / Status -->
<div>
    <label for="pekerjaan" class="admin-label">Pekerjaan / Status</label>
    <input type="text" 
           id="pekerjaan" 
           name="pekerjaan" 
           value="{{ old('pekerjaan', $review->pekerjaan ?? '') }}" 
           placeholder="Contoh: Mahasiswa UNDIP, Karyawan Swasta" 
           class="admin-input @error('pekerjaan') border-red-500 focus:ring-red-100 @enderror">
    @error('pekerjaan')
        <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
    @enderror
</div>

<!-- Rating Bintang -->
<div>
    <label for="bintang" class="admin-label">Rating / Bintang <span class="text-red-500">*</span></label>
    <select id="bintang" name="bintang" class="admin-select @error('bintang') border-red-500 focus:ring-red-100 @enderror" required>
        <option value="" disabled {{ old('bintang', $review->bintang ?? '') === '' ? 'selected' : '' }}>Pilih Rating Bintang</option>
        @foreach($ratingOptions as $val => $label)
            <option value="{{ $val }}" {{ (string) old('bintang', $review->bintang ?? 5) === (string) $val ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
    @error('bintang')
        <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
    @enderror
</div>

<!-- Isi Ulasan -->
<div>
    <label for="ulasan" class="admin-label">Isi Review / Komentar <span class="text-red-500">*</span></label>
    <textarea id="ulasan" 
              name="ulasan" 
              placeholder="Masukkan ulasan atau testimoni pelanggan..." 
              class="admin-textarea @error('ulasan') border-red-500 focus:ring-red-100 @enderror !h-36"
              required>{{ old('ulasan', $review->ulasan ?? '') }}</textarea>
    @error('ulasan')
        <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
    @enderror
</div>

<!-- Foto Pelanggan -->
<div>
    @if($isEdit)
        <label class="admin-label">Foto Pelanggan Saat Ini</label>
        <div class="mb-3 flex items-center gap-3">
            @if($review->foto_url)
                <img src="{{ $review->foto_url }}" alt="Foto {{ $review->nama }}" class="w-16 h-16 object-cover rounded-none border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]">
            @else
                <div class="w-16 h-16 rounded-none bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white border-2 border-black dark:border-white font-black text-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]">
                    {{ $review->initials }}
                </div>
            @endif
            <div class="text-xs text-slate-500">
                {{ $review->foto_url ? 'Gambar ulasan telah diunggah.' : 'Belum ada gambar khusus untuk ulasan ini.' }}
            </div>
        </div>
    @endif

    <label for="foto" class="admin-label">{{ $isEdit ? 'Ganti Foto Pelanggan (Opsional)' : 'Foto Pelanggan (Opsional)' }}</label>
    <input type="file" id="foto" name="foto" class="admin-input @error('foto') border-red-500 focus:ring-red-100 @enderror" accept="image/*">
    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1.5">Mendukung format JPG, JPEG, PNG, atau WEBP (Maksimal 2MB). {{ $isEdit ? 'Biarkan kosong jika tidak ingin mengubah foto.' : '' }}</p>
    @error('foto')
        <span class="text-red-500 text-xs mt-1.5 block font-bold">⚠️ {{ $message }}</span>
    @enderror
</div>
