<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Http\Requests\KonfirmasiCashRequest;
use App\Services\TagihanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Throwable;

class TagihanController extends Controller
{
    public function __construct(
        protected TagihanService $tagihanService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $tagihan = $this->tagihanService->getFilteredPaginatedTagihan($request->all());

        return view('admin.tagihan.index', compact('tagihan'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Tagihan $tagihan): View
    {
        $tagihan->load(['penyewa.user', 'penyewa.kamar', 'pembayaran.dikonfirmasiOleh']);
        
        return view('admin.tagihan.show', compact('tagihan'));
    }

    /**
     * Konfirmasi pembayaran cash secara manual oleh admin.
     */
    public function konfirmasiCash(KonfirmasiCashRequest $request, Tagihan $tagihan): RedirectResponse
    {
        if (! $tagihan->canBeConfirmedManually()) {
            return back()->with('error', 'Tagihan sudah lunas atau tidak valid.');
        }

        try {
            $this->tagihanService->confirmCashPayment(
                tagihan: $tagihan,
                adminId: (int) auth()->id(),
                catatan: $request->input('catatan')
            );

            return redirect()
                ->route('admin.tagihan.show', $tagihan)
                ->with('success', 'Pembayaran cash dikonfirmasi.');
        } catch (Throwable $e) {
            report($e);
            return back()->with('error', 'Gagal mengonfirmasi pembayaran: ' . $e->getMessage());
        }
    }
}
