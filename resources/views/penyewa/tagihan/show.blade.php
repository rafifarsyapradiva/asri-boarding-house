<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl text-black leading-tight uppercase tracking-wider">
            {{ __('Detail Tagihan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="p-4 bg-emerald-400 dark:bg-emerald-800 border-4 border-black dark:border-white text-black dark:text-white font-extrabold shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                    <div class="flex items-center">
                        <span class="mr-2">✅</span>
                        <span class="font-bold text-sm">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-red-400 dark:bg-red-800 border-4 border-black dark:border-white text-black dark:text-white font-extrabold shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)]">
                    <div class="flex items-center">
                        <span class="mr-2">❌</span>
                        <span class="font-bold text-sm">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <div class="admin-card">
                <div class="text-gray-900 dark:text-gray-100 space-y-6">
                    <div class="border-b-4 border-black dark:border-white pb-6 flex flex-wrap justify-between items-center gap-4">
                        <div>
                            <h3 class="text-lg font-black text-slate-900 dark:text-slate-100 uppercase tracking-wide">Tagihan #{{ $tagihan->order_id }}</h3>
                            <p class="admin-subtitle">Periode: {{ $tagihan->periode_formatted }}</p>
                        </div>
                        <div>
                            <span class="admin-badge {{ $tagihan->status_badge_class }} text-sm py-1.5 px-4 font-black">
                                {{ strtoupper($tagihan->computed_status) }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <div class="lg:col-span-5 space-y-4">
                            <div>
                                <span class="admin-label">Kamar</span>
                                <span class="text-sm font-black text-slate-900 dark:text-slate-100 uppercase tracking-wide">
                                    Kamar {{ $tagihan->penyewa?->kamar?->nomor_kamar ?? '-' }} (Lantai {{ $tagihan->penyewa?->kamar?->lantai ?? '-' }})
                                </span>
                            </div>
                            <div>
                                <span class="admin-label">Tanggal Tagihan</span>
                                <span class="text-sm font-extrabold text-slate-800 dark:text-slate-200">
                                    {{ $tagihan->tanggal_tagihan?->format('d M Y') ?? '-' }}
                                </span>
                            </div>
                            <div>
                                <span class="admin-label">Jatuh Tempo</span>
                                <span class="text-sm font-extrabold text-slate-800 dark:text-slate-200">
                                    {{ $tagihan->tanggal_jatuh_tempo?->format('d M Y') ?? '-' }}
                                </span>
                            </div>
                        </div>

                        <div class="lg:col-span-7 space-y-4 bg-yellow-100 dark:bg-yellow-950/40 p-5 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] text-black dark:text-white transition-all duration-200 hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#000000] dark:hover:shadow-[6px_6px_0px_0px_#ffffff]">
                            <h4 class="font-black text-xs uppercase tracking-wider text-black dark:text-slate-200 mb-2">Rincian Pembayaran</h4>
                            <div class="flex justify-between text-xs font-bold uppercase tracking-wide">
                                <span>Harga Pokok Sewa</span>
                                <span>Rp {{ number_format($tagihan->nominal_pokok, 0, ',', '.') }}</span>
                            </div>
                            @if($tagihan->nominal_deposit > 0)
                                <div class="flex justify-between text-xs font-bold uppercase tracking-wide">
                                    <span>Uang Deposit Jaminan Kamar</span>
                                    <span>Rp {{ number_format($tagihan->nominal_deposit, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($tagihan->nominal_denda > 0)
                                <div class="flex justify-between text-xs font-bold text-red-600 dark:text-red-400 mt-1 uppercase tracking-wide">
                                    <span>Denda Keterlambatan</span>
                                    <span>Rp {{ number_format($tagihan->nominal_denda, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="border-t-2 border-black dark:border-white my-3 pt-3 flex justify-between text-base font-black uppercase tracking-wide">
                                <span>Total Harus Dibayar</span>
                                <span class="text-blue-600 dark:text-blue-400">Rp {{ number_format($tagihan->nominal_total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    @if($tagihan->keterangan)
                        <div class="bg-cyan-100 dark:bg-cyan-950/40 border-2 border-black dark:border-white p-4 text-xs font-bold text-slate-900 dark:text-slate-100 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            <strong class="uppercase tracking-wide font-black block mb-1">Keterangan:</strong> {{ $tagihan->keterangan }}
                        </div>
                    @endif

                    @if($tagihan->can_be_paid)
                        <div class="border-t-4 border-black dark:border-white pt-8 mt-6">
                            <h4 class="font-black text-slate-900 dark:text-slate-100 mb-4 uppercase tracking-wider text-xs">Pilih Metode Pembayaran</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Pilihan 1: Bayar Online (Instan & Otomatis) -->
                                <div class="bg-blue-100 dark:bg-blue-950/40 border-4 border-black dark:border-white p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] flex flex-col justify-between transition-all duration-200 hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)]">
                                    <div>
                                        <div class="flex items-center gap-2 mb-3">
                                            <span class="text-lg">💳</span>
                                            <h5 class="font-black text-slate-900 dark:text-slate-100 text-xs uppercase tracking-wide">Pembayaran Online Otomatis</h5>
                                        </div>
                                        <p class="text-[11px] font-bold text-slate-800 dark:text-slate-200 leading-relaxed mb-4">
                                            Bayar instan via <strong>Midtrans Snap</strong>. Mendukung Transfer Bank Virtual Account (BCA, Mandiri, BNI, BRI), QRIS (GoPay, OVO, ShopeePay), dan Kartu Kredit. Pembayaran Anda akan otomatis terverifikasi tanpa perlu kirim bukti transfer.
                                        </p>
                                    </div>
                                    <button id="btn-bayar" onclick="window.mulaiBayarOnline(this)" class="admin-btn-primary w-full mt-2 justify-center py-2.5 text-center font-black">
                                        Bayar Online Sekarang
                                    </button>
                                </div>

                                <!-- Pilihan 2: Transfer Manual / Tunai -->
                                <div class="bg-white dark:bg-slate-900 border-4 border-black dark:border-white p-5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] flex flex-col justify-between transition-all duration-200 hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,1)]">
                                    <div>
                                        <div class="flex items-center gap-2 mb-3">
                                            <span class="text-lg">💵</span>
                                            <h5 class="font-black text-slate-900 dark:text-slate-100 text-xs uppercase tracking-wide">Transfer Manual / Cash</h5>
                                        </div>
                                        <p class="text-[11px] font-bold text-slate-800 dark:text-slate-200 leading-relaxed mb-3">
                                            Lakukan transfer bank secara manual atau bayar tunai secara langsung ke pengelola kost:
                                        </p>
                                        <div class="bg-slate-100 dark:bg-slate-800 p-3 border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff] mb-4 space-y-1.5 text-xs font-bold text-slate-800 dark:text-slate-200">
                                            <div class="flex justify-between items-center">
                                                <span>{{ \App\Models\Setting::get('bank_name', 'Bank Mandiri') }}</span>
                                                <div class="flex items-center gap-2">
                                                    <span id="norek" class="font-black text-black dark:text-white">{{ \App\Models\Setting::get('bank_account_number', '123-456-7890') }}</span>
                                                    <button id="btn-copy" type="button" class="text-[9px] px-2 py-0.5 border border-black dark:border-white bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 font-extrabold uppercase transition-all duration-100 shadow-[1px_1px_0px_0px_#000000] dark:shadow-[1px_1px_0px_0px_#ffffff] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none">
                                                        Salin
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Atas Nama</span>
                                                <span class="font-black text-black dark:text-white">{{ \App\Models\Setting::get('bank_account_owner', 'Asri Boarding House') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="https://wa.me/{{ \App\Models\Setting::formatWhatsapp(\App\Models\Setting::get('contact_whatsapp', config('services.wa_owner', ''))) }}?text={{ rawurlencode($tagihan->wa_confirmation_message) }}" target="_blank" rel="noopener noreferrer" class="admin-btn-success w-full mt-2 text-center justify-center py-2.5 font-black">
                                        💬 Kirim Bukti Ke WhatsApp
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="pt-6 border-t-4 border-black dark:border-white flex flex-wrap gap-4 items-center justify-end">
                        <a href="{{ route('penyewa.tagihan.index') }}" class="admin-btn-secondary">
                            Kembali
                        </a>

                        @if($tagihan->computed_status === 'lunas' && $tagihan->pembayaran_terkonfirmasi)
                            <a href="{{ route('penyewa.nota.download', $tagihan->pembayaran_terkonfirmasi->id) }}" class="admin-btn-success">
                                📥 Unduh Nota Pembayaran (PDF)
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


@push('scripts')
    @if($tagihan->can_be_paid)
        <script src="{{ config('midtrans.snap_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
        <script>
            window.isProcessingPayment = false;
            
            window.mulaiBayarOnline = async function(btn) {
                if (window.isProcessingPayment) return;
                window.isProcessingPayment = true;
                
                const originalText = btn.textContent;
                btn.disabled = true;
                btn.textContent = 'Memproses...';
                
                try {
                    const response = await fetch('{{ route("penyewa.pembayaran.token", $tagihan) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });
                    
                    if (!response.ok) {
                        let errMsg = 'Gagal menghubungi server (HTTP ' + response.status + ').';
                        try {
                            const errData = await response.json();
                            errMsg = errData.message || errMsg;
                        } catch (e) {}
                        throw new Error(errMsg);
                    }
                    
                    const data = await response.json();
                    
                    if (!data.snap_token) {
                        throw new Error(data.message || 'Token tidak diterima');
                    }
                    
                    if (typeof window.snap === 'undefined') {
                        throw new Error('Midtrans Snap tidak termuat. Periksa koneksi internet atau matikan AdBlock Anda.');
                    }
                    
                    window.snap.pay(data.snap_token, {
                        onSuccess: function() {
                            window.location.href = '{{ route("penyewa.tagihan.index") }}';
                        },
                        onPending: function() {
                            window.location.reload();
                        },
                        onError: function(result) {
                            if (typeof window.showToast === 'function') window.showToast('Pembayaran gagal: ' + (result.status_message || 'Silakan coba lagi.'), 'error');
                            window.location.reload();
                        },
                        onClose: function() {
                            window.isProcessingPayment = false;
                            btn.disabled = false;
                            btn.textContent = originalText;
                        }
                    });
                } catch (error) {
                    alert('Terjadi kesalahan: ' + error.message);
                    window.isProcessingPayment = false;
                    btn.disabled = false;
                    btn.textContent = originalText;
                }
            };
        </script>
        
        <script>
            (function() {


                const btnCopy = document.getElementById('btn-copy');
                const norekElem = document.getElementById('norek');

                if (btnCopy && norekElem) {
                    btnCopy.addEventListener('click', function() {
                        const norek = norekElem.innerText.trim();
                        const oldText = btnCopy.textContent;

                        const updateButtonState = () => {
                            btnCopy.textContent = 'Tersalin! ✓';
                            btnCopy.classList.remove('bg-slate-200', 'dark:bg-slate-700');
                            btnCopy.classList.add('bg-emerald-300', 'dark:bg-emerald-700', 'text-black');
                            
                            setTimeout(() => {
                                btnCopy.textContent = oldText;
                                btnCopy.classList.remove('bg-emerald-300', 'dark:bg-emerald-700', 'text-black');
                                btnCopy.classList.add('bg-slate-200', 'dark:bg-slate-700');
                            }, 2000);
                        };

                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            navigator.clipboard.writeText(norek).then(updateButtonState).catch(() => {
                                fallbackCopy(norek, updateButtonState);
                            });
                        } else {
                            fallbackCopy(norek, updateButtonState);
                        }
                    });
                }

                function fallbackCopy(text, callback) {
                    const textArea = document.createElement("textarea");
                    textArea.value = text;
                    textArea.style.position = "fixed";
                    document.body.appendChild(textArea);
                    textArea.focus();
                    textArea.select();
                    try {
                        document.execCommand('copy');
                        callback();
                    } catch (err) {
                        console.error('Fallback copy failed: ', err);
                    }
                    document.body.removeChild(textArea);
                }
            })();
        </script>
    @endif
@endpush
</x-app-layout>

