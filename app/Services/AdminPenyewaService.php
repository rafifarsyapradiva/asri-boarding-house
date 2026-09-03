<?php

namespace App\Services;

use App\Models\Penyewa;
use App\Models\User;
use App\Models\Kamar;
use App\Jobs\KirimWelcomeMessageJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class AdminPenyewaService
{
    public function __construct(
        protected BillingService $billingService
    ) {}

    /**
     * Get filtered query for Penyewa.
     *
     * @param array{search?: string, status?: string}|\Illuminate\Http\Request $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getPenyewaQuery(array|Request $filters = [])
    {
        if ($filters instanceof Request) {
            $filters = $filters->all();
        }

        $query = Penyewa::withTrashed()->with(['user', 'kamar'])->withCount('tagihan');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($sq) use ($search) {
                    $sq->withTrashed()->where('nama', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('no_hp', 'like', "%{$search}%");
                })->orWhereHas('kamar', function ($sq) use ($search) {
                    $sq->withTrashed()->where('nomor_kamar', 'like', "%{$search}%");
                })->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->filterStatus($filters['status']);
        }

        return $query;
    }

    /**
     * Register new penyewa in DB transaction.
     */
    public function registerPenyewa(array $data, int $adminId): Penyewa
    {
        $penyewa = DB::transaction(function () use ($data, $adminId) {
            $user = User::create([
                'nama'                    => $data['nama'],
                'email'                   => trim(strtolower($data['email'])),
                'no_hp'                   => $data['no_hp'],
                'password'                => bcrypt($data['no_hp']),
                'role'                    => 'penyewa',
                'is_active'               => 1,
                'require_password_change' => true,
            ]);

            // Row locking to prevent concurrent tenant assignment to the same room
            $kamar = Kamar::where('id', $data['kamar_id'])->lockForUpdate()->firstOrFail();
            if ($kamar->status !== 'tersedia') {
                throw new Exception('Kamar ini sudah terisi oleh penyewa lain.');
            }

            $deposit = isset($data['deposit']) && $data['deposit'] !== null ? $data['deposit'] : $kamar->harga_bulan;
            $hargaSewa = isset($data['harga_sewa']) && $data['harga_sewa'] !== null ? $data['harga_sewa'] : $kamar->harga_bulan;

            $tanggalMasuk = Carbon::parse($data['tanggal_masuk']);
            $tipeSewa = $data['tipe_sewa'];
            $durasi = intval($data['durasi']);

            $tanggalKeluarSeharusnya = match ($tipeSewa) {
                'harian'   => $tanggalMasuk->copy()->addDays($durasi)->toDateString(),
                'mingguan' => $tanggalMasuk->copy()->addWeeks($durasi)->toDateString(),
                default    => $tanggalMasuk->copy()->addMonths($durasi)->toDateString(),
            };

            $penyewa = Penyewa::create([
                'user_id'                   => $user->id,
                'kamar_id'                  => $kamar->id,
                'harga_sewa'                => $hargaSewa,
                'nik'                       => $data['nik'],
                'tanggal_masuk'             => $data['tanggal_masuk'],
                'tanggal_keluar_seharusnya' => $tanggalKeluarSeharusnya,
                'nama_wali'                 => $data['nama_wali'] ?? '-',
                'no_wali'                   => $data['no_wali'] ?? '-',
                'deposit'                   => $deposit,
                'status'                    => 'aktif',
                'tanggal_billing'           => 1,
                'tipe_sewa'                 => $tipeSewa,
                'durasi'                    => $durasi,
            ]);

            $this->billingService->injectManualPenyewaLunas($penyewa, $adminId);

            return $penyewa;
        });

        KirimWelcomeMessageJob::dispatch($penyewa);

        return $penyewa;
    }
}
