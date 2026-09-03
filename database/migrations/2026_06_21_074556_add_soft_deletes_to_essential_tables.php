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
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes()->index();
        });

        Schema::table('kamar', function (Blueprint $table) {
            $table->softDeletes()->index();
        });

        Schema::table('penyewa', function (Blueprint $table) {
            $table->softDeletes()->index();
        });

        Schema::table('reservasi', function (Blueprint $table) {
            $table->softDeletes()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservasi', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('penyewa', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('kamar', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
