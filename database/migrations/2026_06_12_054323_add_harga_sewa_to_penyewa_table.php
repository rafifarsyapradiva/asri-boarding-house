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
            $table->decimal('harga_sewa', 12, 2)->after('kamar_id')->nullable();
        });

        // Set default value for existing records from the kamar's harga_bulan
        // Menggunakan subquery agar kompatibel dengan MySQL dan SQLite
        \DB::table('penyewa')->update([
            'harga_sewa' => \DB::table('kamar')
                ->whereColumn('kamar.id', 'penyewa.kamar_id')
                ->select('harga_bulan')
                ->limit(1)
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penyewa', function (Blueprint $table) {
            $table->dropColumn('harga_sewa');
        });
    }
};
