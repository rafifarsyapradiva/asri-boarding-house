<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Reservasi;

class ReservasiPolicy
{
    /**
     * Determine if the user can view the reservation.
     */
    public function view(User $user, Reservasi $reservasi): bool
    {
        return $user->role === User::ROLE_ADMIN || $this->isOwner($user, $reservasi);
    }

    /**
     * Determine if the user can chat in the reservation.
     */
    public function chat(User $user, Reservasi $reservasi): bool
    {
        return $user->role === User::ROLE_ADMIN || $this->isOwner($user, $reservasi);
    }

    /**
     * Determine if the user can pay the reservation.
     */
    public function pay(User $user, Reservasi $reservasi): bool
    {
        return $this->isOwner($user, $reservasi);
    }

    /**
     * Determine if the user can update (cancel) the reservation.
     */
    public function update(User $user, Reservasi $reservasi): bool
    {
        return $this->isOwner($user, $reservasi);
    }

    /**
     * Check if the user is the owner of the reservation.
     */
    private function isOwner(User $user, Reservasi $reservasi): bool
    {
        return $user->id === $reservasi->user_id;
    }
}

