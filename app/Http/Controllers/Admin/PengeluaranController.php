<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengeluaran;
use App\Http\Requests\Admin\StorePengeluaranRequest;
use App\Http\Requests\Admin\UpdatePengeluaranRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PengeluaranController extends Controller
{
    /**
     * Get filtered query for Pengeluaran.
     */
    private function getFilteredQuery(Request $request)
    {
        $query = Pengeluaran::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_pengeluaran', 'like', '%' . $request->search . '%')
                  ->orWhere('keterangan', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_pengeluaran', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal_pengeluaran', '<=', $request->tanggal_selesai);
        }

        return $query;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $pengeluaran = $this->getFilteredQuery($request)
                            ->orderBy('tanggal_pengeluaran', 'desc')
                            ->paginate(10)
                            ->withQueryString();

        return view('admin.pengeluaran.index', compact('pengeluaran'));
    }

    /**
     * Export pengeluaran to PDF.
     */
    public function exportPdf(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $search = $request->search;
        $kategori = $request->kategori;
        $tanggal_mulai = $request->tanggal_mulai;
        $tanggal_selesai = $request->tanggal_selesai;

        $pengeluaran = $this->getFilteredQuery($request)
                            ->orderBy('tanggal_pengeluaran', 'desc')
                            ->get();

        // Hitung total pengeluaran
        $totalPengeluaran = $pengeluaran->sum('nominal');

        // Susun teks filter secara server-side
        $filterKategori = $kategori ? ucfirst($kategori) : 'Semua Kategori';
        $filterTanggal = 'Semua Periode';
        if ($tanggal_mulai && $tanggal_selesai) {
            $filterTanggal = \Carbon\Carbon::parse($tanggal_mulai)->translatedFormat('d M Y') . ' s.d. ' . \Carbon\Carbon::parse($tanggal_selesai)->translatedFormat('d M Y');
        } elseif ($tanggal_mulai) {
            $filterTanggal = 'Mulai ' . \Carbon\Carbon::parse($tanggal_mulai)->translatedFormat('d M Y');
        } elseif ($tanggal_selesai) {
            $filterTanggal = 'S.d. ' . \Carbon\Carbon::parse($tanggal_selesai)->translatedFormat('d M Y');
        }

        $appName = \App\Models\Setting::get('logo_text', 'Asri Boarding House');

        $html = view('pdf.laporan-pengeluaran', compact(
            'pengeluaran', 'search', 'filterKategori', 'filterTanggal', 'totalPengeluaran', 'appName'
        ))->render();

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return response()->streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, 'laporan-pengeluaran.pdf', [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="laporan-pengeluaran.pdf"',
        ]);
    }

    /**
     * Export pengeluaran to Excel (CSV).
     */
    public function exportExcel(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $pengeluaran = $this->getFilteredQuery($request)
                            ->orderBy('tanggal_pengeluaran', 'desc')
                            ->get();

        return response()->streamDownload(function () use ($pengeluaran, $request) {
            // Add UTF-8 BOM
            echo "\xEF\xBB\xBF";

            $handle = fopen('php://output', 'w');

            // Header & Metadata
            fputcsv($handle, ["LAPORAN PENGELUARAN OPERASIONAL KOST"]);
            fputcsv($handle, ["Tanggal Cetak", now()->format('d-m-Y H:i:s')]);
            fputcsv($handle, ["Filter - Kata Kunci", $request->search ?: 'Semua']);
            fputcsv($handle, ["Filter - Kategori", $request->kategori ? ucfirst(strtolower($request->kategori)) : 'Semua']);
            fputcsv($handle, ["Filter - Periode", ($request->tanggal_mulai ?: 'Awal') . ' s/d ' . ($request->tanggal_selesai ?: 'Akhir')]);
            fputcsv($handle, []);

            // Header Row
            fputcsv($handle, ["ID", "Tanggal", "Nama Pengeluaran", "Kategori", "Nominal (Rp)", "Keterangan"]);

            // Data Rows
            foreach ($pengeluaran as $p) {
                fputcsv($handle, [
                    $p->id,
                    $p->tanggal_pengeluaran ? $p->tanggal_pengeluaran->format('Y-m-d') : '-',
                    $p->nama_pengeluaran ?? '-',
                    strtoupper($p->kategori ?? ''),
                    (int) ($p->nominal ?? 0),
                    $p->keterangan ?: '-'
                ]);
            }

            // Summary Row
            fputcsv($handle, []);
            fputcsv($handle, ["Total Pengeluaran", "", "", "", (int) ($pengeluaran->sum('nominal') ?? 0), ""]);

            fclose($handle);
        }, 'laporan-pengeluaran.csv', [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="laporan-pengeluaran.csv"',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.pengeluaran.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePengeluaranRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('bukti_nota')) {
            $file = $request->file('bukti_nota');
            $path = $file->store('nota_pengeluaran', 'public');
            $data['bukti_nota'] = $path;
        }

        Pengeluaran::create($data);

        return redirect()->route('admin.pengeluaran.index')
            ->with('success', 'Pencatatan pengeluaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pengeluaran $pengeluaran): View
    {
        return view('admin.pengeluaran.show', compact('pengeluaran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pengeluaran $pengeluaran): View
    {
        return view('admin.pengeluaran.edit', compact('pengeluaran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePengeluaranRequest $request, Pengeluaran $pengeluaran): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('bukti_nota')) {
            // Delete old file if exists
            if ($pengeluaran->bukti_nota && Storage::disk('public')->exists($pengeluaran->bukti_nota)) {
                Storage::disk('public')->delete($pengeluaran->bukti_nota);
            }

            $file = $request->file('bukti_nota');
            $path = $file->store('nota_pengeluaran', 'public');
            $data['bukti_nota'] = $path;
        } else {
            unset($data['bukti_nota']);
        }

        $pengeluaran->update($data);

        return redirect()->route('admin.pengeluaran.index')
            ->with('success', 'Pencatatan pengeluaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pengeluaran $pengeluaran): RedirectResponse
    {
        if ($pengeluaran->bukti_nota && Storage::disk('public')->exists($pengeluaran->bukti_nota)) {
            Storage::disk('public')->delete($pengeluaran->bukti_nota);
        }

        $pengeluaran->delete();

        return redirect()->route('admin.pengeluaran.index')
            ->with('success', 'Pencatatan pengeluaran berhasil dihapus.');
    }
}
