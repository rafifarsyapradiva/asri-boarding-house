<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('penyewa_id');
            $table->string('order_id', 50)->unique();
            $table->tinyInteger('periode_bulan');
            $table->smallInteger('periode_tahun');
            $table->date('tanggal_tagihan');
            $table->date('tanggal_jatuh_tempo');
            $table->decimal('nominal_pokok', 12, 2);
            $table->decimal('nominal_denda', 12, 2)->default(0);
            $table->decimal('nominal_total', 12, 2);
            $table->tinyInteger('bulan_keterlambatan')->default(0);
            $table->enum('status', ['pending', 'lunas', 'gagal', 'kadaluarsa'])->default('pending');
            $table->enum('metode_pembayaran', ['midtrans', 'cash', ''])->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('penyewa_id')->references('id')->on('penyewa')->onDelete('restrict');
            $table->unique(['penyewa_id', 'periode_bulan', 'periode_tahun'], 'uq_periode');
            $table->index(['status', 'tanggal_jatuh_tempo'], 'idx_tagihan_cron_keterlambatan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};
