<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TagihanController extends Controller
{
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
     * Tampilkan halaman cetak nota pembayaran (client-side PDF rendering).
     * PDF di-generate di browser penyewa menggunakan html2pdf.js — zero server load.
     */
    public function cetakNota(Pembayaran $pembayaran): View
    {
        if (!$pembayaran->tagihan) {
            abort(404, 'Data tagihan untuk pembayaran ini tidak ditemukan.');
        }

        // Otorisasi: penyewa hanya bisa cetak nota miliknya sendiri
        $this->authorize('downloadNota', $pembayaran);

        $pembayaran->load(['tagihan.penyewa.user', 'tagihan.penyewa.kamar']);
        $tagihan  = $pembayaran->tagihan;
        $penyewa  = $tagihan->penyewa;

        $nominalDeposit  = $tagihan->nominal_total - $tagihan->nominal_pokok - $tagihan->nominal_denda;
        $appName         = Setting::get('logo_text', 'Asri Boarding House');
        $contactAddress  = Setting::get('contact_address');
        $contactWhatsapp = Setting::get('contact_whatsapp');

        return view('nota.cetak', compact(
            'pembayaran', 'tagihan', 'penyewa',
            'nominalDeposit', 'appName', 'contactAddress', 'contactWhatsapp'
        ));
    }
}
