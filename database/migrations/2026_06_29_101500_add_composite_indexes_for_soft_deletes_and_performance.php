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
        Schema::table('kamar', function (Blueprint $table) {
            $table->index(['status', 'deleted_at'], 'idx_kamar_status_deleted_at');
            $table->index(['tipe', 'deleted_at'], 'idx_kamar_tipe_deleted_at');
            $table->index(['lantai', 'deleted_at'], 'idx_kamar_lantai_deleted_at');
        });

        Schema::table('penyewa', function (Blueprint $table) {
            $table->index(['status', 'deleted_at'], 'idx_penyewa_status_deleted_at');
            $table->index(['kamar_id', 'deleted_at'], 'idx_penyewa_kamar_deleted_at');
            $table->index(['user_id', 'deleted_at'], 'idx_penyewa_user_deleted_at');
        });

        Schema::table('reservasi', function (Blueprint $table) {
            $table->index(['status', 'deleted_at'], 'idx_reservasi_status_deleted_at');
            $table->index(['kamar_id', 'deleted_at'], 'idx_reservasi_kamar_deleted_at');
            $table->index(['user_id', 'deleted_at'], 'idx_reservasi_user_deleted_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'deleted_at'], 'idx_users_role_deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role_deleted_at');
        });

        Schema::table('reservasi', function (Blueprint $table) {
            $table->dropIndex('idx_reservasi_status_deleted_at');
            $table->dropIndex('idx_reservasi_kamar_deleted_at');
            $table->dropIndex('idx_reservasi_user_deleted_at');
        });

        Schema::table('penyewa', function (Blueprint $table) {
            $table->dropIndex('idx_penyewa_status_deleted_at');
            $table->dropIndex('idx_penyewa_kamar_deleted_at');
            $table->dropIndex('idx_penyewa_user_deleted_at');
        });

        Schema::table('kamar', function (Blueprint $table) {
            $table->dropIndex('idx_kamar_status_deleted_at');
            $table->dropIndex('idx_kamar_tipe_deleted_at');
            $table->dropIndex('idx_kamar_lantai_deleted_at');
        });
    }
};
