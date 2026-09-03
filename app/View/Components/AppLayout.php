<?php

namespace App\View\Components;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * Route patterns that mandate brutalist header styling.
     */
    public const BRUTALIST_ROUTE_PATTERNS = [
        'reservasi.*',
        'penyewa.*',
        'admin.*',
        'profile.*',
    ];

    /**
     * Explicit override for brutalist layout header.
     */
    public ?bool $brutalist;

    /**
     * Create a new component instance.
     */
    public function __construct(?bool $brutalist = null)
    {
        $this->brutalist = $brutalist;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app', [
            'isBrutalistHeader' => $this->shouldUseBrutalistHeader(),
        ]);
    }

    /**
     * Determine if the layout should use a brutalist header based on explicit override,
     * route pattern match, or active user session conditions (tenant role & active status).
     */
    public function shouldUseBrutalistHeader(): bool
    {
        // 1. Prioritize explicit boolean override if provided
        if (is_bool($this->brutalist)) {
            return $this->brutalist;
        }

        // 2. Check if current route matches brutalist route patterns
        if (request()->routeIs(self::BRUTALIST_ROUTE_PATTERNS)) {
            return true;
        }

        // 3. Fallback: check if active user is an inactive tenant
        return $this->isInactiveTenantUser();
    }

    /**
     * Determine if the authenticated user is a tenant with a non-active status.
     */
    private function isInactiveTenantUser(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user || $user->role !== 'penyewa') {
            return false;
        }

        // Check if penyewa relationship exists and status is not 'aktif'
        $penyewa = $user->penyewa;

        return ! $penyewa || $penyewa->status !== 'aktif';
    }
}

