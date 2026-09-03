@php
    $selectedIds = old('fasilitas', isset($kamar) && $kamar->relationLoaded('fasilitas') ? $kamar->fasilitas->pluck('id')->toArray() : (isset($kamar) ? $kamar->fasilitas()->pluck('id')->toArray() : []));
@endphp

@foreach($fasilitas as $f)
    @php
        $isChecked = in_array($f->id, (array) $selectedIds);
    @endphp
    <div class="flex items-center justify-between p-3 bg-white dark:bg-slate-800 border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,1)] rounded-none">
        <label class="inline-flex items-center space-x-3 text-sm text-slate-700 dark:text-slate-300 cursor-pointer select-none">
            <input type="checkbox" name="fasilitas[]" value="{{ $f->id }}" class="admin-checkbox" {{ $isChecked ? 'checked' : '' }}>
            <span class="font-bold flex items-center gap-1.5">
                <span class="text-base">
                    {{ $f->emoji }}
                </span>
                {{ $f->nama }}
                @if(!$f->is_active)
                    <span class="text-[9px] px-1 py-0.5 bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-200 border border-red-400 font-extrabold uppercase">Nonaktif</span>
                @endif
            </span>
        </label>
        
        <div class="flex items-center gap-2">
            <button type="button" 
                    class="btn-edit-facility text-[11px] font-bold text-blue-600 dark:text-blue-400 hover:underline"
                    data-id="{{ $f->id }}" 
                    data-nama="{{ $f->nama }}" 
                    data-ikon="{{ $f->ikon }}" 
                    data-deskripsi="{{ $f->deskripsi }}" 
                    data-active="{{ $f->is_active ? 1 : 0 }}">
                Edit
            </button>
            <button type="button" 
                    class="btn-delete-facility text-[11px] font-bold text-red-600 dark:text-red-400 hover:underline"
                    data-id="{{ $f->id }}">
                Hapus
            </button>
        </div>
    </div>
@endforeach
