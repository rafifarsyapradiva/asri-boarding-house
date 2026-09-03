<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Services\PdfNotaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TagihanController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(protected PdfNotaService $pdfNotaService)
    {
    }

    /**
     * Display a listing of the resource for tenant.
     */
    public function index(): View
    {
        $penyewaId = Auth::user()->penyewa?->id;
        
        if ($penyewaId) {
            // Optimasi: Agregasi langsung via Kueri SQL tanpa memuat seluruh baris ke RAM
            $stats = Tagihan::where('penyewa_id', $penyewaId)
                ->selectRaw("
                    SUM(CASE WHEN status IN ('pending', 'terlambat') THEN 1 ELSE 0 END) as unpaid_count,
                    SUM(CASE WHEN status IN ('pending', 'terlambat') THEN nominal_total ELSE 0 END) as unpaid_nominal,
                    SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as paid_count,
                    SUM(CASE WHEN status = 'lunas' THEN nominal_total ELSE 0 END) as paid_nominal
                ")->first();

            $unpaidCount   = (int) ($stats->unpaid_count ?? 0);
            $unpaidNominal = (float) ($stats->unpaid_nominal ?? 0);
            $paidCount     = (int) ($stats->paid_count ?? 0);
            $paidNominal   = (float) ($stats->paid_nominal ?? 0);

            $tagihan = Tagihan::where('penyewa_id', $penyewaId)
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString();
        } else {
            $tagihan = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            $unpaidCount = $unpaidNominal = $paidCount = $paidNominal = 0;
        }

        return view('penyewa.tagihan.index', compact(
            'tagihan', 
            'unpaidCount',
            'unpaidNominal',
            'paidCount',
            'paidNominal'
        ));
    }

    /**
     * Display the specified resource for tenant.
     */
    public function show(Tagihan $tagihan): View
    {
        // Refaktor Otorisasi: Menggunakan TagihanPolicy
        $this->authorize('view', $tagihan);

        $tagihan->load(['penyewa.kamar', 'pembayaran']);
        return view('penyewa.tagihan.show', compact('tagihan'));
    }

    /**
     * Download the invoice PDF.
     */
    public function downloadNota(Pembayaran $pembayaran)
    {
        if (!$pembayaran->tagihan) {
            abort(404, 'Data tagihan untuk pembayaran ini tidak ditemukan.');
        }

        // Refaktor Otorisasi: Menggunakan PembayaranPolicy (mengatur admin & kepemilikan tagihan)
        $this->authorize('downloadNota', $pembayaran);

        // Refaktor: Pengecekan penyimpanan & pembuatan nota didelegasikan sepenuhnya ke service
        $pdfPath = $this->pdfNotaService->getOrGeneratePdfPath($pembayaran);

        return Storage::download($pdfPath);
    }
}
