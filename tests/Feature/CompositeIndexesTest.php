<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CompositeIndexesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the required composite indexes exist on the chat_messages and tagihan tables.
     */
    public function test_composite_indexes_exist(): void
    {
        // 1. Verify chat_messages composite index (reservasi_id, id)
        $chatMessagesIndexes = Schema::getIndexes('chat_messages');
        $hasChatIndex = collect($chatMessagesIndexes)->contains(function ($index) {
            // Check by name or columns
            $cols = $index['columns'] ?? [];

            return $index['name'] === 'chat_messages_reservasi_id_id_index'
                || (count($cols) === 2 && $cols[0] === 'reservasi_id' && $cols[1] === 'id');
        });

        $this->assertTrue(
            $hasChatIndex,
            'Composite index (reservasi_id, id) is missing on the chat_messages table.'
        );

        // 2. Verify tagihan composite index (penyewa_id, status)
        $tagihanIndexes = Schema::getIndexes('tagihan');
        $hasTagihanIndex = collect($tagihanIndexes)->contains(function ($index) {
            // Check by name or columns
            $cols = $index['columns'] ?? [];

            return $index['name'] === 'tagihan_penyewa_id_status_index'
                || (count($cols) === 2 && $cols[0] === 'penyewa_id' && $cols[1] === 'status');
        });

        $this->assertTrue(
            $hasTagihanIndex,
            'Composite index (penyewa_id, status) is missing on the tagihan table.'
        );
    }
}
