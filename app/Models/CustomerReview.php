<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CustomerReview extends Model
{
    use HasFactory;

    protected $table = 'customer_reviews';

    protected $fillable = [
        'nama',
        'pekerjaan',
        'bintang',
        'ulasan',
        'foto',
    ];

    protected $casts = [
        'bintang' => 'integer',
    ];

    /**
     * Accessor untuk URL Foto Pelanggan / Storage
     */
    protected function fotoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->foto ? Storage::url($this->foto) : null
        );
    }

    /**
     * Accessor untuk Inisial Nama Pelanggan
     */
    protected function initials(): Attribute
    {
        return Attribute::make(
            get: fn () => mb_strtoupper(mb_substr($this->nama ?? 'P', 0, 1, 'UTF-8'), 'UTF-8')
        );
    }

    /**
     * Accessor untuk Pekerjaan Formatted
     */
    protected function pekerjaanFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->pekerjaan ?: 'Penyewa Kost'
        );
    }
}
