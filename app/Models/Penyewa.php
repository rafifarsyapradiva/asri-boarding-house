<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penyewa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penyewa';

    protected $fillable = [
        'user_id',
        'kamar_id',
        'harga_sewa',
        'nik',
        'tanggal_masuk',
        'tanggal_keluar',
        'tanggal_keluar_seharusnya',
        'status',
        'tanggal_billing',
        'tipe_sewa',
        'durasi',
        'deposit',
        'no_wali',
        'nama_wali',
        'catatan',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
        'tanggal_keluar_seharusnya' => 'date',
        'harga_sewa' => 'decimal:2',
    ];

    /**
     * Relasi ke model User (belongsTo).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }

    /**
     * Relasi ke model Kamar (belongsTo).
     */
    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class, 'kamar_id', 'id')->withTrashed();
    }

    /**
     * Relasi ke model Keluhan (hasMany).
     */
    public function keluhan(): HasMany
    {
        return $this->hasMany(Keluhan::class, 'penyewa_id', 'id');
    }

    /**
     * Relasi ke model Tagihan (hasMany).
     */
    public function tagihan(): HasMany
    {
        return $this->hasMany(Tagihan::class, 'penyewa_id', 'id');
    }

    /**
     * Accessor untuk mengecek apakah masa sewa penyewa aktif sudah overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        if ($this->status !== 'aktif' || !$this->tanggal_keluar_seharusnya) {
            return false;
        }

        return \Illuminate\Support\Carbon::parse($this->tanggal_keluar_seharusnya)->isPast();
    }

    /**
     * Accessor untuk memformat durasi sewa secara terpusat.
     */
    public function getDurasiFormattedAttribute(): string
    {
        if (!$this->durasi) {
            return '-';
        }

        $satuan = match ($this->tipe_sewa) {
            'harian' => 'hari',
            'mingguan' => 'minggu',
            default => 'bln',
        };

        return "{$this->durasi} {$satuan}";
    }

    /**
     * Local query scope to filter tenants by status and soft delete state.
     */
    public function scopeFilterStatus($query, ?string $status)
    {
        if ($status === 'deleted') {
            return $query->onlyTrashed();
        }
        if ($status) {
            return $query->where('status', $status);
        }
        return $query->withoutTrashed();
    }
}

