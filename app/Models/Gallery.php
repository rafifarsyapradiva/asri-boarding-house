<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Gallery extends Model
{
    use HasFactory;

    protected $table = 'galleries';

    protected $fillable = [
        'judul',
        'deskripsi',
        'foto',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Accessor untuk mendapatkan URL foto yang valid (HTTP URL vs Storage Local).
     */
    protected function fotoUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->foto) {
                    return null;
                }
                return Str::startsWith($this->foto, ['http://', 'https://'])
                    ? $this->foto
                    : asset('storage/' . $this->foto);
            }
        );
    }

    /**
     * Scope a query to only include active gallery items, ordered by sequence ascending.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAktif($query)
    {
        return $query->where('is_active', true)->orderBy('urutan', 'asc');
    }
}
