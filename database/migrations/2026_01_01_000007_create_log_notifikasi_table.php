<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_notifikasi', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('penyewa_id');
            $table->unsignedInteger('tagihan_id')->nullable();
            $table->enum('channel', ['whatsapp', 'email', 'system']);
            $table->string('event', 50);
            $table->enum('status', ['sukses', 'gagal']);
            $table->text('pesan')->nullable();
            $table->text('error_msg')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('penyewa_id')->references('id')->on('penyewa')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_notifikasi');
    }
};
