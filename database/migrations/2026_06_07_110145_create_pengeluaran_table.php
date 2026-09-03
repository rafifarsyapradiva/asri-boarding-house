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
        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama_pengeluaran', 150);
            $table->enum('kategori', ['maintenance', 'utilitas', 'operasional', 'lainnya']);
            $table->decimal('nominal', 12, 2);
            $table->date('tanggal_pengeluaran');
            $table->string('bukti_nota', 255)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluaran');
    }
};
