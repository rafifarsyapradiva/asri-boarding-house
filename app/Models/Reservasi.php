<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reservasi';

    protected $fillable = [
        'user_id',
        'kamar_id',
        'dikonfirmasi_oleh',
        'penyewa_id',
        'tipe_sewa',
        'tanggal_mulai',
        'tanggal_selesai',
        'durasi',
        'total_harga',
        'status',
        'metode_pembayaran',
        'is_dp',
        'nominal_dp',
        'nominal_sisa',
        'snap_token',
        'order_id',
        'transaction_id',
        'catatan_user',
        'catatan_admin',
        'tanggal_konfirmasi',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_konfirmasi' => 'datetime',
        'is_dp' => 'boolean',
        'total_harga' => 'decimal:2',
        'nominal_dp' => 'decimal:2',
        'nominal_sisa' => 'decimal:2',
    ];

    /**
     * Accessor untuk kelas CSS status badge Reservasi
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending' => 'admin-badge-warning',
            'dp' => 'admin-badge-info',
            'lunas', 'dikonfirmasi' => 'admin-badge-success',
            'batal' => 'admin-badge-danger',
            default => 'admin-badge-neutral',
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class, 'kamar_id')->withTrashed();
    }

    public function dikonfirmasiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikonfirmasi_oleh')->withTrashed();
    }

    public function penyewa(): BelongsTo
    {
        return $this->belongsTo(Penyewa::class, 'penyewa_id')->withTrashed();
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'reservasi_id');
    }

    /**
     * Get the latest chat message for the reservation.
     */
    public function latestMessage(): HasOne
    {
        return $this->hasOne(ChatMessage::class, 'reservasi_id')->latestOfMany('id');
    }

    /**
     * Alias for latestMessage for backward compatibility.
     */
    public function latestChatMessage(): HasOne
    {
        return $this->latestMessage();
    }

    public function scopeAktif($query)
    {
        return $query->where('status', '!=', 'batal');
    }

    public function scopeOverlapDengan($query, $kamarId, $mulai, $selesai)
    {
        return $query->where('kamar_id', $kamarId)
            ->where(function ($q) use ($mulai, $selesai) {
                $q->where('tanggal_mulai', '<=', $selesai)
                  ->where('tanggal_selesai', '>=', $mulai);
            });
    }

    protected static function booted()
    {
        $clearCache = function ($reservasi) {
            \Illuminate\Support\Facades\Cache::forget("reservasi_status_{$reservasi->id}");
            \Illuminate\Support\Facades\Cache::forget("reservasi_chat_closed_{$reservasi->id}");
        };

        static::created($clearCache);
        static::updated($clearCache);
        static::deleted($clearCache);
    }

    /**
     * Local query scope to filter reservations by status and soft delete state.
     */
    public function scopeFilterStatus($query, ?string $status)
    {
        $status = $status ? strtolower($status) : null;
        if ($status === 'deleted') {
            return $query->onlyTrashed();
        }
        if ($status) {
            return $query->where('status', $status);
        }
        return $query->withoutTrashed();
    }

    /**
     * Mengecek apakah kamar terbooking atau ditempati penyewa aktif pada rentang tanggal tertentu.
     */
    public static function isKamarTerbooking(int $kamarId, string $tanggalMulai, string $tanggalSelesai, ?int $excludeReservasiId = null): bool
    {
        return self::query()
            ->when($excludeReservasiId, function ($query) use ($excludeReservasiId) {
                $query->where('id', '!=', $excludeReservasiId);
            })
            ->overlapDengan($kamarId, $tanggalMulai, $tanggalSelesai)
            ->aktif()
            ->exists() ||
            Penyewa::where('kamar_id', $kamarId)
            ->where('status', 'aktif')
            ->where(function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->where('tanggal_masuk', '<=', $tanggalSelesai)
                  ->where(function ($sub) use ($tanggalMulai) {
                      $sub->where('tanggal_keluar', '>=', $tanggalMulai)
                          ->orWhereNull('tanggal_keluar');
                  });
            })
            ->exists();
    }
}

