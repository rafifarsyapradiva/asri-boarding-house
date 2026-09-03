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
        Schema::table('chat_messages', function (Blueprint $table) {
            // 1. Tambah indeks komposit baru untuk optimasi kueri fetch chat (reservasi_id, id)
            $table->index(['reservasi_id', 'id'], 'chat_messages_reservasi_id_id_index');

            // 2. Tambah indeks komposit baru untuk optimasi kueri update is_read (reservasi_id, is_read, sender_id)
            $table->index(['reservasi_id', 'is_read', 'sender_id'], 'chat_messages_reservasi_read_status_index');
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            // 3. Hapus indeks komposit lama yang kurang efisien
            $table->dropIndex('chat_messages_reservasi_id_created_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->index(['reservasi_id', 'created_at'], 'chat_messages_reservasi_id_created_at_index');
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropIndex('chat_messages_reservasi_id_id_index');
            $table->dropIndex('chat_messages_reservasi_read_status_index');
        });
    }
};
