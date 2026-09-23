<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menghapus kolom pdf_path karena sistem PDF telah dimigrasikan ke client-side rendering.
     */
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn('pdf_path');
        });
    }

    /**
     * Reverse the migrations.
     * Mengembalikan kolom pdf_path jika migration di-rollback.
     */
    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->string('pdf_path')->nullable()->after('tanggal_bayar');
        });
    }
};
