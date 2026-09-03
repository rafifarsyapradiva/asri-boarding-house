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
        Schema::table('tagihan', function (Blueprint $table) {
            $table->enum('status', ['pending', 'lunas', 'gagal', 'kadaluarsa', 'terlambat'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            // Note: If reverting, make sure to clean up any 'terlambat' rows to 'pending' first
            \Illuminate\Support\Facades\DB::table('tagihan')
                ->where('status', 'terlambat')
                ->update(['status' => 'pending']);

            $table->enum('status', ['pending', 'lunas', 'gagal', 'kadaluarsa'])->default('pending')->change();
        });
    }
};
