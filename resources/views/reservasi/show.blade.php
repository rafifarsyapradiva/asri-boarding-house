<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 w-full">
            <h2 class="font-black text-xl text-black uppercase tracking-wider">
                {{ __('Pembayaran Reservasi') }}
            </h2>
            <a href="{{ route('landing.index') }}" class="inline-block px-4 py-2 bg-white dark:bg-slate-800 hover:bg-yellow-400 dark:hover:bg-yellow-500 text-black dark:text-white dark:hover:text-black border-4 border-black dark:border-white font-black text-xs uppercase tracking-wider shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] hover:translate-x-[2px] hover:translate-y-[2px] hover:shadow-[2px_2px_0px_0px_#000000] dark:hover:shadow-[2px_2px_0px_0px_#ffffff] active:translate-x-[3px] active:translate-y-[3px] active:shadow-[0px_0px_0px_0px_#000000] transition duration-150 cursor-pointer text-center">
                ← Kembali ke Beranda
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-screen text-black dark:text-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="p-4 bg-yellow-400 text-black border-4 border-black dark:border-white font-black uppercase tracking-wide shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-red-500 text-white border-4 border-black dark:border-white font-black uppercase tracking-wide shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if($reservasi->status !== 'batal')
                <!-- 5-Step Visual Stepper (Refactored to dynamic loop) -->
                @php
                    $steps = [
                        1 => [
                            'title' => 'Booking',
                            'completed' => true,
                            'active' => false,
                        ],
                        2 => [
                            'title' => 'Diskusi',
                            'completed' => in_array($reservasi->status, ['dp', 'lunas', 'dikonfirmasi']),
                            'active' => $reservasi->status === 'pending',
                        ],
                        3 => [
                            'title' => 'Bayar DP/Full',
                            'completed' => in_array($reservasi->status, ['dp', 'lunas', 'dikonfirmasi']),
                            'active' => $reservasi->status === 'pending',
                        ],
                        4 => [
                            'title' => 'Aktivasi',
                            'completed' => $reservasi->status === 'dikonfirmasi',
                            'active' => in_array($reservasi->status, ['dp', 'lunas']),
                        ],
                        5 => [
                            'title' => 'Selesai',
                            'completed' => $reservasi->status === 'dikonfirmasi',
                            'active' => false,
                        ],
                    ];
                @endphp

                <div class="bg-white dark:bg-slate-950 border-4 border-black dark:border-white p-6 shadow-[6px_6px_0px_0px_#000000] dark:shadow-[6px_6px_0px_0px_#ffffff] mb-8">
                    <h3 class="text-xs font-black uppercase tracking-widest text-black dark:text-white mb-4">
                        Langkah Reservasi Kamar (1 s/d 5)
                    </h3>
                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                        @foreach($steps as $stepNum => $step)
                            @php
                                $isCompleted = $step['completed'];
                                $isActive = $step['active'];

                                if ($isCompleted) {
                                    $containerStyle = 'bg-black dark:bg-white text-white dark:text-black';
                                    $badgeStyle = 'bg-yellow-400 text-black';
                                    $textMuted = 'text-yellow-400 dark:text-yellow-600';
                                } elseif ($isActive) {
                                    $containerStyle = 'bg-yellow-400 text-black';
                                    $badgeStyle = 'bg-white text-black';
                                    $textMuted = 'text-black/70';
                                } else {
                                    $containerStyle = 'bg-gray-100 dark:bg-slate-800 text-gray-400 dark:text-slate-500 border-dashed';
                                    $badgeStyle = 'bg-gray-300 dark:bg-slate-700 text-gray-600 dark:text-slate-400';
                                    $textMuted = 'text-gray-400 dark:text-slate-500';
                                }
                            @endphp

                            <div class="border-4 border-black dark:border-white p-3 flex items-center gap-3 shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff] {{ $containerStyle }}">
                                <span class="w-7 h-7 rounded-full flex items-center justify-center font-black text-xs shrink-0 border-2 border-black dark:border-white {{ $badgeStyle }}">
                                    {!! $isCompleted ? '✓' : $stepNum !!}
                                </span>
                                <div>
                                    <p class="text-[9px] font-black uppercase tracking-wider {{ $textMuted }}">Step {{ $stepNum }}</p>
                                    <h4 class="text-xs font-black uppercase leading-tight">{{ $step['title'] }}</h4>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Dynamic Instructions Banner -->
                    <div class="mt-6 p-4 bg-yellow-100 dark:bg-yellow-950/20 text-black dark:text-yellow-200 border-4 border-black dark:border-yellow-400 font-bold text-xs uppercase tracking-wide shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#facc15]">
                        @if($reservasi->status === 'pending')
                            📢 <span class="font-black text-red-600 dark:text-red-400">Langkah Saat Ini (Langkah 2 & 3):</span> Silakan hubungi admin via chat box di bawah untuk memverifikasi kesiapan kamar fisik, kemudian lakukan pembayaran dengan menekan tombol <strong class="bg-yellow-400 text-black px-1.5 py-0.5 border-2 border-black shadow-[1px_1px_0px_0px_#000000]">Bayar Sekarang</strong> di kolom kanan.
                        @elseif(in_array($reservasi->status, ['dp', 'lunas']))
                            📢 <span class="font-black text-blue-600 dark:text-blue-400">Langkah Saat Ini (Langkah 4):</span> Pembayaran awal Anda berhasil diverifikasi secara otomatis! Harap tunggu admin memvalidasi data dan mengaktifkan sewa kamar Anda secara resmi.
                        @elseif($reservasi->status === 'dikonfirmasi')
                            📢 <span class="font-black text-green-600 dark:text-green-400">Langkah Saat Ini (Langkah 5):</span> Reservasi selesai! Kamar Anda sudah aktif. Silakan salin Kode Akses Kamar digital Anda di bawah dan masuk ke Dashboard utama Anda.
                        @endif
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Detail Reservasi -->
                <div class="md:col-span-2 bg-white dark:bg-slate-950 border-4 border-black dark:border-white p-6 shadow-[6px_6px_0px_0px_#000000] dark:shadow-[6px_6px_0px_0px_#ffffff] space-y-6">
                    <div>
                        <h3 class="text-xl font-black text-black dark:text-white uppercase tracking-tight border-b-4 border-black dark:border-white pb-2 mb-6">Detail Booking Kamar</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-black tracking-wider">Kamar Pilihan</p>
                                <p class="text-sm font-bold text-black dark:text-white mt-1">
                                    Kamar {{ $reservasi->kamar?->nomor_kamar ?? '-' }} (Lantai {{ $reservasi->kamar?->lantai ?? '-' }})
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-black tracking-wider">Tipe Sewa & Durasi</p>
                                <p class="text-sm font-bold text-black dark:text-white mt-1 capitalize">
                                    {{ $reservasi->tipe_sewa }} ({{ $reservasi->durasi }} {{ $reservasi->tipe_sewa === 'harian' ? 'Hari' : ($reservasi->tipe_sewa === 'mingguan' ? 'Minggu' : 'Bulan') }})
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-black tracking-wider">Tanggal Mulai</p>
                                <p class="text-sm font-bold text-black dark:text-white mt-1">
                                    {{ $reservasi->tanggal_mulai ? $reservasi->tanggal_mulai->format('d M Y') : '-' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-black tracking-wider">Tanggal Selesai</p>
                                <p class="text-sm font-bold text-black dark:text-white mt-1">
                                    {{ $reservasi->tanggal_selesai ? $reservasi->tanggal_selesai->format('d M Y') : '-' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-black tracking-wider">Total Nilai Sewa</p>
                                <p class="text-base font-black text-black dark:text-white mt-1">
                                    Rp {{ number_format($reservasi->total_harga, 0, ',', '.') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-black tracking-wider">Status Reservasi</p>
                                <div class="mt-2">
                                    @php
                                        $badgeColor = match($reservasi->status) {
                                            'pending' => 'bg-yellow-400 text-black border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff]',
                                            'dp' => 'bg-white dark:bg-slate-800 text-black dark:text-white border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff]',
                                            'lunas' => 'bg-black dark:bg-white text-white dark:text-black border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff]',
                                            'dikonfirmasi' => 'bg-black dark:bg-white text-white dark:text-black border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff]',
                                            'batal' => 'bg-red-500 text-white border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff]',
                                            default => 'bg-gray-200 dark:bg-slate-700 text-black dark:text-white border-2 border-black dark:border-white shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff]',
                                        };
                                    @endphp
                                    <span class="px-3 py-1.5 text-xs font-black uppercase tracking-wider {{ $badgeColor }}">
                                        {{ strtoupper($reservasi->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 border-t-4 border-black dark:border-white pt-6">
                            @if($reservasi->is_dp)
                                <div class="bg-yellow-50 dark:bg-slate-900 p-5 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] text-black dark:text-white">
                                    <h4 class="font-black uppercase text-sm mb-3 border-b-2 border-black dark:border-white pb-1 inline-block">Skema Uang Muka (DP 30%)</h4>
                                    <div class="flex justify-between text-sm font-bold">
                                        <span>Uang Muka yang Harus Dibayar Sekarang:</span>
                                        <span class="font-black bg-yellow-400 border border-black dark:border-white text-black px-2 py-0.5 shadow-[1px_1px_0px_0px_#000000] dark:shadow-[1px_1px_0px_0px_#ffffff]">Rp {{ number_format($reservasi->nominal_dp, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-xs font-bold mt-2 pt-2 border-t border-black dark:border-white border-dashed">
                                        <span>Sisa Pelunasan (Dibayar nanti):</span>
                                        <span class="font-bold">Rp {{ number_format($reservasi->nominal_sisa, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="bg-yellow-50 dark:bg-slate-900 p-5 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] text-black dark:text-white">
                                    <h4 class="font-black uppercase text-sm mb-3 border-b-2 border-black dark:border-white pb-1 inline-block">Skema Pembayaran Penuh</h4>
                                    <div class="flex justify-between text-sm font-black">
                                        <span>Total Kewajiban Pembayaran:</span>
                                        <span class="bg-yellow-400 border border-black dark:border-white text-black px-2 py-0.5 shadow-[1px_1px_0px_0px_#000000] dark:shadow-[1px_1px_0px_0px_#ffffff]">Rp {{ number_format($reservasi->total_harga, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Obrolan Chat Box (Aktif Khusus Saat Status Pending) -->
                    @if(($reservasi->status ?? '') === 'pending')
                        <div class="border-t-4 border-black dark:border-white pt-8">
                            @include('reservasi.chat-box')
                        </div>
                    @else
                        <div class="border-t-4 border-black dark:border-white pt-6">
                            <div class="text-center text-black dark:text-white text-xs py-5 px-4 bg-purple-300 dark:bg-slate-900 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] font-black uppercase tracking-widest rounded-none">
                                🔒 
                                @if($reservasi->status === 'batal')
                                    Obrolan dinonaktifkan karena reservasi ini telah dibatalkan.
                                @elseif(in_array($reservasi->status, ['dp', 'lunas']))
                                    Obrolan detail dinonaktifkan. Silakan berdiskusi di <a href="{{ route('penyewa.reservasi.chat', $reservasi->id) }}" class="underline font-black text-yellow-700 dark:text-yellow-400 bg-white dark:bg-slate-800 px-1.5 py-0.5 border-2 border-black dark:border-white hover:bg-yellow-400 dark:hover:bg-yellow-500 hover:text-black dark:hover:text-black shadow-[2px_2px_0px_0px_#000000] dark:shadow-[2px_2px_0px_0px_#ffffff] transition duration-150">Ruang Chat Room Mandiri</a>.
                                @else
                                    Obrolan dinonaktifkan karena reservasi ini telah dikonfirmasi (Status: {{ strtoupper($reservasi->status) }}).
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Panel Aksi Pembayaran -->
                <div class="bg-white dark:bg-slate-950 border-4 border-black dark:border-white p-6 shadow-[6px_6px_0px_0px_#000000] dark:shadow-[6px_6px_0px_0px_#ffffff] h-fit">
                    <h3 class="text-lg font-black uppercase tracking-tight text-black dark:text-white border-b-4 border-black dark:border-white pb-2 mb-4">Metode Pembayaran</h3>

                    @if($reservasi->status === 'pending')
                        <div class="space-y-4">
                            <p class="text-xs font-bold text-black dark:text-slate-300 leading-relaxed">
                                Silakan verifikasi status kesiapan kamar dengan admin melalui fitur chat di sebelah kiri sebelum menekan tombol bayar di bawah ini.
                            </p>
                            
                            <button id="pay-button" 
                                class="w-full py-3.5 px-4 bg-yellow-400 hover:bg-yellow-300 text-black border-4 border-black dark:border-white font-black uppercase tracking-wider text-sm shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] active:translate-x-[2px] active:translate-y-[2px] active:shadow-[2px_2px_0px_0px_#000000] dark:active:shadow-[2px_2px_0px_0px_#ffffff] transition duration-150 cursor-pointer">
                                Bayar Sekarang (Midtrans)
                            </button>

                            <form action="{{ route('penyewa.reservasi.batal', $reservasi->id) }}" method="POST" data-confirm="Apakah Anda yakin ingin membatalkan reservasi ini? Tindakan ini tidak dapat dibatalkan." data-title="Konfirmasi Pembatalan" data-confirm-danger="true">
                                @csrf
                                <button type="submit"
                                    class="w-full py-2.5 px-4 bg-red-400 hover:bg-red-500 text-black border-4 border-black dark:border-white font-black uppercase tracking-wider text-xs shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition duration-150 cursor-pointer text-center rounded-none">
                                    Batalkan Reservasi
                                </button>
                            </form>
                        </div>

                        <!-- Midtrans Snap Script -->
                        @php
                            $snapScriptUrl = config('midtrans.is_production') 
                                ? 'https://app.midtrans.com/snap/snap.js' 
                                : 'https://app.sandbox.midtrans.com/snap/snap.js';
                        @endphp
                        <script src="{{ $snapScriptUrl }}" data-client-key="{{ $clientKey }}"></script>
                        <script type="text/javascript">
                            (function() {
                                let isProcessing = false;
                                const payButton = document.getElementById('pay-button');
                                if (payButton) {
                                    payButton.addEventListener('click', async function (e) {
                                        e.preventDefault();
                                        if (isProcessing) return;
                                        isProcessing = true;
                                        
                                        this.disabled = true;
                                        this.textContent = 'Memproses...';
                                        
                                        try {
                                            const response = await fetch('{{ route("penyewa.reservasi.pembayaran.token", $reservasi->id) }}', {
                                                method: 'POST',
                                                headers: {
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                    'Content-Type': 'application/json',
                                                    'Accept': 'application/json'
                                                }
                                            });
                                            
                                            if (!response.ok) {
                                                let errMsg = 'Gagal menghubungi server.';
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
                                            
                                            window.snap.pay(data.snap_token, {
                                                onSuccess: function(result) {
                                                    window.location.reload();
                                                },
                                                onPending: function(result) {
                                                    window.location.reload();
                                                },
                                                onError: function(result) {
                                                    if (typeof window.showToast === 'function') window.showToast("Pembayaran gagal! Silakan coba lagi.", 'error');
                                                    window.location.reload();
                                                },
                                                onClose: function() {
                                                    isProcessing = false;
                                                    const btn = document.getElementById('pay-button');
                                                    if (btn) {
                                                        btn.disabled = false;
                                                        btn.textContent = 'Bayar Sekarang (Midtrans)';
                                                    }
                                                }
                                            });
                                        } catch (error) {
                                            if (typeof window.showToast === 'function') window.showToast('Terjadi kesalahan: ' + error.message, 'error');
                                            isProcessing = false;
                                            this.disabled = false;
                                            this.textContent = 'Bayar Sekarang (Midtrans)';
                                        }
                                    });
                                }
                            })();
                        </script>
                    @elseif($reservasi->status === 'batal')
                        <div class="text-center py-8 px-4 text-white bg-red-500 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff]">
                            <span class="text-4xl block mb-2">❌</span>
                            <p class="text-sm font-black uppercase tracking-wider mt-3">Reservasi Dibatalkan / Gagal</p>
                            <p class="text-xs font-bold mt-1 text-white opacity-90">Order ID: {{ $reservasi->order_id }}</p>
                            <div class="mt-4 p-3 bg-black dark:bg-white text-white dark:text-black border-2 border-white dark:border-black font-bold text-xs uppercase tracking-wider">
                                Silakan daftar kamar lainnya atau silakan jumpa kembali.
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8 px-4 text-black bg-yellow-400 border-4 border-black dark:border-white shadow-[4px_4px_0px_0px_#000000] dark:shadow-[4px_4px_0px_0px_#ffffff] space-y-4">
                            <div>
                                <span class="text-4xl block mb-2">✅</span>
                                <p class="text-sm font-black uppercase tracking-wider mt-3">Pembayaran Berhasil / Diterima</p>
                                <p class="text-xs font-bold mt-1 text-black opacity-75">Order ID: {{ $reservasi->order_id }}</p>
                            </div>
                            <div class="pt-4 border-t-2 border-black dark:border-white border-dashed">
                                @if(in_array($reservasi->status, ['dp', 'lunas']))
                                    <!-- UX Improvement: Menghindari redirect loop membingungkan -->
                                    <button class="inline-block w-full py-3 bg-slate-300 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-2 border-slate-400 dark:border-slate-600 font-black uppercase tracking-wider text-xs cursor-not-allowed text-center" disabled>
                                        ⏳ Menunggu Aktivasi Admin
                                    </button>
                                @else
                                    <a href="{{ route('dashboard') }}" class="inline-block w-full py-3 bg-black dark:bg-white hover:bg-slate-900 dark:hover:bg-slate-100 text-white dark:text-black border-2 border-black dark:border-white font-black uppercase tracking-wider text-xs shadow-[3px_3px_0px_0px_#ffffff] dark:shadow-[3px_3px_0px_0px_#000000] active:translate-x-[1px] active:translate-y-[1px] active:shadow-[2px_2px_0px_0px_#ffffff] dark:active:shadow-[2px_2px_0px_0px_#000000] transition duration-150 text-center">
                                        Masuk ke Dashboard Kost →
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
