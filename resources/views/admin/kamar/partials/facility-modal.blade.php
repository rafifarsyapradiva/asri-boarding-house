<!-- Neo-Brutalist Reusable Modal for Facility CRUD -->
<div id="facility-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/60 p-4">
    <div class="w-full max-w-md bg-white dark:bg-slate-900 border-4 border-black dark:border-white shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,1)] p-6 relative">
        <h3 id="modal-title" class="text-lg font-black text-slate-900 dark:text-white mb-4 border-b-2 border-black dark:border-white pb-2">
            Tambah Fasilitas Baru
        </h3>
        
        <div id="modal-error-alert" class="hidden mb-4 p-3 bg-red-100 dark:bg-red-950 border-2 border-black dark:border-white text-red-900 dark:text-red-200 text-xs font-bold">
            <span class="block" id="modal-error-msg"></span>
        </div>

        <form id="facility-form" data-store-url="{{ route('admin.fasilitas.store') }}" data-update-url-pattern="{{ route('admin.fasilitas.update', ':id') }}" data-destroy-url-pattern="{{ route('admin.fasilitas.destroy', ':id') }}">
            @csrf
            <input type="hidden" id="facility-id" name="facility_id" value="">
            
            <div class="space-y-4">
                <!-- Nama Fasilitas -->
                <div>
                    <label for="fac-nama" class="admin-label">Nama Fasilitas <span class="text-red-500">*</span></label>
                    <input type="text" id="fac-nama" name="nama" class="admin-input" required placeholder="Contoh: Water Heater">
                </div>
                
                <!-- Ikon -->
                <div>
                    <label for="fac-ikon" class="admin-label">Ikon <span class="text-red-500">*</span></label>
                    <select id="fac-ikon" name="ikon" class="admin-select" required>
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
                    <textarea id="fac-deskripsi" name="deskripsi" class="admin-textarea !h-20" required placeholder="Tulis deskripsi singkat fasilitas..."></textarea>
                </div>
                
                <!-- Status Aktif -->
                <div>
                    <label class="inline-flex items-center space-x-3 text-sm text-slate-700 dark:text-slate-300 cursor-pointer select-none">
                        <input type="checkbox" id="fac-is-active" name="is_active" class="admin-checkbox" checked value="1">
                        <span class="font-bold">Status Aktif (Tampil di Landing Page & Kamar)</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 border-t-2 border-black dark:border-white pt-4">
                <button type="button" id="btn-close-modal" class="admin-btn-secondary">
                    Batal
                </button>
                <button type="submit" id="btn-submit-facility" class="admin-btn-primary">
                    <span id="btn-submit-text">Simpan Fasilitas</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('facility-modal');
    if (!modal) return;

    const modalTitle = document.getElementById('modal-title');
    const form = document.getElementById('facility-form');
    const modalErrorAlert = document.getElementById('modal-error-alert');
    const modalErrorMsg = document.getElementById('modal-error-msg');
    
    const inputId = document.getElementById('facility-id');
    const inputNama = document.getElementById('fac-nama');
    const inputIkon = document.getElementById('fac-ikon');
    const inputDeskripsi = document.getElementById('fac-deskripsi');
    const inputIsActive = document.getElementById('fac-is-active');
    
    const btnAdd = document.getElementById('btn-add-facility');
    const btnClose = document.getElementById('btn-close-modal');
    const btnSubmit = document.getElementById('btn-submit-facility');
    const btnSubmitText = document.getElementById('btn-submit-text');
    const container = document.getElementById('facilities-list-container');
    const roomId = "{{ isset($kamar) ? $kamar->id : '' }}";

    // Safe scoped toast notification helper
    function showNotification(message, type = 'success') {
        if (typeof window.showToast === 'function') {
            window.showToast(message, type);
            return;
        }

        const existingToast = document.getElementById('dynamic-toast');
        if (existingToast) existingToast.remove();

        const toastHtml = `
        <div id="dynamic-toast" class="fixed top-5 right-5 z-50 transform translate-y-[-20px] opacity-0 transition-all duration-500 ease-out pointer-events-auto">
            <div class="flex items-center gap-3 p-4 rounded-2xl shadow-2xl border backdrop-blur-md max-w-sm ${
                type === 'success' ? 'bg-emerald-600/95 border-emerald-500 text-white' : 'bg-rose-600/95 border-rose-500 text-white'
            }">
                <div class="flex-shrink-0 text-xl">${type === 'success' ? '✅' : '❌'}</div>
                <div class="flex-1">
                    <p class="text-xs font-extrabold tracking-wide uppercase opacity-75">${type === 'success' ? 'Sukses' : 'Gagal'}</p>
                    <p class="text-sm font-semibold">${message}</p>
                </div>
                <button type="button" onclick="document.getElementById('dynamic-toast').remove()" class="flex-shrink-0 text-white hover:opacity-75 focus:outline-none ml-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', toastHtml);
        const toast = document.getElementById('dynamic-toast');
        setTimeout(() => toast?.classList.replace('opacity-0', 'opacity-100'), 10);
        setTimeout(() => toast?.remove(), 4000);
    }
    
    // Helper to show/hide modal
    function toggleModal(show) {
        if (show) {
            modal.classList.remove('hidden');
            modalErrorAlert.classList.add('hidden');
        } else {
            modal.classList.add('hidden');
            form.reset();
            inputId.value = '';
        }
    }
    
    // Open modal to add
    if (btnAdd) {
        btnAdd.addEventListener('click', function() {
            modalTitle.textContent = 'Tambah Fasilitas Baru';
            toggleModal(true);
        });
    }
    
    // Close modal
    if (btnClose) {
        btnClose.addEventListener('click', function() {
            toggleModal(false);
        });
    }
    
    // Refresh Facilities checklist HTML
    function refreshFacilitiesList(newlyCreatedId = null) {
        if (!container) return;
        const checkedIds = Array.from(container.querySelectorAll('input[name="fasilitas[]"]:checked')).map(cb => cb.value);
        if (newlyCreatedId) {
            checkedIds.push(newlyCreatedId.toString());
        }

        const baseUrl = "{{ route('admin.fasilitas.listHtml') }}";
        const url = roomId ? `${baseUrl}?kamar_id=${roomId}` : baseUrl;
        
        fetch(url)
            .then(res => {
                if (!res.ok) throw new Error('HTTP Status Error: ' + res.status);
                return res.text();
            })
            .then(html => {
                container.innerHTML = html;

                // Restore checked state
                container.querySelectorAll('input[name="fasilitas[]"]').forEach(cb => {
                    if (checkedIds.includes(cb.value.toString())) {
                        cb.checked = true;
                    }
                });
            })
            .catch(err => console.error('Gagal memuat ulang daftar fasilitas:', err));
    }
    
    // Double-Submit Protected Submit Form via AJAX
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const id = inputId.value;
        const isEdit = !!id;
        
        const storeUrl = form.dataset.storeUrl;
        const updateUrlPattern = form.dataset.updateUrlPattern;
        const url = isEdit ? updateUrlPattern.replace(':id', id) : storeUrl;
        const method = isEdit ? 'PUT' : 'POST';
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

        // Lock UI State
        btnSubmit.disabled = true;
        btnSubmitText.textContent = 'Memproses...';
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                nama: inputNama.value,
                ikon: inputIkon.value,
                deskripsi: inputDeskripsi.value,
                is_active: inputIsActive.checked ? 1 : 0,
                _method: method
            })
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                let errMsg = data.message || 'Terjadi kesalahan sistem.';
                if (data.errors) {
                    errMsg = Object.values(data.errors).flat().join('\n');
                }
                throw new Error(errMsg);
            }
            return data;
        })
        .then(data => {
            toggleModal(false);
            refreshFacilitiesList(data.data ? data.data.id : null);
            showNotification(data.message || 'Sukses menyimpan fasilitas!', 'success');
        })
        .catch(err => {
            modalErrorAlert.classList.remove('hidden');
            modalErrorMsg.textContent = err.message;
        })
        .finally(() => {
            // Unlock UI State
            btnSubmit.disabled = false;
            btnSubmitText.textContent = 'Simpan Fasilitas';
        });
    });

    // Clean Event Delegation for Edit and Delete action buttons
    if (container) {
        container.addEventListener('click', function(e) {
            const editBtn = e.target.closest('.btn-edit-facility');
            const deleteBtn = e.target.closest('.btn-delete-facility');

            if (editBtn) {
                modalTitle.textContent = 'Edit Fasilitas';
                inputId.value = editBtn.dataset.id;
                inputNama.value = editBtn.dataset.nama;
                inputIkon.value = editBtn.dataset.ikon;
                inputDeskripsi.value = editBtn.dataset.deskripsi;
                inputIsActive.checked = editBtn.dataset.active == 1;
                toggleModal(true);
                return;
            }

            if (deleteBtn) {
                const id = deleteBtn.dataset.id;
                const destroyPattern = form.dataset.destroyUrlPattern;
                const deleteUrl = destroyPattern.replace(':id', id);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                
                const performDelete = () => {
                    fetch(deleteUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            _method: 'DELETE'
                        })
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) {
                            throw new Error(data.message || 'Gagal menghapus fasilitas.');
                        }
                        return data;
                    })
                    .then(data => {
                        refreshFacilitiesList();
                        showNotification(data.message || 'Fasilitas berhasil dihapus!', 'success');
                    })
                    .catch(err => {
                        showNotification(err.message, 'error');
                    });
                };

                if (window.brutalistConfirm) {
                    window.brutalistConfirm(
                        'Hapus Fasilitas Master',
                        'Apakah Anda yakin ingin menghapus fasilitas master ini? (Catatan: Fasilitas yang sedang digunakan oleh kamar tidak dapat dihapus)',
                        performDelete,
                        { isDanger: true }
                    );
                } else {
                    performDelete();
                }
            }
        });
    }
});
</script>
