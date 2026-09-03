<!-- Neo-Brutalist Modal for Facility CRUD Partial -->
<div id="facility-modal" 
     class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/60 p-4" 
     role="dialog" 
     aria-modal="true" 
     aria-labelledby="modal-title" 
     data-testid="facility-modal">
    
    <div class="w-full max-w-md bg-white dark:bg-slate-900 border-4 border-black dark:border-white shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,1)] p-6 relative">
        
        <!-- Header & Close Icon Button -->
        <div class="flex items-center justify-between mb-4 border-b-2 border-black dark:border-white pb-2">
            <h3 id="modal-title" class="text-lg font-black text-slate-900 dark:text-white">
                Tambah Fasilitas Baru
            </h3>
            <button type="button" class="btn-close-modal text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white text-xl font-bold leading-none" data-testid="btn-x-close-modal" aria-label="Tutup modal">
                &times;
            </button>
        </div>
        
        <!-- Error Alert Container -->
        <div id="modal-error-alert" class="hidden mb-4 p-3 bg-red-100 dark:bg-red-950 border-2 border-black dark:border-white text-red-900 dark:text-red-200 text-xs font-bold whitespace-pre-line" data-testid="modal-error-alert">
            <span class="block" id="modal-error-msg"></span>
        </div>

        <form id="facility-form" data-store-url="{{ route('admin.fasilitas.store') }}" data-testid="facility-form">
            @csrf
            <input type="hidden" id="facility-id" name="facility_id" value="">
            
            <div class="space-y-4">
                <!-- Nama Fasilitas -->
                <div>
                    <label for="fac-nama" class="admin-label">Nama Fasilitas <span class="text-red-500">*</span></label>
                    <input type="text" id="fac-nama" name="nama" class="admin-input" required maxlength="100" placeholder="Contoh: Water Heater" data-testid="input-fac-nama">
                </div>
                
                <!-- Ikon -->
                <div>
                    <label for="fac-ikon" class="admin-label">Ikon <span class="text-red-500">*</span></label>
                    <select id="fac-ikon" name="ikon" class="admin-select" required data-testid="select-fac-ikon">
                        <option value="wifi">WiFi (📶)</option>
                        <option value="snowflake">AC / Snowflake (❄️)</option>
                        <option value="bolt">Listrik / Bolt (⚡)</option>
                        <option value="bath">Kamar Mandi Dalam / Bath (🛁)</option>
                        <option value="shower">Kamar Mandi Luar / Shower (🚿)</option>
                        <option value="door-closed">Lemari Pakaian / Door Closed (🚪)</option>
                        <option value="desktop">Meja Belajar / Desktop (🖥️)</option>
                        <option value="bed">Kasur / Bed (🛏️)</option>
                    </select>
                </div>
                
                <!-- Deskripsi -->
                <div>
                    <label for="fac-deskripsi" class="admin-label">Deskripsi <span class="text-red-500">*</span></label>
                    <textarea id="fac-deskripsi" name="deskripsi" class="admin-textarea !h-20" required placeholder="Tulis deskripsi singkat fasilitas..." data-testid="textarea-fac-deskripsi"></textarea>
                </div>
                
                <!-- Status Aktif -->
                <div>
                    <label class="inline-flex items-center space-x-3 text-sm text-slate-700 dark:text-slate-300 cursor-pointer select-none">
                        <input type="checkbox" id="fac-is-active" name="is_active" class="admin-checkbox" checked value="1" data-testid="checkbox-fac-active">
                        <span class="font-bold">Status Aktif (Tampil di Landing Page & Kamar)</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 border-t-2 border-black dark:border-white pt-4">
                <button type="button" id="btn-close-modal" class="btn-close-modal admin-btn-secondary" data-testid="btn-close-modal">
                    Batal
                </button>
                <button type="submit" id="btn-submit-facility" class="admin-btn-primary" data-testid="btn-submit-facility">
                    <span id="submit-spinner" class="hidden animate-spin mr-1">🌀</span>
                    <span id="submit-text">Simpan Fasilitas</span>
                </button>
            </div>
        </form>
    </div>
</div>
