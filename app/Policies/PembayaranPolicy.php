<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Pembayaran;

class PembayaranPolicy
{
    /**
     * Determine if the user can download the invoice PDF of the payment.
     */
    public function downloadNota(User $user, Pembayaran $pembayaran): bool
    {
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        $penyewaId = $user->penyewa?->id;
        if (!$penyewaId) {
            return false;
        }

        return $pembayaran->tagihan?->penyewa_id === $penyewaId;
    }
}

