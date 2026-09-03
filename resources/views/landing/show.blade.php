@extends('layouts.landing')

@section('title', 'Detail Kamar ' . $kamar->nomor_kamar)

@section('wa_custom_message', "Halo Admin Asri Boarding House, saya tertarik dengan Kamar #" . $kamar->nomor_kamar . " (Tipe " . ucfirst($kamar->tipe) . "). Apakah masih tersedia?")

@section('content')
<div class="bg-white min-h-screen py-12 px-6 antialiased text-black">
    <div class="max-w-7xl mx-auto">
        <!-- Back Button in Neo-Brutalism style -->
        <a href="{{ route('landing.index') }}" class="inline-flex items-center gap-2 bg-white text-black border-4 border-black px-5 py-2.5 font-black text-xs sm:text-sm uppercase tracking-wider neo-btn-shadow neo-btn-interactive transition-all duration-200 mb-8">
            ← Kembali ke Beranda
        </a>

        @if ($errors->any())
            <div class="mb-8 p-4 bg-yellow-100 border-4 border-black text-black neo-btn-shadow" id="alertError">
                <div class="font-black uppercase tracking-wider text-xs flex items-center gap-1.5 mb-2">
                    ⚠️ Terjadi kesalahan validasi:
                </div>
                <ul class="list-disc pl-5 space-y-1 text-xs font-bold">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-8 p-4 bg-yellow-400 border-4 border-black text-black font-black uppercase tracking-wider text-sm neo-btn-shadow">
                ✅ {{ session('success') }}
            </div>
        @endif

        <!-- Layout 2 Kolom -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            <!-- Kolom Kiri: Galeri Foto & Daftar Fasilitas -->
            <div class="lg:col-span-2 space-y-10">
                <!-- Galeri Foto Kamar -->
                <div class="bg-white border-4 border-black p-6 neo-card-shadow">
                    <h1 id="page-heading-detail-kamar" class="text-3xl sm:text-4xl font-black text-black uppercase tracking-tight mb-4">
                        Kamar {{ $kamar->nomor_kamar }}
                    </h1>
                    
                    <div class="border-4 border-black overflow-hidden h-[300px] sm:h-[450px] bg-yellow-50 relative">
                        <img src="{{ $kamar->foto_url }}" alt="Foto Kamar {{ $kamar->nomor_kamar }}" loading="lazy" class="w-full h-full object-cover">
                    </div>
                </div>

                <!-- Detail & Fasilitas -->
                <div class="bg-white border-4 border-black p-6 neo-card-shadow">
                    <h2 class="text-xl sm:text-2xl font-black text-black uppercase tracking-tight mb-6">Spesifikasi & Fasilitas Kamar</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="border-4 border-black bg-white p-4 neo-shadow-sm">
                            <span class="text-gray-500 block text-[10px] font-black uppercase tracking-wider">Tipe Kamar</span>
                            <span class="font-black text-black text-base uppercase tracking-tight">{{ $kamar->tipe }}</span>
                        </div>
                        <div class="border-4 border-black bg-white p-4 neo-shadow-sm">
                            <span class="text-gray-500 block text-[10px] font-black uppercase tracking-wider">Lantai Unit</span>
                            <span class="font-black text-black text-base uppercase tracking-tight">Lantai {{ $kamar->lantai }}</span>
                        </div>
                        <div class="border-4 border-black bg-white p-4 neo-shadow-sm">
                            <span class="text-gray-500 block text-[10px] font-black uppercase tracking-wider">Luas Kamar</span>
                            <span class="font-black text-black text-base uppercase tracking-tight">{{ $kamar->luas_m2 }} m²</span>
                        </div>
                    </div>

                    <h3 class="font-black text-black uppercase tracking-tight text-base mb-3">Deskripsi Kamar</h3>
                    <p class="text-gray-800 text-sm leading-relaxed mb-8 font-semibold">
                        {{ $kamar->deskripsi ?? 'Pilihan kamar hunian yang nyaman dengan sirkulasi udara baik dan pencahayaan alami.' }}
                    </p>

                    <h3 class="font-black text-black uppercase tracking-tight text-base mb-4">Fasilitas Unit</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @forelse($kamar->fasilitas as $item)
                            <div class="flex items-center gap-3 text-xs font-black uppercase tracking-wider text-black bg-yellow-100 p-3 border-2 border-black neo-shadow-sm">
                                <span class="text-lg">
                                    {{ $item->emoji }}
                                </span>
                                <span>{{ $item->nama }}</span>
                            </div>
                        @empty
                            <p class="text-gray-500 text-xs font-bold uppercase tracking-wider col-span-full">Tidak ada fasilitas khusus kamar ini.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Promo Packages Section -->
                <div class="bg-white border-4 border-black p-6 neo-card-shadow">
                    <h2 class="text-xl sm:text-2xl font-black text-black uppercase tracking-tight mb-2">
                        🎁 {{ $promoSectionTitle }}
                    </h2>
                    <p class="text-xs text-gray-500 font-bold uppercase mb-6">
                        {{ $promoSectionSubtitle }}
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($kamar->promo_packages as $pkg)
                            <div class="promo-card border-4 border-black p-5 flex flex-col justify-between transition-all duration-200 hover:-translate-y-1 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] bg-white relative cursor-pointer {{ $pkg['discount'] > 0 ? 'bg-yellow-50/50' : '' }}"
                                 data-type="{{ $pkg['type'] }}"
                                 data-duration="{{ $pkg['duration'] }}">
                                @if($pkg['discount'] > 0)
                                    <span class="absolute -top-3 -right-3 bg-red-500 text-white text-[10px] font-black uppercase px-2.5 py-1 border-2 border-black rotate-12 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                        Hemat {{ $pkg['discount'] }}%
                                    </span>
                                @endif

                                <div class="space-y-3">
                                    <div class="text-[10px] font-black uppercase tracking-wider text-gray-500">
                                        {{ $pkg['type'] === 'harian' ? 'Harian' : ($pkg['type'] === 'mingguan' ? 'Mingguan' : 'Bulanan') }} ({{ $pkg['duration'] }} {{ $pkg['type'] === 'harian' ? 'Hari' : ($pkg['type'] === 'mingguan' ? 'Minggu' : 'Bulan') }})
                                    </div>
                                    <h3 class="text-md font-black text-black uppercase tracking-tight leading-tight">
                                        {{ $pkg['name'] }}
                                    </h3>
                                    <p class="text-[11px] text-gray-600 font-semibold leading-relaxed">
                                        {{ $pkg['desc'] }}
                                    </p>
                                </div>

                                <div class="mt-6 pt-4 border-t-2 border-dashed border-black space-y-3">
                                    <div>
                                        @if($pkg['discount'] > 0)
                                            <span class="text-xs text-red-500 font-bold line-through block">
                                                Rp {{ number_format($pkg['original_price'], 0, ',', '.') }}
                                            </span>
                                            <span class="text-lg font-black text-black">
                                                Rp {{ number_format($pkg['promo_price'], 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="text-lg font-black text-black">
                                                Rp {{ number_format($pkg['original_price'], 0, ',', '.') }}
                                            </span>
                                        @endif
                                        <span class="text-[9px] text-gray-500 font-black uppercase block">
                                            Total Estimasi
                                        </span>
                                    </div>

                                    <button type="button" 
                                            class="btn-pilih-paket w-full text-center py-2 bg-yellow-400 hover:bg-yellow-300 text-black border-2 border-black font-black text-[10px] uppercase tracking-wider neo-shadow-sm transition-all duration-150 active:translate-y-0.5 active:shadow-none">
                                        Pilih Paket Ini →
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Sticky Card Simulasi & Reservasi -->
            <div>
                <div class="bg-white border-4 border-black p-6 sticky top-24 space-y-6 neo-card-shadow">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-black text-black uppercase tracking-tight">Simulasi & Reservasi</h2>
                        <p class="text-xs text-gray-500 font-bold uppercase mt-1">Estimasi biaya sewa kamar Anda secara realtime.</p>
                    </div>

                    @auth
                        @if(auth()->user()->role === 'admin')
                            <!-- Tampilan Admin -->
                            <div class="bg-yellow-100 border-4 border-black p-4 text-black text-xs font-black uppercase tracking-wider leading-relaxed text-center neo-shadow-sm">
                                🔒 Masuk Sebagai Admin<br>
                                Akun admin tidak dapat memesan kamar. Silakan gunakan akun Penyewa.
                            </div>
                        @else
                            <!-- Tampilan Penyewa (Formulir Reservasi Riil) -->
                            <form action="{{ route('penyewa.reservasi.store') }}" method="POST" class="space-y-4" id="form-booking-real">
                                @csrf
                                <input type="hidden" name="kamar_id" value="{{ $kamar->id }}">

                                <div>
                                    <label for="tipe_sewa" class="block text-xs font-black uppercase tracking-wider text-black mb-1">Tipe Sewa</label>
                                    <select id="tipe_sewa" name="tipe_sewa" class="w-full border-4 border-black bg-white px-4 py-3 font-bold text-sm focus:outline-none focus:ring-4 focus:ring-yellow-400 focus:bg-yellow-50 text-black">
                                        <option value="bulanan" {{ request('tipe_sewa', session('reservasi_tipe_sewa', old('tipe_sewa'))) == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                        <option value="mingguan" {{ request('tipe_sewa', session('reservasi_tipe_sewa', old('tipe_sewa'))) == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                                        <option value="harian" {{ request('tipe_sewa', session('reservasi_tipe_sewa', old('tipe_sewa'))) == 'harian' ? 'selected' : '' }}>Harian</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="durasi" class="block text-xs font-black uppercase tracking-wider text-black mb-1">Durasi</label>
                                    <input type="number" id="durasi" name="durasi" min="1" value="{{ request('durasi', session('reservasi_durasi', old('durasi', 1))) }}" class="w-full border-4 border-black bg-white px-4 py-3 font-bold text-sm focus:outline-none focus:ring-4 focus:ring-yellow-400 focus:bg-yellow-50 text-black">
                                </div>

                                <div>
                                    <label for="tanggal_mulai" class="block text-xs font-black uppercase tracking-wider text-black mb-1">Tanggal Mulai</label>
                                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" min="{{ date('Y-m-d') }}" value="{{ request('tanggal_mulai', old('tanggal_mulai', date('Y-m-d'))) }}" class="w-full border-4 border-black bg-white px-4 py-3 font-bold text-sm focus:outline-none focus:ring-4 focus:ring-yellow-400 focus:bg-yellow-50 text-black" required>
                                </div>

                                <div>
                                    <label for="is_dp" class="block text-xs font-black uppercase tracking-wider text-black mb-1">Opsi Pembayaran</label>
                                    <select id="is_dp" name="is_dp" class="w-full border-4 border-black bg-white px-4 py-3 font-bold text-sm focus:outline-none focus:ring-4 focus:ring-yellow-400 focus:bg-yellow-50 text-black">
                                        <option value="0" {{ old('is_dp') == '0' ? 'selected' : '' }}>Bayar Penuh (100%)</option>
                                        <option value="1" {{ old('is_dp') == '1' ? 'selected' : '' }}>Uang Muka / DP (30%)</option>
                                    </select>
                                </div>

                                <!-- Container Error Estimasi -->
                                <div id="estimasi-error-container" class="hidden bg-red-100 border-4 border-red-500 text-red-700 p-3 text-xs font-bold text-center neo-shadow-sm mb-4"></div>

                                <!-- Tampilan Hasil Kalkulasi -->
                                <div id="tampilan-estimasi-harga" class="bg-yellow-100 border-4 border-black p-4 space-y-2 text-xs font-bold text-black neo-shadow-sm">
                                    <div class="flex justify-between">
                                        <span>Harga Pokok:</span>
                                        <span class="font-black">Rp {{ number_format($kamar->harga_bulan, 0, ',', '.') }}/bln</span>
                                    </div>
                                    <div class="flex justify-between border-t-2 border-black pt-2 text-sm font-black">
                                        <span>Estimasi Total:</span>
                                        <span id="total-harga-display">Rp {{ number_format($kamar->harga_bulan, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between border-t-2 border-black pt-2 text-[10px] uppercase font-black text-gray-700">
                                        <span>DP Minimal (30%):</span>
                                        <span id="dp-display">Rp {{ number_format($kamar->harga_bulan * 0.3, 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-300 text-black border-4 border-black font-black py-4 px-4 neo-btn-shadow neo-btn-interactive text-xs uppercase tracking-wider text-center transition-colors">
                                    Pesan Kamar Sekarang →
                                </button>
                            </form>
                        @endif
                    @else
                        <!-- Tampilan Tamu/Guest (Formulir Simulasi + Panel Autentikasi) -->
                        <div class="space-y-4">
                            <form id="form-simulasi" class="space-y-4" onsubmit="event.preventDefault();">
                                <div>
                                    <label for="tipe_sewa" class="block text-xs font-black uppercase tracking-wider text-black mb-1">Tipe Sewa</label>
                                    <select id="tipe_sewa" name="tipe_sewa" class="w-full border-4 border-black bg-white px-4 py-3 font-bold text-sm focus:outline-none focus:ring-4 focus:ring-yellow-400 focus:bg-yellow-50 text-black">
                                        <option value="bulanan" {{ request('tipe_sewa', session('reservasi_tipe_sewa', old('tipe_sewa'))) == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                        <option value="mingguan" {{ request('tipe_sewa', session('reservasi_tipe_sewa', old('tipe_sewa'))) == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                                        <option value="harian" {{ request('tipe_sewa', session('reservasi_tipe_sewa', old('tipe_sewa'))) == 'harian' ? 'selected' : '' }}>Harian</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="durasi" class="block text-xs font-black uppercase tracking-wider text-black mb-1">Durasi</label>
                                    <input type="number" id="durasi" name="durasi" min="1" value="{{ request('durasi', session('reservasi_durasi', old('durasi', 1))) }}" class="w-full border-4 border-black bg-white px-4 py-3 font-bold text-sm focus:outline-none focus:ring-4 focus:ring-yellow-400 focus:bg-yellow-50 text-black">
                                </div>
                            </form>

                            <!-- Container Error Estimasi -->
                             <div id="estimasi-error-container" class="hidden bg-red-100 border-4 border-red-500 text-red-700 p-3 text-xs font-bold text-center neo-shadow-sm mb-4"></div>

                            <!-- Tampilan Hasil Kalkulasi -->
                            <div id="tampilan-estimasi-harga" class="bg-yellow-100 border-4 border-black p-4 space-y-2 text-xs font-bold text-black neo-shadow-sm">
                                <div class="flex justify-between">
                                    <span>Harga Pokok:</span>
                                    <span class="font-black">Rp {{ number_format($kamar->harga_bulan, 0, ',', '.') }}/bln</span>
                                </div>
                                <div class="flex justify-between border-t-2 border-black pt-2 text-sm font-black">
                                    <span>Estimasi Total:</span>
                                    <span id="total-harga-display">Rp {{ number_format($kamar->harga_bulan, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between border-t-2 border-black pt-2 text-[10px] uppercase font-black text-gray-700">
                                    <span>DP Minimal (30%):</span>
                                    <span id="dp-display">Rp {{ number_format($kamar->harga_bulan * 0.3, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <!-- Panel Autentikasi Brutalist -->
                            <div class="border-4 border-black bg-yellow-100 p-5 space-y-4 text-center mt-6">
                                <div class="text-sm font-black uppercase tracking-wider text-black">
                                    Masuk untuk Melakukan Reservasi
                                </div>
                                <p class="text-xs text-black font-semibold leading-relaxed">
                                    Silakan masuk menggunakan akun Anda atau mendaftar untuk memesan Kamar secara instan.
                                </p>
                                
                                <!-- Google Sign-in -->
                                <a id="btn-google-login" href="{{ route('auth.google') }}" class="flex items-center justify-center gap-2.5 w-full py-3 bg-white border-4 border-black text-black text-xs font-black uppercase tracking-wider neo-btn-shadow neo-btn-interactive">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
                                    </svg>
                                    <span>Masuk dengan Google</span>
                                </a>

                                <div class="relative flex items-center justify-center my-3">
                                    <span class="absolute inset-x-0 h-[2px] bg-black"></span>
                                    <span class="relative bg-yellow-100 px-2 text-[10px] text-black font-black uppercase tracking-wider">ATAU</span>
                                </div>

                                <!-- Manual Login/Register Buttons -->
                                <div class="grid grid-cols-2 gap-3">
                                    <a id="btn-manual-login" href="{{ route('reservasi.login') }}" class="py-2.5 bg-yellow-400 text-black border-4 border-black text-xs font-black uppercase tracking-wider text-center neo-btn-shadow neo-btn-interactive">
                                        Masuk
                                    </a>
                                    <a id="btn-manual-register" href="{{ route('reservasi.register') }}" class="py-2.5 bg-white text-black border-4 border-black text-xs font-black uppercase tracking-wider text-center neo-btn-shadow neo-btn-interactive">
                                        Daftar
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>

        </div>
    </div>
</div>

<script>
window.pilihPaket = function(type, duration, element, event) {
    const isLoggedIn = @json(auth()->check());
    if (!isLoggedIn) {
        if (element) {
            element.classList.remove('bg-yellow-400', 'hover:bg-yellow-300');
            element.classList.add('bg-red-500', 'text-white', 'translate-y-1', 'shadow-none');
            element.innerText = 'Wajib Login...';
        }

        const alertBox = document.createElement('div');
        alertBox.className = 'fixed top-10 left-1/2 -translate-x-1/2 bg-yellow-400 border-4 border-black p-5 text-black font-black text-xs sm:text-sm z-50 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] uppercase tracking-wider animate-bounce';
        alertBox.innerHTML = '⚠️ Anda wajib masuk / login terlebih dahulu sebelum melakukan reservasi!';
        document.body.appendChild(alertBox);

        setTimeout(() => {
            window.location.href = "{{ route('reservasi.login') }}?tipe_sewa=" + encodeURIComponent(type) + "&durasi=" + encodeURIComponent(duration);
        }, 1200);
        return;
    }

    const tipeSewaSelect = document.getElementById('tipe_sewa');
    const durasiInput = document.getElementById('durasi');

    if (tipeSewaSelect && durasiInput) {
        tipeSewaSelect.value = type;
        durasiInput.value = duration;

        tipeSewaSelect.dispatchEvent(new Event('change'));
        durasiInput.dispatchEvent(new Event('input'));

        const formCard = tipeSewaSelect.closest('.neo-card-shadow');
        if (formCard) {
            formCard.scrollIntoView({ behavior: 'smooth', block: 'center' });

            let blinks = 0;
            const blinkInterval = setInterval(() => {
                formCard.style.backgroundColor = blinks % 2 === 0 ? '#FACC15' : '#FFFFFF';
                blinks++;
                if (blinks >= 6) {
                    clearInterval(blinkInterval);
                    formCard.style.backgroundColor = '';
                }
            }, 150);

            formCard.classList.add('ring-4', 'ring-yellow-400', 'scale-[1.02]');
            setTimeout(() => {
                formCard.classList.remove('ring-4', 'ring-yellow-400', 'scale-[1.02]');
            }, 1000);
        }
    }
};

document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('click', function(event) {
        const card = event.target.closest('.promo-card');
        if (card) {
            event.preventDefault();
            const type = card.getAttribute('data-type');
            const duration = parseInt(card.getAttribute('data-duration'));
            const btn = card.querySelector('.btn-pilih-paket');
            
            pilihPaket(type, duration, btn, event);
        }
    });

    const bookingFormReal = document.getElementById('form-booking-real');
    if (bookingFormReal) {
        bookingFormReal.addEventListener('submit', function() {
            const submitBtn = bookingFormReal.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                submitBtn.innerText = 'Memproses Reservasi...';
            }
        });
    }

    const tipeSewaSelect = document.getElementById('tipe_sewa');
    const durasiInput = document.getElementById('durasi');
    const totalDisplay = document.getElementById('total-harga-display');
    const dpDisplay = document.getElementById('dp-display');
    const errorContainer = document.getElementById('estimasi-error-container');

    let priceEstimationAbortController = null;

    function hitungEstimasi() {
        if (!tipeSewaSelect || !durasiInput) return;

        const tipeSewa = tipeSewaSelect.value;
        const durasi = durasiInput.value;

        if (priceEstimationAbortController) {
            priceEstimationAbortController.abort();
        }
        priceEstimationAbortController = new AbortController();

        const submitBtn = document.querySelector('form button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitBtn.innerText = 'Menghitung Estimasi...';
        }

        if (totalDisplay) {
            totalDisplay.innerHTML = `<span class="animate-pulse bg-yellow-300/60 h-5 w-28 inline-block border border-black/10"></span>`;
        }
        if (dpDisplay) {
            dpDisplay.innerHTML = `<span class="animate-pulse bg-yellow-300/40 h-4 w-20 inline-block border border-black/10"></span>`;
        }

        fetch('{{ route('landing.hitungHarga', $kamar->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                tipe_sewa: tipeSewa,
                durasi: durasi
            }),
            signal: priceEstimationAbortController.signal
        })
        .then(async response => {
            const data = await response.json().catch(() => null);
            if (!response.ok || !data) {
                throw new Error((data && data.message) ? data.message : `Gagal menghitung estimasi harga (HTTP ${response.status}).`);
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                if (submitBtn) {
                    submitBtn.classList.remove('hidden');
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    submitBtn.innerText = 'Pesan Kamar Sekarang →';
                }

                const formattedPrice = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    maximumFractionDigits: 0
                }).format(data.total_harga);

                const formattedDp = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    maximumFractionDigits: 0
                }).format(data.dp_minimal);
                
                if (totalDisplay) totalDisplay.textContent = formattedPrice;
                if (dpDisplay) dpDisplay.textContent = formattedDp;
                if (errorContainer) {
                    errorContainer.classList.add('hidden');
                    errorContainer.textContent = '';
                }
            } else {
                throw new Error(data.message || 'Gagal menghitung estimasi harga.');
            }
        })
        .catch(error => {
            if (error.name === 'AbortError') return;
            console.error('Error calculating price estimation:', error);

            if (submitBtn) submitBtn.classList.add('hidden');
            if (errorContainer) {
                errorContainer.classList.remove('hidden');
                errorContainer.textContent = error.message || 'Gagal menghitung estimasi harga.';
            }
            if (totalDisplay) totalDisplay.textContent = 'Rp -';
            if (dpDisplay) dpDisplay.textContent = 'Rp -';
        });
    }

    const googleLoginBtn = document.getElementById('btn-google-login');
    const manualLoginBtn = document.getElementById('btn-manual-login');
    const manualRegisterBtn = document.getElementById('btn-manual-register');

    function updateAuthUrls() {
        if (!tipeSewaSelect || !durasiInput) return;
        const tipeSewa = tipeSewaSelect.value;
        const durasi = durasiInput.value;
        const params = `?tipe_sewa=${encodeURIComponent(tipeSewa)}&durasi=${encodeURIComponent(durasi)}`;

        if (googleLoginBtn) googleLoginBtn.setAttribute('href', "{{ route('auth.google') }}" + params);
        if (manualLoginBtn) manualLoginBtn.setAttribute('href', "{{ route('reservasi.login') }}" + params);
        if (manualRegisterBtn) manualRegisterBtn.setAttribute('href', "{{ route('reservasi.register') }}" + params);
    }

    if (tipeSewaSelect) {
        tipeSewaSelect.addEventListener('change', function() {
            hitungEstimasi();
            updateAuthUrls();
        });
    }
    if (durasiInput) {
        durasiInput.addEventListener('input', function() {
            hitungEstimasi();
            updateAuthUrls();
        });
    }

    hitungEstimasi();
    updateAuthUrls();
});
</script>
@endsection
