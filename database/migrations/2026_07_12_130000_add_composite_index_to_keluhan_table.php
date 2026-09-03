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
        Schema::table('keluhan', function (Blueprint $table) {
            $table->index(['penyewa_id', 'status'], 'idx_keluhan_penyewa_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keluhan', function (Blueprint $table) {
            $table->dropIndex('idx_keluhan_penyewa_status');
        });
    }
};
