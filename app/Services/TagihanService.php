<?php

namespace App\Services;

use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Events\PembayaranCashDikonfirmasi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TagihanService
{
    /**
     * Konfirmasi pembayaran tunai dalam transaksi database.
     */
    public function confirmCashPayment(Tagihan $tagihan, int $adminId, ?string $catatan = null): Pembayaran
    {
        return DB::transaction(function () use ($tagihan, $adminId, $catatan) {
            $pembayaran = Pembayaran::create([
                'tagihan_id'        => $tagihan->id,
                'transaction_id'    => sprintf('CASH-%d-%d', $tagihan->id, time()),
                'payment_type'      => 'cash',
                'nominal'           => $tagihan->nominal_total,
                'status_midtrans'   => 'cash_confirmed',
                'dikonfirmasi_oleh' => $adminId,
                'tanggal_bayar'     => Carbon::now(),
            ]);

            $tagihan->update([
                'status'            => 'lunas',
                'metode_pembayaran' => 'cash',
                'keterangan'        => $catatan ?: $tagihan->keterangan,
            ]);

            event(new PembayaranCashDikonfirmasi($pembayaran));

            return $pembayaran;
        });
    }

    /**
     * Ambil data tagihan terpaginasi berdasarkan filter.
     */
    public function getFilteredPaginatedTagihan(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = Tagihan::with(['penyewa.user', 'penyewa.kamar']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhereHas('penyewa.user', fn($qu) => $qu->where('nama', 'like', "%{$search}%"))
                  ->orWhereHas('penyewa.kamar', fn($qk) => $qk->where('nomor_kamar', 'like', "%{$search}%"));
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', strtolower($filters['status']));
        }

        return $query->orderBy('created_at', 'desc')
                     ->paginate($perPage)
                     ->withQueryString();
    }
}
