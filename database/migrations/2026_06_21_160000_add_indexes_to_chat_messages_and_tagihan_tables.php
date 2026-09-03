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
            $indexes = Schema::getIndexes('chat_messages');
            $hasIndex = collect($indexes)->contains(function ($index) {
                return $index['name'] === 'chat_messages_reservasi_id_created_at_index';
            });

            if (! $hasIndex) {
                $table->index(['reservasi_id', 'created_at']);
            }
        });

        // Drop the single-column foreign key index if it exists to avoid redundancy,
        // since the composite index covers reservasi_id as its leftmost column.
        Schema::table('chat_messages', function (Blueprint $table) {
            $indexes = Schema::getIndexes('chat_messages');
            $hasSingle = collect($indexes)->contains(function ($index) {
                return $index['name'] === 'chat_messages_reservasi_id_foreign';
            });

            if ($hasSingle) {
                $table->dropIndex('chat_messages_reservasi_id_foreign');
            }
        });

        Schema::table('tagihan', function (Blueprint $table) {
            $indexes = Schema::getIndexes('tagihan');
            $hasIndex = collect($indexes)->contains(function ($index) {
                return $index['name'] === 'tagihan_penyewa_id_status_index';
            });

            if (! $hasIndex) {
                $table->index(['penyewa_id', 'status']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $indexes = Schema::getIndexes('chat_messages');
            $hasIndex = collect($indexes)->contains(function ($index) {
                return $index['name'] === 'chat_messages_reservasi_id_created_at_index';
            });

            if ($hasIndex) {
                // MySQL requires an index on the foreign key column (reservasi_id).
                // Before dropping the composite index (which starts with reservasi_id),
                // we must ensure a single-column index on reservasi_id exists so the foreign key constraint remains satisfied.
                $hasSingle = collect($indexes)->contains(function ($idx) {
                    return $idx['name'] === 'chat_messages_reservasi_id_foreign';
                });

                if (! $hasSingle) {
                    $table->index('reservasi_id', 'chat_messages_reservasi_id_foreign');
                }

                $table->dropIndex('chat_messages_reservasi_id_created_at_index');
            }
        });

        Schema::table('tagihan', function (Blueprint $table) {
            $indexes = Schema::getIndexes('tagihan');
            $hasIndex = collect($indexes)->contains(function ($index) {
                return $index['name'] === 'tagihan_penyewa_id_status_index';
            });

            if ($hasIndex) {
                $table->dropIndex(['penyewa_id', 'status']);
            }
        });
    }
};
