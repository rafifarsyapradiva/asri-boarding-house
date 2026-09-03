<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            
            // Foreign Key Columns
            $table->unsignedBigInteger('reservasi_id');
            $table->unsignedInteger('sender_id');
            
            // Attribute Columns
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamp('created_at')->useCurrent();

            // Foreign Key Constraints
            $table->foreign('reservasi_id')->references('id')->on('reservasi')->cascadeOnDelete();
            $table->foreign('sender_id')->references('id')->on('users')->cascadeOnDelete();

            // TODO: Tambahkan pengaturan indeks pada kolom 'reservasi_id' dan 'created_at'.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
