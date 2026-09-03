<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Tagihan;

class TagihanPolicy
{
    /**
     * Determine if the user can view the tagihan.
     */
    public function view(User $user, Tagihan $tagihan): bool
    {
        return $user->role === User::ROLE_ADMIN || $this->isOwner($user, $tagihan);
    }

    /**
     * Determine if the user can pay the tagihan.
     */
    public function pay(User $user, Tagihan $tagihan): bool
    {
        return $this->isOwner($user, $tagihan);
    }

    /**
     * Check if the user is the owner of the tagihan.
     */
    private function isOwner(User $user, Tagihan $tagihan): bool
    {
        return $tagihan->penyewa?->user_id === $user->id;
    }
}

