@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Nama Pengeluaran -->
    <div class="md:col-span-2">
        <label for="nama_pengeluaran" class="admin-label">Nama Pengeluaran <span class="text-red-500">*</span></label>
        <input id="nama_pengeluaran" 
               name="nama_pengeluaran" 
               type="text" 
               class="admin-input @error('nama_pengeluaran') !border-red-500 @enderror" 
               value="{{ old('nama_pengeluaran', $pengeluaran->nama_pengeluaran ?? '') }}" 
               placeholder="Contoh: Perbaikan Pintu Kamar 102, Pembayaran Listrik" 
               required 
               autofocus />
        @error('nama_pengeluaran')
            <p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Kategori -->
    <div>
        <label for="kategori" class="admin-label">Kategori <span class="text-red-500">*</span></label>
        <select id="kategori" name="kategori" class="admin-select @error('kategori') !border-red-500 @enderror" required>
            <option value="" disabled {{ old('kategori', $pengeluaran->kategori ?? '') == '' ? 'selected' : '' }}>-- Pilih Kategori --</option>
            @php
                $categoryOptions = $categories ?? [
                    'maintenance' => 'Maintenance',
                    'utilitas' => 'Utilitas',
                    'operasional' => 'Operasional',
                    'lainnya' => 'Lainnya'
                ];
            @endphp
            @foreach($categoryOptions as $key => $label)
                <option value="{{ $key }}" {{ old('kategori', $pengeluaran->kategori ?? '') == $key ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('kategori')
            <p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Nominal -->
    <div>
        <label for="nominal_display" class="admin-label">Nominal Pengeluaran (Rp) <span class="text-red-500">*</span></label>
        @php
            $rawNominal = old('nominal', isset($pengeluaran) ? (int) $pengeluaran->nominal : '');
        @endphp
        <input type="hidden" name="nominal" id="nominal" value="{{ $rawNominal }}">
        <input id="nominal_display" 
               type="text" 
               class="admin-input rupiah-input @error('nominal') !border-red-500 @enderror" 
               data-target="nominal" 
               value="{{ $rawNominal }}" 
               placeholder="Contoh: 150.000" 
               required />
        @error('nominal')
            <p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Tanggal Pengeluaran -->
    <div>
        <label for="tanggal_pengeluaran" class="admin-label">Tanggal Pengeluaran <span class="text-red-500">*</span></label>
        <input id="tanggal_pengeluaran" 
               name="tanggal_pengeluaran" 
               type="date" 
               class="admin-input @error('tanggal_pengeluaran') !border-red-500 @enderror" 
               value="{{ old('tanggal_pengeluaran', isset($pengeluaran) ? $pengeluaran->tanggal_pengeluaran->format('Y-m-d') : now()->toDateString()) }}" 
               required />
        @error('tanggal_pengeluaran')
            <p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>
        @enderror
    </div>

    <!-- Upload Bukti Nota -->
    <div class="md:col-span-2">
        <label for="bukti_nota" class="admin-label">Foto Bukti Nota (Maksimal 2MB - JPG/JPEG/PNG)</label>
        <input id="bukti_nota" 
               name="bukti_nota" 
               type="file" 
               class="w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-none file:border-2 file:border-black dark:file:border-white file:text-xs file:font-black file:bg-yellow-300 file:text-black hover:file:bg-yellow-400 file:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:file:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] hover:file:translate-y-[-1px] active:file:translate-y-0 transition-all cursor-pointer @error('bukti_nota') !border-red-500 @enderror" 
               accept="image/jpeg,image/png,image/jpg" />
        @error('bukti_nota')
            <p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>
        @enderror

        @if(isset($pengeluaran) && $pengeluaran->bukti_nota)
            <div class="mt-3">
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-1 font-bold">Bukti nota saat ini:</p>
                <a href="{{ asset('storage/' . $pengeluaran->bukti_nota) }}" target="_blank" rel="noopener noreferrer" class="inline-block relative group border-2 border-black dark:border-white rounded-none shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] hover:translate-y-[-1px] transition-all cursor-pointer">
                    <img src="{{ asset('storage/' . $pengeluaran->bukti_nota) }}" alt="Bukti Nota" class="w-32 h-20 object-cover rounded-none hover:opacity-90">
                    <div class="absolute inset-0 flex items-center justify-center bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity">
                        <span class="text-[10px] font-black text-white uppercase tracking-wider">Perbesar</span>
                    </div>
                </a>
            </div>
        @endif
    </div>

    <!-- Keterangan -->
    <div class="md:col-span-2">
        <label for="keterangan" class="admin-label">Keterangan Tambahan (Opsional)</label>
        <textarea id="keterangan" 
                  name="keterangan" 
                  class="admin-textarea @error('keterangan') !border-red-500 @enderror" 
                  placeholder="Tulis rincian lebih detail jika diperlukan...">{{ old('keterangan', $pengeluaran->keterangan ?? '') }}</textarea>
        @error('keterangan')
            <p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>
