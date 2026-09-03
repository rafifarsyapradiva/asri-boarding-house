<x-app-layout>
    <x-toast />
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Kelola Master Fasilitas Kamar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100">

                    <!-- Header Section -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-800 dark:text-slate-100">Daftar Master Fasilitas</h3>
                            <p class="admin-subtitle">Kelola nama, deskripsi, ikon emoji, dan status keaktifan fasilitas kamar kost.</p>
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('admin.kamar.index') }}" class="admin-btn-secondary !py-1.5 !px-3 text-xs flex items-center" data-testid="btn-back-to-kamar">
                                &larr; Kembali ke Kamar
                            </a>
                            <button type="button" id="btn-add-facility" class="admin-btn-primary" data-testid="btn-add-facility">
                                <span class="text-sm">+</span> Tambah Fasilitas
                            </button>
                        </div>
                    </div>

                    <!-- Fasilitas Table Component -->
                    <div class="admin-table-container">
                        <table class="admin-table" data-testid="table-facilities">
                            <thead class="admin-table-thead">
                                <tr>
                                    <th scope="col" class="admin-table-th text-center w-20">Ikon</th>
                                    <th scope="col" class="admin-table-th w-64">Nama Fasilitas</th>
                                    <th scope="col" class="admin-table-th">Deskripsi</th>
                                    <th scope="col" class="admin-table-th w-32 text-center">Status</th>
                                    <th scope="col" class="admin-table-th w-40 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="admin-table-tbody" id="facilities-table-body">
                                @forelse($fasilitas as $facility)
                                    <tr class="admin-table-tr" data-testid="facility-row-{{ $facility->id }}">
                                        <!-- Ikon Emoji via Model Accessor -->
                                        <td class="admin-table-td text-center text-2xl font-bold">
                                            {{ $facility->emoji }}
                                        </td>
                                        <!-- Nama -->
                                        <td class="admin-table-td font-extrabold text-slate-800 dark:text-slate-100">
                                            {{ $facility->nama }}
                                        </td>
                                        <!-- Deskripsi -->
                                        <td class="admin-table-td !whitespace-normal text-slate-500 dark:text-slate-400 font-semibold text-xs leading-relaxed max-w-md break-words">
                                            {{ $facility->deskripsi }}
                                        </td>
                                        <!-- Status -->
                                        <td class="admin-table-td text-center">
                                            @if($facility->is_active)
                                                <span class="admin-badge admin-badge-success" data-testid="badge-status-active">Aktif</span>
                                            @else
                                                <span class="admin-badge admin-badge-warning" data-testid="badge-status-inactive">Nonaktif</span>
                                            @endif
                                        </td>
                                        <!-- Aksi -->
                                        <td class="admin-table-td text-center">
                                            <div class="flex justify-center gap-3">
                                                <button type="button" 
                                                        class="btn-edit-facility text-xs font-bold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition duration-150"
                                                        data-testid="btn-edit-facility-{{ $facility->id }}"
                                                        data-id="{{ $facility->id }}" 
                                                        data-nama="{{ $facility->nama }}" 
                                                        data-ikon="{{ $facility->ikon }}" 
                                                        data-deskripsi="{{ $facility->deskripsi }}" 
                                                        data-active="{{ $facility->is_active ? 1 : 0 }}"
                                                        data-update-url="{{ route('admin.fasilitas.update', $facility->id) }}">
                                                    Edit
                                                </button>
                                                <button type="button" 
                                                        class="btn-delete-facility text-xs font-bold text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition duration-150"
                                                        data-testid="btn-delete-facility-{{ $facility->id }}"
                                                        data-id="{{ $facility->id }}"
                                                        data-delete-url="{{ route('admin.fasilitas.destroy', $facility->id) }}">
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-slate-400 dark:text-slate-500 italic" data-testid="empty-facilities-msg">
                                            Belum ada data fasilitas. Silakan tambahkan fasilitas baru.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $fasilitas->links('vendor.pagination.neo-brutalist') }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Neo-Brutalist Modal Partial -->
    @include('admin.fasilitas.partials.modal')

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('facility-modal');
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
        const submitText = document.getElementById('submit-text');
        const submitSpinner = document.getElementById('submit-spinner');
        const tableBody = document.getElementById('facilities-table-body');
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
            || '{{ csrf_token() }}';

        // Reusable Toast Helper
        function showToast(message, type = 'success') {
            const existingToast = document.getElementById('dynamic-toast');
            if (existingToast) existingToast.remove();

            const toastHtml = `
            <div id="dynamic-toast" class="fixed top-5 right-5 z-50 transform translate-y-[-20px] opacity-0 transition-all duration-500 ease-out pointer-events-auto" data-testid="toast-notification">
                <div class="flex items-center gap-3 p-4 rounded-2xl shadow-2xl border backdrop-blur-md max-w-sm ${
                    type === 'success' 
                        ? 'bg-emerald-600/95 border-emerald-500 text-white' 
                        : 'bg-rose-600/95 border-rose-500 text-white'
                }">
                    <div class="flex-shrink-0 text-xl">${type === 'success' ? '✅' : '❌'}</div>
                    <div class="flex-1">
                        <p class="text-xs font-extrabold tracking-wide uppercase opacity-75">${type === 'success' ? 'Sukses' : 'Gagal'}</p>
                        <p class="text-sm font-semibold">${message}</p>
                    </div>
                    <button type="button" onclick="document.getElementById('dynamic-toast').remove()" class="flex-shrink-0 text-white hover:opacity-75 focus:outline-none ml-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>`;
            document.body.insertAdjacentHTML('beforeend', toastHtml);
            const toast = document.getElementById('dynamic-toast');
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-[-20px]', 'opacity-0');
                toast.classList.add('translate-y-0', 'opacity-100');
            });

            setTimeout(() => {
                if (toast) {
                    toast.classList.remove('translate-y-0', 'opacity-100');
                    toast.classList.add('translate-y-[-20px]', 'opacity-0');
                    setTimeout(() => toast.remove(), 500);
                }
            }, 4000);
        }
        
        // Modal state management
        function toggleModal(show) {
            if (show) {
                modal.classList.remove('hidden');
                modalErrorAlert.classList.add('hidden');
            } else {
                modal.classList.add('hidden');
                form.reset();
                inputId.value = '';
                setSubmittingState(false);
            }
        }

        // Form submit state toggler (prevents click-spam)
        function setSubmittingState(isSubmitting) {
            btnSubmit.disabled = isSubmitting;
            if (isSubmitting) {
                submitSpinner.classList.remove('hidden');
                submitText.textContent = 'Memproses...';
            } else {
                submitSpinner.classList.add('hidden');
                submitText.textContent = 'Simpan Fasilitas';
            }
        }
        
        // Open modal to add facility
        btnAdd.addEventListener('click', function() {
            modalTitle.textContent = 'Tambah Fasilitas Baru';
            toggleModal(true);
        });
        
        // Close modal listeners (Batal & X button)
        document.querySelectorAll('.btn-close-modal').forEach(btn => {
            btn.addEventListener('click', function() {
                toggleModal(false);
            });
        });
        
        // Submit store/update via AJAX
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const id = inputId.value;
            const isEdit = !!id;
            const targetUrl = isEdit 
                ? `/admin/fasilitas/${id}` 
                : form.dataset.storeUrl;
            const method = isEdit ? 'PUT' : 'POST';

            setSubmittingState(true);
            
            const formData = {
                nama: inputNama.value.trim(),
                ikon: inputIkon.value,
                deskripsi: inputDeskripsi.value.trim(),
                is_active: inputIsActive.checked ? 1 : 0
            };
            
            fetch(targetUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    ...formData,
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
                showToast(data.message || 'Fasilitas berhasil disimpan!', 'success');
                setTimeout(() => window.location.reload(), 800);
            })
            .catch(err => {
                setSubmittingState(false);
                modalErrorAlert.classList.remove('hidden');
                modalErrorMsg.textContent = err.message;
            });
        });
        
        // Event Delegation for Edit and Delete buttons
        if (tableBody) {
            tableBody.addEventListener('click', function(e) {
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
                    const deleteUrl = deleteBtn.dataset.deleteUrl || `/admin/fasilitas/${id}`;

                    const executeDelete = function() {
                        fetch(deleteUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ _method: 'DELETE' })
                        })
                        .then(async res => {
                            const data = await res.json();
                            if (!res.ok) {
                                throw new Error(data.message || 'Gagal menghapus fasilitas.');
                            }
                            return data;
                        })
                        .then(data => {
                            showToast(data.message || 'Fasilitas berhasil dihapus!', 'success');
                            setTimeout(() => window.location.reload(), 800);
                        })
                        .catch(err => {
                            showToast(err.message, 'error');
                        });
                    };

                    if (typeof window.brutalistConfirm === 'function') {
                        window.brutalistConfirm(
                            'Hapus Fasilitas',
                            'Apakah Anda yakin ingin menghapus fasilitas master ini? (Catatan: Fasilitas yang sedang digunakan oleh kamar tidak dapat dihapus)',
                            executeDelete,
                            { isDanger: true }
                        );
                    } else {
                        executeDelete();
                    }
                }
            });
        }
    });
    </script>
</x-app-layout>

