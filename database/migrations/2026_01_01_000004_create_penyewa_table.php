<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyewa', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->unique();
            $table->unsignedInteger('kamar_id');
            $table->string('nik', 20)->unique();
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->tinyInteger('tanggal_billing')->default(1);
            $table->enum('tipe_sewa', ['harian', 'mingguan', 'bulanan'])->default('bulanan');
            $table->tinyInteger('durasi')->default(1);
            $table->decimal('deposit', 12, 2)->default(0);
            $table->string('no_wali', 20);
            $table->string('nama_wali', 100);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('kamar_id')->references('id')->on('kamar')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyewa');
    }
};
