<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kamar', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nomor_kamar', 10)->unique();
            $table->tinyInteger('lantai')->default(1);
            $table->enum('tipe', ['standar', 'deluxe', 'vip'])->default('standar');
            $table->decimal('luas_m2', 5, 2);
            $table->decimal('harga_bulan', 12, 2);
            $table->text('deskripsi')->nullable();
            $table->string('foto', 255)->nullable();
            $table->enum('status', ['tersedia', 'terisi', 'maintenance'])->default('tersedia')->index('idx_status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kamar');
    }
};
