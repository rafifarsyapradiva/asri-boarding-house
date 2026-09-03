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
        Schema::table('penyewa', function (Blueprint $table) {
            $table->index(['kamar_id', 'status', 'tanggal_masuk', 'tanggal_keluar'], 'idx_penyewa_overlap_check');
        });

        Schema::table('reservasi', function (Blueprint $table) {
            $table->index(['kamar_id', 'status', 'tanggal_mulai', 'tanggal_selesai'], 'idx_reservasi_overlap_check');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservasi', function (Blueprint $table) {
            $table->dropIndex('idx_reservasi_overlap_check');
        });

        Schema::table('penyewa', function (Blueprint $table) {
            $table->dropIndex('idx_penyewa_overlap_check');
        });
    }
};
