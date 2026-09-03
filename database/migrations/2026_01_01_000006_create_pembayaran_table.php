<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('tagihan_id');
            $table->string('transaction_id', 100)->unique();
            $table->string('payment_type', 50)->nullable();
            $table->string('bank', 20)->nullable();
            $table->string('va_number', 30)->nullable();
            $table->decimal('nominal', 12, 2);
            $table->string('status_midtrans', 50)->nullable();
            $table->unsignedInteger('dikonfirmasi_oleh')->nullable();
            $table->string('signature_key', 255)->nullable();
            $table->json('response_json')->nullable();
            $table->dateTime('tanggal_bayar')->nullable();
            $table->string('pdf_path', 255)->nullable();
            $table->timestamps();

            $table->foreign('tagihan_id')->references('id')->on('tagihan')->onDelete('restrict');
            $table->foreign('dikonfirmasi_oleh')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
