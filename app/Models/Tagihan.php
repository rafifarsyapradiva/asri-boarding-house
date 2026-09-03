<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihan';

    protected $fillable = [
        'penyewa_id',
        'order_id',
        'periode_bulan',
        'periode_tahun',
        'tanggal_tagihan',
        'tanggal_jatuh_tempo',
        'nominal_pokok',
        'nominal_denda',
        'nominal_total',
        'bulan_keterlambatan',
        'status',
        'metode_pembayaran',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_tagihan' => 'date',
        'tanggal_jatuh_tempo' => 'date',
    ];

    /**
     * Accessor untuk nominal deposit
     */
    public function getNominalDepositAttribute(): int
    {
        return max(0, $this->nominal_total - $this->nominal_pokok - $this->nominal_denda);
    }

    /**
     * Accessor untuk status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->computed_status) {
            'lunas' => 'admin-badge-success',
            'pending' => 'admin-badge-warning',
            'terlambat', 'gagal' => 'admin-badge-danger',
            'kadaluarsa' => 'admin-badge-neutral',
            default => 'admin-badge-neutral',
        };
    }

    /**
     * Accessor untuk data banner status pada Dashboard Penyewa
     */
    public function getBannerStatusAttribute(): array
    {
        $isLate = $this->computed_status === 'terlambat' || $this->bulan_keterlambatan > 0;

        if (!$isLate) {
            $today = now()->startOfDay();
            $dueDate = $this->tanggal_jatuh_tempo?->startOfDay();

            if (!$dueDate) {
                $daysLeft = 0;
            } elseif ($today->gt($dueDate)) {
                $daysLeft = -1;
            } else {
                $daysLeft = (int) $today->diffInDays($dueDate);
            }

            $msg = match(true) {
                $daysLeft > 0 => "{$daysLeft} Hari lagi menuju batas jatuh tempo.",
                $daysLeft === 0 => 'Hari ini adalah batas akhir pembayaran (Jatuh Tempo).',
                default => 'Batas jatuh tempo telah lewat.'
            };

            return [
                'color' => 'bg-yellow-100 dark:bg-yellow-950/40 text-black dark:text-yellow-200 border-black dark:border-white',
                'title' => 'Tagihan Menunggu Pembayaran',
                'message' => $msg,
            ];
        }

        return match((int) $this->bulan_keterlambatan) {
            1 => [
                'color' => 'bg-orange-100 dark:bg-orange-950/40 text-black dark:text-orange-200 border-black dark:border-white',
                'title' => 'Tagihan Terlambat (Bulan 1)',
                'message' => 'Tagihan Terlambat — Belum ada denda.',
            ],
            2 => [
                'color' => 'bg-rose-100 dark:bg-rose-950/40 text-black dark:text-rose-200 border-black dark:border-white',
                'title' => 'Tagihan Terlambat (Bulan 2)',
                'message' => 'Wali/Orang tua Anda telah dihubungi terkait tunggakan.',
            ],
            default => [
                'color' => 'bg-red-200 dark:bg-red-950 text-black dark:text-red-200 border-black dark:border-white',
                'title' => 'Tagihan Terlambat (Bulan 3+)',
                'message' => 'Denda keterlambatan 5% telah diberlakukan secara berkala.',
            ]
        };
    }

    /**
     * Accessor untuk pesan konfirmasi WhatsApp
     */
    public function getWaConfirmationMessageAttribute(): string
    {
        $formattedTotal = number_format($this->nominal_total, 0, ',', '.');
        $periode = sprintf('%02d/%d', $this->periode_bulan, $this->periode_tahun);
        
        return "Selamat pagi/siang/sore Admin Asri Boarding House,\n\n"
             . "Saya ingin mengonfirmasi pembayaran tagihan kost saya dengan rincian sebagai berikut:\n"
             . "• Order ID: #{$this->order_id}\n"
             . "• Periode: {$periode}\n"
             . "• Total Tagihan: Rp {$formattedTotal}\n\n"
             . "Berikut saya lampirkan bukti transfer pembayaran manual untuk dapat diverifikasi. Terima kasih banyak.";
    }

    /**
     * Relasi ke model Penyewa (belongsTo).
     */
    public function penyewa(): BelongsTo
    {
        return $this->belongsTo(Penyewa::class, 'penyewa_id', 'id')->withTrashed();
    }

    /**
     * Relasi ke model Pembayaran (hasMany).
     */
    public function pembayaran(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Pembayaran::class, 'tagihan_id', 'id');
    }

    /**
     * Accessor untuk status komputasi tanpa merusak nilai mentah status database.
     */
    public function getComputedStatusAttribute(): string
    {
        $rawStatus = $this->attributes['status'] ?? 'pending';
        if ($rawStatus === 'pending' && $this->tanggal_jatuh_tempo && $this->tanggal_jatuh_tempo->endOfDay()->isPast()) {
            return 'terlambat';
        }
        return $rawStatus;
    }

    /**
     * Accessor untuk status tagihan mentah database.
     */
    public function getStatusAttribute($value): string
    {
        return $value ?? 'pending';
    }

    /**
     * Accessor untuk status tampilan tagihan.
     */
    public function getStatusDisplayAttribute(): string
    {
        return $this->computed_status;
    }

    /**
     * Scope untuk menyaring tagihan yang terlambat secara akurat di level database.
     */
    public function scopeTerlambat($query)
    {
        return $query->where('status', 'pending')
            ->whereNotNull('tanggal_jatuh_tempo')
            ->where('tanggal_jatuh_tempo', '<', now()->startOfDay());
    }

    /**
     * Cek apakah tagihan dapat dikonfirmasi pembayaran cash secara manual.
     */
    public function canBeConfirmedManually(): bool
    {
        return in_array($this->computed_status, ['pending', 'terlambat']);
    }

    /**
     * Accessor untuk pemformatan periode (contoh: 08/2026)
     */
    public function getPeriodeFormattedAttribute(): string
    {
        return sprintf('%02d/%d', $this->periode_bulan, $this->periode_tahun);
    }

    /**
     * Accessor untuk mengecek apakah tagihan dalam status yang dapat dibayar
     */
    public function getCanBePaidAttribute(): bool
    {
        return in_array($this->computed_status, ['pending', 'terlambat', 'gagal']);
    }

    /**
     * Accessor untuk mendapatkan record pembayaran yang terkonfirmasi / lunas
     */
    public function getPembayaranTerkonfirmasiAttribute(): ?Pembayaran
    {
        return $this->pembayaran->first(function ($pembayaran) {
            return in_array($pembayaran->status_midtrans, ['settlement', 'capture', 'cash_confirmed']);
        });
    }
}

