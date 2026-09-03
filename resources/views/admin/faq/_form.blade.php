@props([
    'faq' => null,
    'action',
    'method' => 'POST',
    'submitLabel' => 'Simpan FAQ',
    'cancelRoute' => null,
])

@php
    $resolvedCancelRoute = $cancelRoute ?? route('admin.faq.index');
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6" data-testid="faq-form">
    @csrf
    @if(strtoupper($method) !== 'POST')
        @method($method)
    @endif

    <!-- Pertanyaan -->
    <div>
        <label for="pertanyaan" class="admin-label">
            Pertanyaan <span class="text-red-500">*</span>
        </label>
        <input type="text" 
               id="pertanyaan" 
               name="pertanyaan" 
               value="{{ old('pertanyaan', $faq->pertanyaan ?? '') }}" 
               placeholder="Masukkan pertanyaan FAQ" 
               class="admin-input @error('pertanyaan') border-red-500 focus:ring-red-100 @enderror"
               required
               aria-required="true"
               @error('pertanyaan')
                   aria-invalid="true"
                   aria-describedby="pertanyaan-error"
               @enderror
               data-testid="faq-input-pertanyaan">
        @error('pertanyaan')
            <span id="pertanyaan-error" class="text-red-500 text-xs mt-1.5 block font-bold" role="alert">⚠️ {{ $message }}</span>
        @enderror
    </div>

    <!-- Jawaban -->
    <div>
        <label for="jawaban" class="admin-label">
            Jawaban <span class="text-red-500">*</span>
        </label>
        <textarea id="jawaban" 
                  name="jawaban" 
                  placeholder="Masukkan jawaban FAQ..." 
                  class="admin-textarea @error('jawaban') border-red-500 focus:ring-red-100 @enderror !h-36"
                  required
                  aria-required="true"
                  @error('jawaban')
                      aria-invalid="true"
                      aria-describedby="jawaban-error"
                  @enderror
                  data-testid="faq-input-jawaban">{{ old('jawaban', $faq->jawaban ?? '') }}</textarea>
        @error('jawaban')
            <span id="jawaban-error" class="text-red-500 text-xs mt-1.5 block font-bold" role="alert">⚠️ {{ $message }}</span>
        @enderror
    </div>

    <!-- Urutan Tampil -->
    <div>
        <label for="urutan" class="admin-label">
            Urutan Tampil <span class="text-red-500">*</span>
        </label>
        <input type="number" 
               id="urutan" 
               name="urutan" 
               value="{{ old('urutan', $faq->urutan ?? 0) }}" 
               min="0" 
               placeholder="Contoh: 1 (Urutan tampil paling atas)" 
               class="admin-input @error('urutan') border-red-500 focus:ring-red-100 @enderror"
               required
               aria-required="true"
               @error('urutan')
                   aria-invalid="true"
                   aria-describedby="urutan-error"
               @enderror
               data-testid="faq-input-urutan">
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1.5">
            Mengatur posisi tampil FAQ. Angka lebih kecil akan tampil lebih dulu.
        </p>
        @error('urutan')
            <span id="urutan-error" class="text-red-500 text-xs mt-1.5 block font-bold" role="alert">⚠️ {{ $message }}</span>
        @enderror
    </div>

    <!-- Status Aktif (Hidden default 0 + Checkbox 1 for correct POST handling on unchecking) -->
    <div class="flex items-center gap-3 py-2">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" 
               id="is_active" 
               name="is_active" 
               value="1" 
               {{ old('is_active', $faq->is_active ?? true) ? 'checked' : '' }} 
               class="admin-checkbox"
               data-testid="faq-checkbox-is-active">
        <label for="is_active" class="text-sm font-bold text-slate-700 dark:text-slate-200 cursor-pointer">
            Aktif / Tampilkan di Halaman Publik
        </label>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-700/50">
        <a href="{{ $resolvedCancelRoute }}" class="admin-btn-secondary" data-testid="faq-btn-cancel">
            Batal
        </a>
        <button type="submit" class="admin-btn-primary" data-testid="faq-btn-submit">
            {{ $submitLabel }}
        </button>
    </div>
</form>
