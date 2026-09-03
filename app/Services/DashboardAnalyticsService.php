<?php

namespace App\Services;

use App\Models\Kamar;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Reservasi;
use App\Models\Pengeluaran;
use App\Models\WhatsappClick;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardAnalyticsService
{
    /**
     * Get all dashboard metrics data.
     */
    public function getDashboardMetrics(): array
    {
        $now = Carbon::now();

        // 1. Room metrics (using single-pass aggregated query)
        $roomStats = Kamar::withTrashed()
            ->selectRaw("
                COUNT(CASE WHEN deleted_at IS NULL THEN 1 END) as total,
                COUNT(CASE WHEN deleted_at IS NULL AND status = 'terisi' THEN 1 END) as terisi,
                COUNT(CASE WHEN deleted_at IS NULL AND status = 'tersedia' THEN 1 END) as tersedia,
                COUNT(CASE WHEN deleted_at IS NULL AND status = 'maintenance' THEN 1 END) as maintenance
            ")
            ->first();

        $totalKamar = (int) ($roomStats->total ?? 0);
        $kamarTerisi = (int) ($roomStats->terisi ?? 0);
        $kamarTersedia = (int) ($roomStats->tersedia ?? 0);
        $kamarMaintenance = (int) ($roomStats->maintenance ?? 0);
        $occupancyRate = $totalKamar > 0 ? round(($kamarTerisi / $totalKamar) * 100, 1) : 0;

        // 2. Current Month Cash Flow Metrics
        $pembayaranPokok = Pembayaran::whereMonth('tanggal_bayar', $now->month)
            ->whereYear('tanggal_bayar', $now->year)
            ->whereIn('status_midtrans', ['settlement', 'capture', 'success', 'cash_confirmed'])
            ->sum('nominal');

        $reservasiDp = Reservasi::where('is_dp', true)
            ->whereIn('status', ['dp', 'dikonfirmasi'])
            ->where(function ($q) use ($now) {
                $q->whereMonth('tanggal_konfirmasi', $now->month)
                  ->whereYear('tanggal_konfirmasi', $now->year)
                  ->orWhere(function ($sq) use ($now) {
                      $sq->whereNull('tanggal_konfirmasi')
                         ->whereMonth('updated_at', $now->month)
                         ->whereYear('updated_at', $now->year);
                  });
            })
            ->sum('nominal_dp');

        $reservasiFull = Reservasi::where('is_dp', false)
            ->whereIn('status', ['lunas'])
            ->where(function ($q) use ($now) {
                $q->whereMonth('tanggal_konfirmasi', $now->month)
                  ->whereYear('tanggal_konfirmasi', $now->year)
                  ->orWhere(function ($sq) use ($now) {
                      $sq->whereNull('tanggal_konfirmasi')
                         ->whereMonth('updated_at', $now->month)
                         ->whereYear('updated_at', $now->year);
                  });
            })
            ->sum('total_harga');

        $totalPemasukan = $pembayaranPokok + $reservasiDp + $reservasiFull;

        $totalPengeluaran = Pengeluaran::whereMonth('tanggal_pengeluaran', $now->month)
            ->whereYear('tanggal_pengeluaran', $now->year)
            ->sum('nominal');

        $keuntunganBersih = $totalPemasukan - $totalPengeluaran;

        // 3. Unpaid Invoice Breakdown
        $tagihanBelumLunas = Tagihan::where('status', 'pending')->count();
        $breakdownTerlambat = [
            'pending' => Tagihan::where('status', 'pending')
                ->where(function ($q) {
                    $q->whereDate('tanggal_jatuh_tempo', '>=', Carbon::today())
                      ->orWhere('bulan_keterlambatan', 0);
                })->count(),
            '1_bulan' => Tagihan::where('status', 'pending')
                ->whereDate('tanggal_jatuh_tempo', '<', Carbon::today())
                ->where('bulan_keterlambatan', 1)->count(),
            '2_bulan' => Tagihan::where('status', 'pending')
                ->whereDate('tanggal_jatuh_tempo', '<', Carbon::today())
                ->where('bulan_keterlambatan', 2)->count(),
            '3_bulan_plus' => Tagihan::where('status', 'pending')
                ->whereDate('tanggal_jatuh_tempo', '<', Carbon::today())
                ->where('bulan_keterlambatan', '>=', 3)->count(),
        ];

        // 4. Reservation Metrics
        $reservasiPendingKonfirmasi = Reservasi::whereIn('status', ['dp', 'lunas'])->count();
        $reservasiDikonfirmasiBulanIni = Reservasi::where('status', 'dikonfirmasi')
            ->whereMonth('tanggal_konfirmasi', $now->month)
            ->whereYear('tanggal_konfirmasi', $now->year)
            ->count();

        // 5. WhatsApp Click Metrics
        $totalWaClicksBulanIni = WhatsappClick::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        $waClicksBreakdown = WhatsappClick::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->selectRaw('source, count(*) as count')
            ->groupBy('source')
            ->orderBy('count', 'desc')
            ->get();

        // 6. Chart.js 12 Months Aggregation
        $chartData = $this->get12MonthsFinancialChart();

        // 7. Latest Invoices
        $latestTagihan = Tagihan::with(['penyewa.user', 'penyewa.kamar'])
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return [
            'totalKamar'                    => $totalKamar,
            'kamarTerisi'                   => $kamarTerisi,
            'occupancyRate'                 => $occupancyRate,
            'kamarTersedia'                 => $kamarTersedia,
            'kamarMaintenance'              => $kamarMaintenance,
            'totalPemasukan'                => $totalPemasukan,
            'totalPengeluaran'              => $totalPengeluaran,
            'keuntunganBersih'              => $keuntunganBersih,
            'tagihanBelumLunas'             => $tagihanBelumLunas,
            'breakdownTerlambat'            => $breakdownTerlambat,
            'reservasiPendingKonfirmasi'    => $reservasiPendingKonfirmasi,
            'reservasiDikonfirmasiBulanIni' => $reservasiDikonfirmasiBulanIni,
            'labelsBulan'                   => $chartData['labelsBulan'],
            'dataPemasukan'                 => $chartData['dataPemasukan'],
            'dataPengeluaran'               => $chartData['dataPengeluaran'],
            'latestTagihan'                 => $latestTagihan,
            'totalWaClicksBulanIni'         => $totalWaClicksBulanIni,
            'waClicksBreakdown'             => $waClicksBreakdown,
        ];
    }

    /**
     * Get 12 months financial chart data.
     */
    private function get12MonthsFinancialChart(): array
    {
        $labelsBulan = [];
        $dataPemasukan = [];
        $dataPengeluaran = [];

        $startRange = Carbon::now()->startOfMonth()->subMonths(11);
        $endRange = Carbon::now()->endOfMonth();

        $isSqlite = DB::connection()->getDriverName() === 'sqlite';

        if ($isSqlite) {
            $selectPembayaran = "strftime('%Y', tanggal_bayar) as year, strftime('%m', tanggal_bayar) as month, SUM(nominal) as total";
            $selectDp = "strftime('%Y', coalesce(tanggal_konfirmasi, updated_at)) as year, strftime('%m', coalesce(tanggal_konfirmasi, updated_at)) as month, SUM(nominal_dp) as total";
            $selectFull = "strftime('%Y', coalesce(tanggal_konfirmasi, updated_at)) as year, strftime('%m', coalesce(tanggal_konfirmasi, updated_at)) as month, SUM(total_harga) as total";
            $selectPengeluaran = "strftime('%Y', tanggal_pengeluaran) as year, strftime('%m', tanggal_pengeluaran) as month, SUM(nominal) as total";
        } else {
            $selectPembayaran = "YEAR(tanggal_bayar) as year, MONTH(tanggal_bayar) as month, SUM(nominal) as total";
            $selectDp = "YEAR(COALESCE(tanggal_konfirmasi, updated_at)) as year, MONTH(COALESCE(tanggal_konfirmasi, updated_at)) as month, SUM(nominal_dp) as total";
            $selectFull = "YEAR(COALESCE(tanggal_konfirmasi, updated_at)) as year, MONTH(COALESCE(tanggal_konfirmasi, updated_at)) as month, SUM(total_harga) as total";
            $selectPengeluaran = "YEAR(tanggal_pengeluaran) as year, MONTH(tanggal_pengeluaran) as month, SUM(nominal) as total";
        }

        $pembayaranMonthly = Pembayaran::selectRaw($selectPembayaran)
            ->whereBetween('tanggal_bayar', [$startRange->toDateString(), $endRange->toDateString()])
            ->whereIn('status_midtrans', ['settlement', 'capture', 'success', 'cash_confirmed'])
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(fn($item) => "{$item->year}-" . str_pad($item->month, 2, '0', STR_PAD_LEFT));

        $reservasiDpMonthly = Reservasi::selectRaw($selectDp)
            ->where('is_dp', true)
            ->whereIn('status', ['dp', 'dikonfirmasi'])
            ->whereBetween(
                DB::raw('COALESCE(tanggal_konfirmasi, updated_at)'),
                [$startRange->startOfDay()->toDateTimeString(), $endRange->endOfDay()->toDateTimeString()]
            )
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(fn($item) => "{$item->year}-" . str_pad($item->month, 2, '0', STR_PAD_LEFT));

        $reservasiFullMonthly = Reservasi::selectRaw($selectFull)
            ->where('is_dp', false)
            ->whereIn('status', ['lunas'])
            ->whereBetween(
                DB::raw('COALESCE(tanggal_konfirmasi, updated_at)'),
                [$startRange->startOfDay()->toDateTimeString(), $endRange->endOfDay()->toDateTimeString()]
            )
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(fn($item) => "{$item->year}-" . str_pad($item->month, 2, '0', STR_PAD_LEFT));

        $pengeluaranMonthly = Pengeluaran::selectRaw($selectPengeluaran)
            ->whereBetween('tanggal_pengeluaran', [$startRange->toDateString(), $endRange->toDateString()])
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(fn($item) => "{$item->year}-" . str_pad($item->month, 2, '0', STR_PAD_LEFT));

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->startOfMonth()->subMonths($i);
            $key = "{$date->year}-" . str_pad($date->month, 2, '0', STR_PAD_LEFT);

            $labelsBulan[] = $date->format('M Y');

            $pemasukanBulan = $pembayaranMonthly->get($key)->total ?? 0;
            $reservasiDpBulan = $reservasiDpMonthly->get($key)->total ?? 0;
            $reservasiFullBulan = $reservasiFullMonthly->get($key)->total ?? 0;

            $dataPemasukan[] = $pemasukanBulan + $reservasiDpBulan + $reservasiFullBulan;
            $dataPengeluaran[] = $pengeluaranMonthly->get($key)->total ?? 0;
        }

        return [
            'labelsBulan'     => $labelsBulan,
            'dataPemasukan'   => $dataPemasukan,
            'dataPengeluaran' => $dataPengeluaran,
        ];
    }
}
