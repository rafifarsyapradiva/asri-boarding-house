<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservasiRequest;
use App\Models\Reservasi;
use App\Models\Tagihan;
use App\Services\ReservasiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class ReservasiController extends Controller
{
    /**
     * Inject the ReservasiService dependency.
     */
    public function __construct(protected ReservasiService $reservasiService)
    {
    }

    /**
     * Display a listing of the tenant's reservations.
     */
    public function index(): View
    {
        $user = Auth::user();

        // Hitung statistik langsung via SQL raw select agar hemat memori
        $stats = $user->reservasi()
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status IN ('dp', 'lunas', 'dikonfirmasi') THEN 1 ELSE 0 END) as aktif,
                SUM(CASE WHEN status = 'batal' THEN 1 ELSE 0 END) as batal
            ")
            ->first();

        $totalPemesanan = $stats->total ?? 0;
        $menungguBayar  = $stats->pending ?? 0;
        $pemesananAktif = $stats->aktif ?? 0;
        $pemesananBatal = $stats->batal ?? 0;

        $reservasi = $user->reservasi()->with('kamar')->latest()->paginate(10)->withQueryString();

        return view('reservasi.index', compact(
            'reservasi', 
            'totalPemesanan', 
            'menungguBayar', 
            'pemesananAktif', 
            'pemesananBatal'
        ));
    }

    /**
     * Store a newly created reservation in storage.
     */
    public function store(StoreReservasiRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        try {
            $reservasi = $this->reservasiService->buatReservasi($data);

            return redirect()
                ->route('penyewa.reservasi.pembayaran', $reservasi->id)
                ->with('success', 'Reservasi berhasil dibuat! Silakan lakukan pembayaran.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['tanggal_mulai' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the reservation payment / detail page.
     * Otomatis sinkronisasi status pembayaran dari Midtrans API jika masih pending,
     * sebagai fallback apabila webhook Midtrans gagal terkirim (misal: ngrok timeout).
     */
    public function show(Reservasi $reservasi): View|RedirectResponse
    {
        $this->authorize('view', $reservasi);

        // Jika penyewa sudah di-checkout oleh admin (status nonaktif) dan reservasi ini
        // bukan lagi dalam proses (bukan pending/dp), arahkan ke dashboard reservasi
        // agar penyewa bisa melakukan reservasi baru. Ini mencegah halaman "nyangkut".
        $user = Auth::user();
        $penyewa = $user->penyewa;
        $isCheckedOut = $penyewa && $penyewa->status === 'nonaktif';
        // 'lunas' adalah status aktif menunggu konfirmasi admin, bukan completed.
        $reservasiIsCompleted = in_array($reservasi->status, ['dikonfirmasi', 'batal', 'gagal', 'selesai']);

        if ($isCheckedOut && $reservasiIsCompleted) {
            return redirect()
                ->route('penyewa.reservasi.dashboard')
                ->with('info', 'Masa sewa Anda telah selesai. Silakan buat reservasi baru untuk menyewa kembali.');
        }

        // FALLBACK: Jika status masih pending dan sudah ada snap_token,
        // cek langsung ke Midtrans API apakah pembayaran sudah berhasil.
        // Bisa dinonaktifkan via MIDTRANS_SYNC_FALLBACK_ENABLED=false di .env
        // untuk keperluan testing webhook secara mandiri.
        if ($reservasi->status === 'pending' && $reservasi->order_id && config('midtrans.sync_fallback_enabled', true)) {
            try {
                $this->syncStatusFromMidtrans($reservasi);
                $reservasi->refresh(); // Reload data terbaru dari database
            } catch (\Exception $e) {
                // Gagal sinkronisasi tidak boleh menghalangi halaman terbuka
                Log::warning('Gagal sinkronisasi status Midtrans saat show: ' . $e->getMessage(), [
                    'reservasi_id' => $reservasi->id,
                ]);
            }
        }

        $clientKey = config('midtrans.client_key');

        return view('reservasi.show', compact('reservasi', 'clientKey'));
    }

    /**
     * Sinkronisasi status reservasi dengan mengecek langsung ke Midtrans Transaction Status API.
     * Dipanggil sebagai fallback ketika webhook tidak terkirim (misal: ngrok timeout).
     *
     * Keamanan setara dengan Webhook controller:
     * 1. Validasi Gross Amount — uang yang dibayar harus cocok dengan tagihan
     * 2. Cek Double-Booking — kamar tidak boleh sudah dipesan orang lain pada tanggal yang sama
     * 3. DB Transaction + Row Lock — mencegah race condition jika request masuk secara bersamaan
     */
    private function syncStatusFromMidtrans(Reservasi $reservasi): void
    {
        $orderId   = $reservasi->order_id;
        $serverKey = config('midtrans.server_key');

        $baseUrl = config('midtrans.is_production')
            ? "https://api.midtrans.com/v2/{$orderId}/status"
            : "https://api.sandbox.midtrans.com/v2/{$orderId}/status";

        // Tanya langsung ke Midtrans API dengan timeout ketat (5 detik)
        $response = Http::withBasicAuth($serverKey, '')
            ->timeout(5)
            ->get($baseUrl);

        if (!$response->successful()) {
            return;
        }

        $txStatus    = $response->json('transaction_status');
        $grossAmount = $response->json('gross_amount');
        $transactionId = $response->json('transaction_id');

        // Hanya proses jika Midtrans konfirmasi pembayaran berhasil
        if (!in_array($txStatus, ['settlement', 'capture'])) {
            if (in_array($txStatus, ['deny', 'cancel', 'expire'])) {
                $reservasi->update(['status' => 'batal']);
            }
            return;
        }

        // Jalankan seluruh validasi & update di dalam DB Transaction dengan Row Lock
        \Illuminate\Support\Facades\DB::transaction(function () use ($reservasi, $grossAmount, $transactionId, $txStatus, $orderId) {
            // LAYER 3: Row Lock — kunci baris di DB agar tidak ada proses lain yang bisa
            // membaca/menulis data reservasi ini secara bersamaan (mencegah race condition)
            $locked = Reservasi::where('id', $reservasi->id)->lockForUpdate()->first();

            if (!$locked) {
                return;
            }

            // IDEMPOTENCY: Jika sudah diproses (misal oleh webhook yang datang terlambat), hentikan
            if (in_array($locked->status, ['dp', 'lunas', 'dikonfirmasi'])) {
                return;
            }

            // LAYER 1: VALIDASI GROSS AMOUNT
            // Uang yang dibayar harus persis sama dengan tagihan yang tersimpan di database.
            // Mencegah bug jika admin mengubah harga kamar SETELAH penyewa sudah klik bayar.
            $expectedAmount = $locked->is_dp ? $locked->nominal_dp : $locked->total_harga;
            if (abs((float) $grossAmount - (float) $expectedAmount) > 0.01) {
                Log::error('Midtrans fallback sync: gross_amount TIDAK COCOK dengan tagihan database', [
                    'reservasi_id'    => $locked->id,
                    'order_id'        => $orderId,
                    'dibayar_midtrans' => $grossAmount,
                    'tagihan_database' => $expectedAmount,
                ]);
                // Jangan update status — biarkan admin menangani secara manual
                return;
            }

            // LAYER 2: CEK DOUBLE-BOOKING
            // Pastikan tidak ada reservasi lain yang sudah 'dikonfirmasi/dp/lunas' untuk
            // kamar yang sama pada rentang tanggal yang tumpang-tindih.
            $hasDoubleBooking = Reservasi::isKamarTerbooking(
                $locked->kamar_id,
                $locked->tanggal_mulai->toDateString(),
                $locked->tanggal_selesai->toDateString(),
                $locked->id // Kecualikan diri sendiri dari pengecekan
            );

            if ($hasDoubleBooking) {
                Log::error('Midtrans fallback sync: Double-booking terdeteksi saat konfirmasi pembayaran', [
                    'reservasi_id' => $locked->id,
                    'order_id'     => $orderId,
                    'kamar_id'     => $locked->kamar_id,
                ]);
                $locked->update([
                    'status'          => 'batal',
                    'transaction_id'  => $transactionId,
                    'catatan_admin'   => 'Double-booking terdeteksi saat sinkronisasi status Midtrans (fallback). Diperlukan refund manual oleh admin.',
                ]);
                return;
            }

            // Semua validasi lolos — update status menjadi dp/lunas
            $statusTarget = $locked->is_dp ? 'dp' : 'lunas';
            $locked->update([
                'status'             => $statusTarget,
                'metode_pembayaran'  => 'midtrans',
                'tanggal_konfirmasi' => now(),
                'transaction_id'     => $transactionId,
            ]);

            event(new \App\Events\ReservasiDibayar($locked));

            Log::info('Midtrans fallback sync: status berhasil diperbarui (dengan validasi penuh)', [
                'reservasi_id' => $locked->id,
                'order_id'     => $orderId,
                'new_status'   => $statusTarget,
            ]);
        });
    }

    /**
     * Cancel the reservation by the tenant.
     */
    public function batal(Reservasi $reservasi): RedirectResponse
    {
        $this->authorize('update', $reservasi);

        if ($reservasi->status !== 'pending') {
            return redirect()
                ->back()
                ->with('error', 'Hanya reservasi berstatus PENDING yang dapat dibatalkan.');
        }

        $this->reservasiService->batalkanReservasi($reservasi);

        return redirect()
                ->route('landing.index')
                ->with('success', 'Reservasi Anda telah berhasil dibatalkan.');
    }

    /**
     * Display the tenant's payment history.
     */
    public function riwayatPembayaran(): View
    {
        $user = Auth::user();
        $penyewaId = $user->penyewa?->id;

        // 1. Koleksi untuk statistik pembayaran
        $allReservasi = $user->reservasi()->latest()->get();
        $allTagihan = $penyewaId 
            ? Tagihan::where('penyewa_id', $penyewaId)->with('pembayaran')->orderBy('created_at', 'desc')->get()
            : collect();

        // Hitung statistik untuk view
        $totalBookingPaid = $allReservasi->whereIn('status', ['dp', 'lunas', 'dikonfirmasi'])->reduce(function($carry, $item) {
            return $carry + ($item->is_dp ? (float)$item->nominal_dp : (float)$item->total_harga);
        }, 0);

        $totalBillsPaid = $allTagihan->where('status', 'lunas')->reject(function($t) {
            return str_contains($t->keterangan ?? '', 'Full Payment');
        })->sum('nominal_total');

        $successTransCount = $allReservasi->whereIn('status', ['dp', 'lunas', 'dikonfirmasi'])->count() + 
            $allTagihan->where('status', 'lunas')->reject(function($t) {
                return str_contains($t->keterangan ?? '', 'Full Payment');
            })->count();

        // 2. Paginated listings
        $reservasi = $user->reservasi()
            ->with('kamar')
            ->latest()
            ->paginate(10, ['*'], 'page_reservasi')
            ->withQueryString();

        if ($penyewaId) {
            $tagihan = Tagihan::where('penyewa_id', $penyewaId)
                ->with(['penyewa.kamar', 'pembayaran'])
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'page_tagihan')
                ->withQueryString();
        } else {
            $tagihan = new LengthAwarePaginator([], 0, 10, 1, [
                'path' => request()->url(),
                'query' => request()->query(),
                'pageName' => 'page_tagihan'
            ]);
        }

        return view('penyewa.riwayat-pembayaran', compact(
            'allReservasi', 
            'allTagihan', 
            'reservasi', 
            'tagihan',
            'totalBookingPaid',
            'totalBillsPaid',
            'successTransCount'
        ));
    }

    /**
     * Display the dedicated chat page for a reservation.
     */
    public function chat(Reservasi $reservasi): View
    {
        $this->authorize('chat', $reservasi);

        return view('penyewa.chat', compact('reservasi'));
    }

    /**
     * Display a listing of all chat rooms (reservations).
     */
    public function riwayatChat(): View
    {
        $user = Auth::user();
        
        $reservasi = $user->reservasi()
            ->with(['kamar', 'latestChatMessage.sender'])
            ->latest()
            ->get();

        $totalChatRooms = $reservasi->count();
        $activeChatRooms = $reservasi->filter(fn($item) => in_array($item->status, ['pending', 'dp', 'lunas']))->count();

        return view('penyewa.riwayat-chat', compact('reservasi', 'totalChatRooms', 'activeChatRooms'));
    }
}
