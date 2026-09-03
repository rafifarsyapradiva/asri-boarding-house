<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\Pengumuman;
use App\Models\Peraturan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardController extends Controller
{
    /**
     * Display the tenant dashboard aligned with the admin dashboard metrics and charts.
     */
    public function index(): View
    {
        $user = Auth::user();
        $penyewa = $user->penyewa;

        if ($penyewa) {
            $penyewa->load(['kamar.fasilitas', 'user']);
        }

        $penyewaId = $penyewa?->id;

        if ($penyewaId) {
            // Optimasi: Gabungkan kueri agregat 4 metrik status menjadi 1 kueri SQL tunggal
            $stats = Tagihan::where('penyewa_id', $penyewaId)
                ->selectRaw("
                    SUM(CASE WHEN status IN ('pending', 'terlambat') THEN 1 ELSE 0 END) as belum_lunas_count,
                    SUM(CASE WHEN status = 'lunas' THEN nominal_total ELSE 0 END) as total_paid,
                    SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) as lunas_count,
                    SUM(CASE WHEN status IN ('kadaluarsa', 'gagal') THEN 1 ELSE 0 END) as gagal_count
                ")->first();

            $tagihanBelumLunas    = (int) ($stats->belum_lunas_count ?? 0);
            $totalPaidAmount      = (float) ($stats->total_paid ?? 0);
            $lunasCount           = (int) ($stats->lunas_count ?? 0);
            $gagalKadaluarsaCount = (int) ($stats->gagal_count ?? 0);

            $activeTagihan = Tagihan::where('penyewa_id', $penyewaId)
                ->whereIn('status', ['pending', 'terlambat', 'gagal'])
                ->first();

            $latestTagihan = Tagihan::where('penyewa_id', $penyewaId)
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString();
        } else {
            $tagihanBelumLunas = $totalPaidAmount = $lunasCount = $gagalKadaluarsaCount = 0;
            $activeTagihan = null;
            $latestTagihan = new LengthAwarePaginator([], 0, 10);
        }

        // Summary Cards Metrics
        $nomorKamar    = $penyewa?->kamar?->nomor_kamar ?? '-';
        $lantaiKamar   = $penyewa?->kamar?->lantai ?? '-';
        $tipeKamar     = $penyewa?->kamar?->tipe ?? '-';
        $hargaSewa     = $penyewa ? ($penyewa->harga_sewa ?? ($penyewa->kamar?->harga_bulan ?? 0)) : 0;
        $depositAmount = $penyewa?->deposit ?? 0;

        // Ambil data pengeluaran 12 bulan sekaligus
        $labelsBulan = [];
        $dataPemasukan = []; // Penamaan dipertahankan untuk kompatibilitas Blade view

        if ($penyewaId) {
            $startDate = Carbon::now()->subMonths(11)->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();

            $tagihanBulanan = Tagihan::where('penyewa_id', $penyewaId)
                ->where('status', 'lunas')
                ->whereBetween('tanggal_tagihan', [$startDate, $endDate])
                ->get()
                ->groupBy(fn($item) => Carbon::parse($item->tanggal_tagihan)->format('Y-m'))
                ->map(fn($group) => $group->sum('nominal_total'));

            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $key = $date->format('Y-m');
                $labelsBulan[] = $date->format('M Y');
                $dataPemasukan[] = $tagihanBulanan->has($key) ? (float) $tagihanBulanan->get($key) : 0.0;
            }
        } else {
            for ($i = 11; $i >= 0; $i--) {
                $labelsBulan[] = Carbon::now()->subMonths($i)->format('M Y');
                $dataPemasukan[] = 0.0;
            }
        }

        // Variable Aliasing Kompatibilitas View Chart
        $tersedia    = $lunasCount;
        $terisi      = $tagihanBelumLunas;
        $maintenance = $gagalKadaluarsaCount;

        $pengumuman = Pengumuman::where('is_active', true)->latest()->get();

        return view('penyewa.dashboard', compact(
            'user',
            'penyewa',
            'nomorKamar',
            'lantaiKamar',
            'tipeKamar',
            'hargaSewa',
            'depositAmount',
            'tagihanBelumLunas',
            'totalPaidAmount',
            'lunasCount',
            'labelsBulan',
            'dataPemasukan',
            'tersedia',
            'terisi',
            'maintenance',
            'latestTagihan',
            'activeTagihan',
            'pengumuman'
        ));
    }

    public function peraturan(): View
    {
        $user = Auth::user();
        $peraturan = Peraturan::orderBy('urutan', 'asc')->get();

        return view('penyewa.peraturan', compact('user', 'peraturan'));
    }

    /**
     * Display the dedicated dashboard for tenants with pending/inactive reservations.
     */
    public function dashboardPending(): View
    {
        $user = Auth::user();
        $latestReservasi = $user->reservasi()
            ->where('status', '!=', 'batal')
            ->latest()
            ->first();

        return view('penyewa.dashboard-pending', compact('user', 'latestReservasi'));
    }
}
