<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use InvalidArgumentException;

class Kamar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kamar';

    protected $fillable = [
        'nomor_kamar',
        'lantai',
        'tipe',
        'luas_m2',
        'harga_bulan',
        'deskripsi',
        'foto',
        'status',
    ];

    protected $casts = [
        'harga_bulan' => 'integer',
        'luas_m2' => 'float',
        'lantai' => 'integer',
    ];

    /**
     * Relasi Many-to-Many ke tabel fasilitas.
     */
    public function fasilitas(): BelongsToMany
    {
        return $this->belongsToMany(Fasilitas::class, 'kamar_fasilitas', 'kamar_id', 'fasilitas_id');
    }

    /**
     * Relasi ke model Penyewa (hasOne) yang berstatus aktif.
     */
    public function penyewaAktif(): HasOne
    {
        return $this->hasOne(Penyewa::class, 'kamar_id', 'id')->where('status', 'aktif');
    }

    /**
     * Relasi ke model Reservasi (hasMany).
     */
    public function reservasi(): HasMany
    {
        return $this->hasMany(Reservasi::class, 'kamar_id');
    }

    /**
     * Relasi ke model Penyewa (hasMany).
     */
    public function penyewa(): HasMany
    {
        return $this->hasMany(Penyewa::class, 'kamar_id');
    }

    /**
     * Relasi ke model Reservasi (hasMany) yang aktif.
     */
    public function reservasiAktif(): HasMany
    {
        return $this->hasMany(Reservasi::class, 'kamar_id')->where('status', '!=', 'batal');
    }

    /**
     * Local query scope to filter rooms by status and soft delete state.
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

    /**
     * Hitung harga dasar sewa tanpa diskon (Single Responsibility & DRY).
     */
    public function kalkulasiHargaDasar(string $tipeSewa, int $durasi): float
    {
        if ($durasi <= 0) {
            throw new InvalidArgumentException("Durasi sewa harus lebih besar dari nol.");
        }

        return match ($tipeSewa) {
            'harian'   => ($this->harga_bulan / 30) * $durasi,
            'mingguan' => ($this->harga_bulan / 4) * $durasi,
            'bulanan'  => (float) ($this->harga_bulan * $durasi),
            default    => (float) ($this->harga_bulan * $durasi),
        };
    }

    /**
     * Hitung total harga berdasarkan tipe sewa dan durasi (termasuk diskon promo).
     */
    public function kalkulasiHargaSewa(string $tipeSewa, int $durasi, ?float $overrideDiscount = null): float
    {
        $hargaDasar = $this->kalkulasiHargaDasar($tipeSewa, $durasi);

        $discount = $overrideDiscount ?? Setting::getDiscountForDuration($tipeSewa, $durasi);
        if ($discount > 0) {
            $hargaDasar *= (1 - ($discount / 100));
        }

        return (float) ceil($hargaDasar);
    }

    /**
     * Hitung nominal minimal DP (default 30% dari total harga).
     */
    public function kalkulasiMinimalDp(float $totalHarga): float
    {
        if ($totalHarga <= 0) {
            return 0.0;
        }
        $persentaseDp = config('reservasi.dp_percentage', 0.30);
        return (float) ceil($totalHarga * $persentaseDp);
    }

    /**
     * Get the room's photo URL with safe fallback image handling (non-blocking I/O).
     */
    public function getFotoUrlAttribute(): string
    {
        if (!empty($this->foto)) {
            return asset('storage/' . $this->foto);
        }

        $fallbackImages = config('reservasi.fallback_images', [
            'vip' => 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=600&q=80',
            'deluxe' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=600&q=80',
            'standar' => 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=600&q=80'
        ]);

        $tipeLower = strtolower($this->tipe ?? 'standar');

        return $fallbackImages[$tipeLower] ?? $fallbackImages['standar'] ?? 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=600&q=80';
    }

    /**
     * Mendapatkan daftar paket promo yang sudah terhitung harganya.
     */
    public function getPromoPackagesAttribute(): array
    {
        $packages = [];
        for ($i = 1; $i <= 3; $i++) {
            $type = Setting::get("promo_pkg{$i}_type", 'bulanan');
            $duration = (int) Setting::get("promo_pkg{$i}_duration");

            if ($duration <= 0) {
                continue;
            }

            $originalPrice = $this->kalkulasiHargaDasar($type, $duration);
            $discount = (float) Setting::get("promo_pkg{$i}_discount");

            $packages[] = [
                'name' => Setting::get("promo_pkg{$i}_name"),
                'type' => $type,
                'duration' => $duration,
                'discount' => $discount,
                'desc' => Setting::get("promo_pkg{$i}_desc"),
                'original_price' => (float) ceil($originalPrice),
                'promo_price' => $this->kalkulasiHargaSewa($type, $duration, $discount),
            ];
        }
        return $packages;
    }
}

