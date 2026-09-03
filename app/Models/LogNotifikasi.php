<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogNotifikasi extends Model
{
    use HasFactory;

    protected $table = 'log_notifikasi';

    public $timestamps = false;

    protected $fillable = [
        'penyewa_id',
        'tagihan_id',
        'channel',
        'event',
        'status',
        'pesan',
        'error_msg',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Relasi ke model Penyewa (belongsTo).
     */
    public function penyewa(): BelongsTo
    {
        return $this->belongsTo(Penyewa::class, 'penyewa_id', 'id')->withTrashed();
    }

    /**
     * Relasi ke model Tagihan (belongsTo).
     */
    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(Tagihan::class, 'tagihan_id', 'id');
    }

    /**
     * Accessor untuk nama event yang terformat rapi.
     */
    public function getFormattedEventAttribute(): string
    {
        return str_replace('_', ' ', $this->event ?? '');
    }

    /**
     * Accessor untuk tanggal dibuat yang terformat rapi.
     */
    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('d M Y H:i') : '-';
    }

    /**
     * Accessor untuk pesan dengan fallback otomatis.
     */
    public function getPesanDisplayAttribute(): string
    {
        return $this->pesan ?? 'Notifikasi otomatis (Isi pesan dinamis)';
    }

    /**
     * Format payload yang aman untuk modal Alpine.js.
     */
    public function toModalPayload(): array
    {
        return [
            'channel' => $this->channel,
            'event' => $this->formatted_event,
            'status' => $this->status,
            'pesan' => $this->pesan_display,
            'waktu' => $this->formatted_created_at,
        ];
    }
}
