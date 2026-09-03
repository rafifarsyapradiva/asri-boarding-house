<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\Reservasi;
use App\Services\PdfGeneratorInterface;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(protected PdfGeneratorInterface $pdfGenerator)
    {
    }

    /**
     * Helper to get filtered tagihan query builder based on request parameters.
     */
    private function getTagihanQuery(Request $request)
    {
        $query = Tagihan::with(['penyewa.user', 'penyewa.kamar']);

        // Filter periode bulan
        if ($request->filled('bulan')) {
            $query->where('periode_bulan', $request->bulan);
        }

        // Filter periode tahun
        if ($request->filled('tahun')) {
            $query->where('periode_tahun', $request->tahun);
        }

        // Filter status tagihan
        if ($request->filled('status')) {
            if ($request->status === 'terlambat') {
                $query->where('status', 'pending')
                      ->whereDate('tanggal_jatuh_tempo', '<', today())
                      ->where('bulan_keterlambatan', '>', 0);
            } elseif ($request->status === 'pending') {
                $query->where('status', 'pending')
                      ->where(function ($q) {
                          $q->whereDate('tanggal_jatuh_tempo', '>=', today())
                            ->orWhere('bulan_keterlambatan', 0);
                      });
            } else {
                $query->where('status', $request->status);
            }
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Helper to get filtered tagihan list based on request parameters.
     */
    private function getFilteredTagihan(Request $request)
    {
        return $this->getTagihanQuery($request)->get();
    }

    /**
     * Helper to get filtered pengeluaran query builder based on request parameters.
     */
    private function getPengeluaranQuery(Request $request)
    {
        $query = \App\Models\Pengeluaran::query();

        // Filter periode bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_pengeluaran', $request->bulan);
        }

        // Filter periode tahun
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_pengeluaran', $request->tahun);
        }

        return $query->orderBy('tanggal_pengeluaran', 'desc');
    }

    /**
     * Helper to get filtered pengeluaran list based on request parameters.
     */
    private function getFilteredPengeluaran(Request $request)
    {
        return $this->getPengeluaranQuery($request)->get();
    }

    /**
     * Helper to get filtered tagihan list as a cursor.
     */
    private function getFilteredTagihanCursor(Request $request)
    {
        return $this->getTagihanQuery($request)->cursor();
    }

    /**
     * Helper to get filtered pengeluaran list as a cursor.
     */
    private function getFilteredPengeluaranCursor(Request $request)
    {
        return $this->getPengeluaranQuery($request)->cursor();
    }

    /**
     * Helper to calculate cash flow summary (total masuk, keluar, saldo bersih).
     */
    private function calculateCashFlowSummary(Request $request): array
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $tagihanAllPeriod = Tagihan::query();
        if ($bulan) {
            $tagihanAllPeriod->where('periode_bulan', $bulan);
        }
        if ($tahun) {
            $tagihanAllPeriod->where('periode_tahun', $tahun);
        }
        $totalMasukPokok = $tagihanAllPeriod->where('status', 'lunas')->sum('nominal_total');

        $reservasiDpQuery = Reservasi::where('is_dp', true)
            ->whereIn('status', ['dp', 'dikonfirmasi']);
        if ($bulan) {
            $reservasiDpQuery->whereMonth('tanggal_mulai', $bulan);
        }
        if ($tahun) {
            $reservasiDpQuery->whereYear('tanggal_mulai', $tahun);
        }
        $totalReservasiDp = $reservasiDpQuery->sum('nominal_dp');

        $reservasiFullQuery = Reservasi::where('is_dp', false)
            ->whereIn('status', ['lunas']);
        if ($bulan) {
            $reservasiFullQuery->whereMonth('tanggal_mulai', $bulan);
        }
        if ($tahun) {
            $reservasiFullQuery->whereYear('tanggal_mulai', $tahun);
        }
        $totalReservasiFull = $reservasiFullQuery->sum('total_harga');

        $totalMasuk = $totalMasukPokok + $totalReservasiDp + $totalReservasiFull;
        $totalKeluar = $this->getPengeluaranQuery($request)->sum('nominal');
        $saldoBersih = $totalMasuk - $totalKeluar;

        return compact('totalMasuk', 'totalKeluar', 'saldoBersih');
    }

    /**
     * Display a listing of the reports.
     */
    public function index(Request $request)
    {
        $tagihanQuery = $this->getTagihanQuery($request);
        $pengeluaranQuery = $this->getPengeluaranQuery($request);

        $summary = $this->calculateCashFlowSummary($request);

        // Paginasi server-side (10 entri per halaman)
        $tagihan = $tagihanQuery->paginate(10, ['*'], 'page_tagihan')->withQueryString();
        $pengeluaran = $pengeluaranQuery->paginate(10, ['*'], 'page_pengeluaran')->withQueryString();

        return view('admin.laporan.index', array_merge(
            compact('tagihan', 'pengeluaran'),
            $summary
        ));
    }

    /**
     * Export reports to PDF format.
     */
    public function exportPdf(Request $request)
    {
        $tagihan = $this->getFilteredTagihan($request);
        $pengeluaran = $this->getFilteredPengeluaran($request);
        $summary = $this->calculateCashFlowSummary($request);

        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $status = $request->status;

        // Hitung total data tabel secara server-side
        $totalPokokTabel = $tagihan->sum('nominal_pokok');
        $totalDendaTabel = $tagihan->sum('nominal_denda');
        $totalTotalTabel = $tagihan->sum('nominal_total');
        $totalPengeluaranTabel = $pengeluaran->sum('nominal');

        // Susun teks filter secara server-side
        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $bulanNama = $request->filled('bulan') ? ($bulanList[(int)$bulan] ?? null) : null;
        $filterPeriode = 'Semua Periode';
        if ($bulanNama && $tahun) {
            $filterPeriode = $bulanNama . ' ' . $tahun;
        } elseif ($tahun) {
            $filterPeriode = 'Tahun ' . $tahun;
        } elseif ($bulanNama) {
            $filterPeriode = 'Bulan ' . $bulanNama;
        }

        $filterStatus = $status ? ucfirst($status) : 'Semua Status';
        $appName = \App\Models\Setting::get('logo_text', 'Asri Boarding House');

        $pdfOutput = $this->pdfGenerator->generate(
            'pdf.laporan-keuangan',
            array_merge(
                compact(
                    'tagihan', 'pengeluaran', 'bulan', 'tahun', 'status',
                    'totalPokokTabel', 'totalDendaTabel', 'totalTotalTabel',
                    'totalPengeluaranTabel', 'filterPeriode', 'filterStatus', 'appName'
                ),
                $summary
            ),
            'A4',
            'landscape'
        );

        return response()->streamDownload(function () use ($pdfOutput) {
            echo $pdfOutput;
        }, 'laporan-keuangan.pdf', [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="laporan-keuangan.pdf"',
        ]);
    }

    /**
     * Export reports to Excel format.
     */
    public function exportExcel(Request $request)
    {
        $tagihan = $this->getFilteredTagihan($request);
        $pengeluaran = $this->getFilteredPengeluaran($request);
        $summary = $this->calculateCashFlowSummary($request);

        $totalMasuk = $summary['totalMasuk'];
        $totalKeluar = $summary['totalKeluar'];
        $saldoBersih = $summary['saldoBersih'];

        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $namaBulan = $request->filled('bulan') ? ($bulanList[(int)$request->bulan] ?? $request->bulan) : 'Semua';

        return response()->streamDownload(function () use ($tagihan, $pengeluaran, $totalMasuk, $totalKeluar, $saldoBersih, $request, $namaBulan) {
            // Add UTF-8 BOM
            echo "\xEF\xBB\xBF";

            $handle = fopen('php://output', 'w');

            // Header & Metadata
            fputcsv($handle, ["LAPORAN KEUANGAN & NERACA KAS KOST"]);
            fputcsv($handle, ["Tanggal Cetak", now()->format('d-m-Y H:i:s')]);
            fputcsv($handle, ["Filter - Bulan", $namaBulan]);
            fputcsv($handle, ["Filter - Tahun", $request->tahun ?: 'Semua']);
            fputcsv($handle, ["Filter - Status Tagihan", $request->status ? ucfirst($request->status) : 'Semua']);
            fputcsv($handle, []);

            // Output CSV Header - Pemasukan
            fputcsv($handle, ["LAPORAN ARUS KAS MASUK (PEMASUKAN TAGIHAN)"]);
            fputcsv($handle, ["ID", "Order ID", "Periode", "Penyewa", "Nomor Kamar", "Sewa Pokok (Rp)", "Denda (Rp)", "Total Tagihan (Rp)", "Metode Pembayaran", "Status"]);

            // Output data rows - Pemasukan
            foreach ($tagihan as $t) {
                $periode = ($t->periode_bulan && $t->periode_tahun) ? sprintf('%02d/%d', $t->periode_bulan, $t->periode_tahun) : '-';
                $namaUser = $t->penyewa?->user?->nama ?? '-';
                
                // Format metode pembayaran
                $metode = '-';
                if ($t->metode_pembayaran) {
                    $metode = ($t->metode_pembayaran === 'cash') ? 'Tunai/Cash' : ucfirst($t->metode_pembayaran);
                }
                
                // Format status
                $status = ucfirst($t->status ?? '');

                fputcsv($handle, [
                    $t->id,
                    $t->order_id ?? '-',
                    $periode,
                    $namaUser,
                    $t->penyewa?->kamar?->nomor_kamar ?? '-',
                    (int) ($t->nominal_pokok ?? 0),
                    (int) ($t->nominal_denda ?? 0),
                    (int) ($t->nominal_total ?? 0),
                    $metode,
                    $status
                ]);
            }

            fputcsv($handle, []);

            // Output CSV Header - Pengeluaran
            fputcsv($handle, ["LAPORAN ARUS KAS KELUAR (REKAPITULASI PENGELUARAN OPERASIONAL)"]);
            fputcsv($handle, ["ID", "Tanggal Pengeluaran", "Nama Pengeluaran", "Kategori", "Nominal (Rp)", "Keterangan"]);

            // Output data rows - Pengeluaran
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

            fputcsv($handle, []);
            fputcsv($handle, ["RINGKASAN NERACA KAS BULANAN"]);
            fputcsv($handle, ["Total Arus Kas Masuk (Penerimaan Lunas) (Rp)", (int) $totalMasuk]);
            fputcsv($handle, ["Total Arus Kas Keluar (Total Pengeluaran) (Rp)", (int) $totalKeluar]);
            fputcsv($handle, ["Saldo Bersih Akhir (Rp)", (int) $saldoBersih]);

            fclose($handle);
        }, 'laporan-keuangan.csv', [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="laporan-keuangan.csv"',
        ]);
    }
}
