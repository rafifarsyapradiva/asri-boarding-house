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
        Schema::create('notifikasi_khusus', function (Blueprint $table) {
            $table->id();
            $table->string('sumber'); // 'reservasi', 'tagihan', 'admin'
            $table->string('tipe_aktivitas'); // e.g., 'reservasi_baru', 'tagihan_lunas', 'kamar_diubah', etc.
            $table->text('deskripsi');
            $table->json('data_detail')->nullable();
            $table->unsignedInteger('user_id')->nullable(); // Who triggered the event (optional/nullable)
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index('sumber');
            $table->index('tipe_aktivitas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasi_khusus');
    }
};
