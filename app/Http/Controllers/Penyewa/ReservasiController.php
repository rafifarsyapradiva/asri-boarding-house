<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservasiRequest;
use App\Models\Reservasi;
use App\Models\Tagihan;
use App\Services\ReservasiService;
use Illuminate\Support\Facades\Auth;
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
     */
    public function show(Reservasi $reservasi): View
    {
        $this->authorize('view', $reservasi);
        $clientKey = config('midtrans.client_key');

        return view('reservasi.show', compact('reservasi', 'clientKey'));
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
