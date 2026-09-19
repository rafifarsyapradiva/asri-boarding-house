<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    public const ROLE_PENYEWA = 'penyewa';
    public const ROLE_ADMIN = 'admin';

    protected static function booted()
    {
        static::deleted(function ($user) {
            if ($user->penyewa) {
                $user->penyewa->update(['status' => 'nonaktif']);
            }
        });
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'no_hp',
        'nik',
        'nama_wali',
        'no_wali',
        'role',
        'foto',
        'is_active',
        'require_password_change',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'require_password_change' => 'boolean',
        ];
    }

    /**
     * Accessor untuk mendapatkan nomor HP yang bersih dari awalan temporary (temp_).
     */
    public function getDisplayNoHpAttribute(): string
    {
        return ($this->no_hp && !str_starts_with($this->no_hp, 'temp_')) ? $this->no_hp : '';
    }

    /**
     * Relasi ke model Penyewa (hasOne).
     */
    public function penyewa(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Penyewa::class, 'user_id', 'id');
    }

    /**
     * Relasi ke model Reservasi (hasMany).
     */
    public function reservasi(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Reservasi::class, 'user_id');
    }

    /**
     * Relasi ke model ChatMessage (hasMany).
     */
    public function chatMessages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }

    /**
     * Overrides remember token methods to disable it since database has no remember_token column.
     */
    public function getRememberToken()
    {
        return null;
    }

    public function setRememberToken($value)
    {
        // Do nothing
    }

    public function getRememberTokenName()
    {
        return '';
    }

    /**
     * Map 'name' attribute dynamically to 'nama' database column.
     */
    public function getNameAttribute(): string
    {
        return $this->nama ?? '';
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['nama'] = $value;
    }



    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        if ($this->role === self::ROLE_PENYEWA) {
            $this->notify(new \App\Notifications\PenyewaResetPasswordNotification($token));
        } elseif ($this->role === self::ROLE_ADMIN) {
            $this->notify(new \App\Notifications\AdminResetPasswordNotification($token));
        } else {
            $this->notify(new \Illuminate\Auth\Notifications\ResetPassword($token));
        }
    }

    /**
     * Anonymize sensitive fields and soft-delete the user within a safe DB transaction.
     *
     * @return void
     * @throws \Exception
     */
    public function anonymizeAndDelete(): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () {
            $timestamp = time();

            $this->update([
                'email' => substr($this->email, 0, 150) . '_deleted_' . $timestamp,
                'no_hp' => substr($this->no_hp ?? '', 0, 4) . '_deleted_' . substr($timestamp, -4),
                'nik'   => $this->nik ? substr($this->nik, 0, 4) . '_deleted_' . substr($timestamp, -4) : null,
            ]);
            $this->delete();
        });
    }

    /**
     * Pengecekan kelengkapan data profil penyewa.
     */
    public function isProfileComplete(): bool
    {
        return !empty($this->no_hp) &&
               !str_starts_with($this->no_hp, 'temp_') &&
               !empty($this->nik) &&
               !empty($this->nama_wali) &&
               !empty($this->no_wali);
    }

    /**
     * Cek apakah user adalah penyewa aktif.
     */
    public function isActiveTenant(): bool
    {
        return $this->penyewa && $this->penyewa->status === 'aktif';
    }

    /**
     * Mendapatkan reservasi pending terakhir milik user ini.
     * Mencegah domain leakage (kebocoran query) ke controller.
     */
    public function latestPendingReservasi(): ?Reservasi
    {
        return $this->reservasi()
            ->where('status', 'pending')
            ->latest()
            ->first();
    }

    /**
     * Cek apakah user memiliki reservasi aktif (pending, dp, lunas).
     */
    public function hasActiveReservations(): bool
    {
        return \App\Models\Reservasi::where('user_id', $this->id)
            ->whereIn('status', ['pending', 'dp', 'lunas'])
            ->exists();
    }

    /**
     * Cek apakah user adalah admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Cek apakah user memiliki pembayaran reservasi yang sedang berjalan (pending atau dp saja).
     * Status 'dikonfirmasi'/'lunas' tidak dihitung karena berarti reservasi sudah selesai.
     */
    public function hasBookingInProgress(): bool
    {
        return $this->role === self::ROLE_PENYEWA 
            && !$this->isActiveTenant() 
            && $this->reservasi()->whereIn('status', ['pending', 'dp'])->exists();
    }

    /**
     * Mengambil data reservasi yang masih dalam proses pembayaran (pending/dp).
     */
    public function getLatestActiveReservasiAttribute()
    {
        return $this->reservasi()->whereIn('status', ['pending', 'dp'])->latest()->first();
    }

    /**
     * Menghasilkan inisial dari nama user secara aman.
     */
    public function getInitialsAttribute(): string
    {
        $name = trim($this->nama ?? $this->name ?? '');
        if (empty($name)) {
            return 'U';
        }
        $nameParts = explode(' ', $name);
        $initial = strtoupper(substr($nameParts[0], 0, 1));
        if (isset($nameParts[1]) && !empty($nameParts[1])) {
            $initial .= strtoupper(substr($nameParts[1], 0, 1));
        }
        return $initial;
    }

    /**
     * Mendapatkan nama rute dashboard yang sesuai berdasarkan role dan status tenant.
     */
    public function getDashboardRouteName(): string
    {
        if ($this->isAdmin()) {
            return 'admin.dashboard';
        }

        if ($this->role === self::ROLE_PENYEWA) {
            return $this->isActiveTenant() 
                ? 'penyewa.dashboard' 
                : 'penyewa.reservasi.dashboard';
        }

        return 'landing.index';
    }

    /**
     * Mengecek apakah akun user layak untuk dihapus secara mandiri.
     */
    public function isDeletable(): bool
    {
        return !$this->penyewa()->exists() && !$this->reservasi()->exists();
    }
}



