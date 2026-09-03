<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kamar_fasilitas', function (Blueprint $table) {
            $table->unsignedInteger('kamar_id');
            $table->unsignedInteger('fasilitas_id');
            
            $table->foreign('kamar_id')->references('id')->on('kamar')->onDelete('cascade');
            $table->foreign('fasilitas_id')->references('id')->on('fasilitas')->onDelete('cascade');
            
            $table->primary(['kamar_id', 'fasilitas_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kamar_fasilitas');
    }
};
