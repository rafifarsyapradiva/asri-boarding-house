<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Keluhan;

class KeluhanPolicy
{
    /**
     * Determine if the user can view the complaint.
     */
    public function view(User $user, Keluhan $keluhan): bool
    {
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        return $keluhan->penyewa?->user_id === $user->id;
    }
}

