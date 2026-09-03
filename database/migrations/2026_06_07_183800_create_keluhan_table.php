<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('keluhan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('penyewa_id');
            $table->string('judul', 150);
            $table->enum('kategori', ['kamar', 'fasilitas_bersama', 'kebersihan', 'keamanan', 'lainnya']);
            $table->text('deskripsi');
            $table->string('foto_bukti', 255)->nullable();
            $table->enum('status', ['pending', 'diproses', 'selesai'])->default('pending');
            $table->text('tanggapan_admin')->nullable();
            $table->dateTime('tanggal_selesai')->nullable();
            $table->timestamps();

            $table->foreign('penyewa_id')
                  ->references('id')
                  ->on('penyewa')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keluhan');
    }
};
