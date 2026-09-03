<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = [
        'tagihan_id',
        'transaction_id',
        'payment_type',
        'bank',
        'va_number',
        'nominal',
        'status_midtrans',
        'dikonfirmasi_oleh',
        'signature_key',
        'response_json',
        'tanggal_bayar',
        'pdf_path',
    ];

    protected $casts = [
        'response_json' => 'array',
        'tanggal_bayar' => 'datetime',
    ];

    /**
     * Relasi ke model Tagihan (belongsTo).
     */
    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(Tagihan::class, 'tagihan_id', 'id');
    }

    /**
     * Relasi ke model User (belongsTo).
     */
    public function dikonfirmasiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikonfirmasi_oleh', 'id')->withTrashed();
    }
}
