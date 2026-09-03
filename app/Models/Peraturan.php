<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peraturan extends Model
{
    use HasFactory;

    protected $table = 'peraturan';

    protected $fillable = [
        'judul',
        'deskripsi',
        'ikon',
        'urutan',
    ];

    /**
     * Get CSS class for icon background badge.
     */
    public function getBadgeColorClassAttribute(): string
    {
        return match($this->ikon) {
            'sparkles' => 'bg-indigo-300 text-indigo-950 border-2 border-black dark:border-white',
            'bolt' => 'bg-amber-300 text-amber-950 border-2 border-black dark:border-white',
            'computer-desktop' => 'bg-blue-300 text-blue-950 border-2 border-black dark:border-white',
            'credit-card' => 'bg-green-300 text-green-950 border-2 border-black dark:border-white',
            'exclamation-triangle' => 'bg-rose-300 text-rose-950 border-2 border-black dark:border-white',
            'user-group' => 'bg-teal-300 text-teal-950 border-2 border-black dark:border-white',
            'shield-alert' => 'bg-red-300 text-red-950 border-2 border-black dark:border-white',
            'no-symbol' => 'bg-orange-300 text-orange-950 border-2 border-black dark:border-white',
            'moon' => 'bg-purple-300 text-purple-950 border-2 border-black dark:border-white',
            default => 'bg-slate-300 text-slate-950 border-2 border-black dark:border-white',
        };
    }

    /**
     * Get readable icon label.
     */
    public function getIkonLabelAttribute(): string
    {
        return str_replace('-', ' ', $this->ikon ?? '');
    }
}
