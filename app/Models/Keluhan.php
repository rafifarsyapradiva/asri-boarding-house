<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Keluhan extends Model
{
    use HasFactory;

    protected $table = 'keluhan';

    protected $fillable = [
        'penyewa_id',
        'judul',
        'kategori',
        'deskripsi',
        'foto_bukti',
        'status',
        'tanggapan_admin',
        'tanggal_selesai',
    ];

    protected $casts = [
        'tanggal_selesai' => 'datetime',
    ];

    /**
     * Accessor untuk label Kategori yang bersih
     */
    public function getKategoriLabelAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->kategori));
    }

    /**
     * Accessor untuk kelas CSS status badge Keluhan
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending' => 'admin-badge-warning',
            'diproses' => 'admin-badge-info',
            'selesai' => 'admin-badge-success',
            default => 'admin-badge-neutral',
        };
    }

    /**
     * Relasi ke model Penyewa (belongsTo).
     */
    public function penyewa(): BelongsTo
    {
        return $this->belongsTo(Penyewa::class, 'penyewa_id', 'id')->withTrashed();
    }
}
