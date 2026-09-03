<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotifikasiKhusus extends Model
{
    use HasFactory;

    protected $table = 'notifikasi_khusus';

    protected $fillable = [
        'sumber',
        'tipe_aktivitas',
        'deskripsi',
        'data_detail',
        'user_id',
    ];

    protected $casts = [
        'data_detail' => 'array',
    ];

    /**
     * Relasi ke model User (yang memicu aksi, jika ada).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }

    /**
     * Accessor: Format tipe aktivitas tanpa underscore
     */
    protected function formattedTipeAktivitas(): Attribute
    {
        return Attribute::make(
            get: fn () => str_replace('_', ' ', strtoupper($this->tipe_aktivitas))
        );
    }

    /**
     * Accessor: Text pemicu aktivitas (User / System)
     */
    protected function pemicuText(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->user) {
                    return 'System / Webhook Callback';
                }
                return "{$this->user->nama} (" . ucfirst($this->user->role) . ")";
            }
        );
    }

    /**
     * Accessor: Class badge warna berdasarkan sumber log
     */
    protected function sumberBadgeClass(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->sumber) {
                'reservasi' => 'bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border-emerald-800 dark:border-emerald-300',
                'tagihan' => 'bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border-amber-800 dark:border-amber-300',
                default => 'bg-purple-100 dark:bg-purple-950 text-purple-800 dark:text-purple-300 border-purple-800 dark:border-purple-300',
            }
        );
    }

    /**
     * Payload terstruktur untuk Alpine.js Modal Detail
     */
    public function toDetailPayload(): array
    {
        return [
            'id' => $this->id,
            'sumber' => strtoupper($this->sumber),
            'tipe' => $this->formatted_tipe_aktivitas,
            'deskripsi' => $this->deskripsi,
            'user' => $this->pemicu_text,
            'waktu' => $this->created_at->format('d M Y H:i:s') . ' (' . $this->created_at->diffForHumans() . ')',
            'detail' => $this->data_detail,
        ];
    }

    /**
     * Helper static method untuk mencatat notifikasi khusus / audit log
     */
    public static function log(string $sumber, string $tipeAktivitas, string $deskripsi, $dataDetail = null, ?int $userId = null): self
    {
        $resolvedUserId = $userId ?? (auth()->check() ? auth()->id() : null);

        return self::create([
            'sumber' => $sumber,
            'tipe_aktivitas' => $tipeAktivitas,
            'deskripsi' => $deskripsi,
            'data_detail' => $dataDetail,
            'user_id' => $resolvedUserId,
        ]);
    }
}
