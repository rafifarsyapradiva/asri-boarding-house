<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Fasilitas extends Model
{
    use HasFactory;

    protected $table = 'fasilitas';

    public $timestamps = false; // Hanya menggunakan created_at yang didefinisikan secara manual

    protected $fillable = [
        'nama',
        'ikon',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi Many-to-Many ke tabel kamar.
     */
    public function kamar(): BelongsToMany
    {
        return $this->belongsToMany(Kamar::class, 'kamar_fasilitas', 'fasilitas_id', 'kamar_id');
    }

    /**
     * Local query scope untuk menyaring fasilitas yang aktif.
     */
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get emoji representation of the facility icon name.
     */
    public function getEmojiAttribute(): string
    {
        $map = [
            'wifi' => '📶',
            'snowflake' => '❄️',
            'bath' => '🛁',
            'bed' => '🛏️',
            'door-closed' => '🚪',
            'shower' => '🚿',
            'bolt' => '⚡',
            'desktop' => '🖥️',
        ];

        return $map[strtolower($this->ikon)] ?? '🏠';
    }
}
