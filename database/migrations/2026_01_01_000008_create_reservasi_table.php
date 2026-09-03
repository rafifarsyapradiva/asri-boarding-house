<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservasi', function (Blueprint $table) {
            $table->id();
            
            // Foreign Key Columns
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('kamar_id');
            $table->unsignedInteger('dikonfirmasi_oleh')->nullable();
            $table->unsignedInteger('penyewa_id')->nullable();
            
            // Business Attribute Columns
            $table->enum('tipe_sewa', ['harian', 'mingguan', 'bulanan'])->default('bulanan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->tinyInteger('durasi')->default(1);
            $table->decimal('total_harga', 12, 2);
            $table->enum('status', ['pending', 'dp', 'lunas', 'dikonfirmasi', 'batal'])->default('pending');
            $table->enum('metode_pembayaran', ['midtrans', 'cash'])->nullable();
            $table->boolean('is_dp')->default(false);
            $table->decimal('nominal_dp', 12, 2)->default(0);
            $table->decimal('nominal_sisa', 12, 2)->default(0);
            
            // Transaction & Log Columns
            $table->string('snap_token', 255)->nullable();
            $table->string('order_id', 100)->unique()->nullable();
            $table->string('transaction_id', 100)->nullable();
            $table->text('catatan_user')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->dateTime('tanggal_konfirmasi')->nullable();
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('kamar_id')->references('id')->on('kamar')->restrictOnDelete();
            $table->foreign('dikonfirmasi_oleh')->references('id')->on('users')->nullOnDelete();
            $table->foreign('penyewa_id')->references('id')->on('penyewa')->nullOnDelete();

            // Indeks perlindungan untuk pencarian query dan optimasi performance
            $table->index('status');
            $table->index('kamar_id');
            $table->index(['tanggal_mulai', 'tanggal_selesai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservasi');
    }
};
