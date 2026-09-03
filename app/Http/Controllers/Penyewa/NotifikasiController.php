<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Models\LogNotifikasi;
use App\Services\NotifikasiService;
use Illuminate\View\View;
use Illuminate\Pagination\LengthAwarePaginator;

class NotifikasiController extends Controller
{
    /**
     * Inject NotifikasiService dependency.
     */
    public function __construct(protected NotifikasiService $notifikasiService)
    {
    }

    /**
     * Tampilkan riwayat notifikasi untuk penyewa yang sedang login dengan eager loading.
     */
    public function index(): View
    {
        $penyewa = auth()->user()?->penyewa;
        $penyewaId = $penyewa?->id;
        
        // Optimasi: Gunakan Eager Loading untuk relasi tagihan dan pembayaran
        $logs = $penyewaId 
            ? LogNotifikasi::with(['tagihan.pembayaran'])
                ->where('penyewa_id', $penyewaId)
                ->latest('id')
                ->paginate(10)
                ->withQueryString()
            : new LengthAwarePaginator([], 0, 10);

        if ($penyewa && $logs->count() > 0) {
            $logs->getCollection()->transform(function ($log) use ($penyewa) {
                if (empty($log->pesan)) {
                    $log->pesan = $this->notifikasiService->formatLogMessage($log, $penyewa);
                }
                return $log;
            });
        }

        return view('penyewa.notifikasi.index', compact('logs'));
    }
}
