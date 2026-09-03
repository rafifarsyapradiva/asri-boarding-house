<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    /**
     * Standard layout container sizes supported by the UI design system.
     */
    public const ALLOWED_SIZES = ['sm', 'md', 'lg', 'xl', 'full'];

    /**
     * The layout container size (e.g., 'md', 'lg').
     */
    public string $size;

    /**
     * Create a new component instance with size validation fallback.
     */
    public function __construct(string $size = 'md')
    {
        $normalizedSize = strtolower(trim($size));

        $this->size = in_array($normalizedSize, self::ALLOWED_SIZES, true)
            ? $normalizedSize
            : 'md';
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.guest', [
            'size' => $this->size,
        ]);
    }
}

