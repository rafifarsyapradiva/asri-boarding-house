<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\GuestChatThread;
use App\Models\GuestChatMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class GuestChatOptimizationTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::create([
            'nama' => 'Admin Asep',
            'email' => 'admin-asep@gmail.com',
            'password' => bcrypt('password'),
            'no_hp' => '6281234567890',
            'role' => 'admin',
            'is_active' => 1,
        ]);
    }

    /**
     * Test that closed thread status is cached and served from RAM (Cache) on subsequent polls.
     */
    public function test_closed_thread_status_is_cached_and_serves_from_cache(): void
    {
        // 1. Create a guest thread and close it
        $token = 'test-cache-token-uuid';
        $hashedToken = hash('sha256', $token);

        $thread = GuestChatThread::create([
            'session_token' => $hashedToken,
            'name' => 'Budi Cache',
            'no_hp' => '081234567890',
            'status' => 'closed',
        ]);

        // Create a message in thread
        GuestChatMessage::create([
            'guest_chat_thread_id' => $thread->id,
            'sender_type' => 'guest',
            'message' => 'Pesan sebelum ditutup',
        ]);

        // Clear cache first to ensure a clean state
        Cache::flush();

        // 2. Fetch messages (first load - Cache Miss, queries DB and populates Cache)
        $response1 = $this->withHeaders(['X-Guest-Chat-Token' => $token])
            ->getJson(route('guest-chat.messages'));

        $response1->assertStatus(200)
            ->assertJsonFragment(['status' => 'closed'])
            ->assertJsonCount(1, 'messages');

        // Verify it exists in cache
        $this->assertTrue(Cache::has("guest_chat_closed_status_{$hashedToken}"));
        $this->assertEquals('closed', Cache::get("guest_chat_closed_status_{$hashedToken}"));

        // 3. Delete the thread from DB completely to simulate database independence
        $thread->delete();

        // 4. Fetch messages again (Cache Hit - returns from Cache directly, no MySQL query to thread table)
        $response2 = $this->withHeaders(['X-Guest-Chat-Token' => $token])
            ->getJson(route('guest-chat.messages'));

        // It should still return successfully with closed status and cached messages from cache
        $response2->assertStatus(200)
            ->assertJsonFragment(['status' => 'closed'])
            ->assertJsonCount(1, 'messages');

        // 5. Send message on closed thread (Cache Hit - returns 403 instantly without DB queries)
        $response3 = $this->withHeaders(['X-Guest-Chat-Token' => $token])
            ->postJson(route('guest-chat.send'), [
                'message' => 'Should fail instantly via Cache',
            ]);

        $response3->assertStatus(403)
            ->assertJsonFragment([
                'success' => false,
                'message' => 'Obrolan ini telah ditutup secara permanen oleh Admin.'
            ]);
    }

    /**
     * Test admin actions update and clear short-term cache appropriately.
     */
    public function test_admin_actions_synchronize_cache(): void
    {
        $admin = $this->createAdmin();

        $token = 'sync-cache-token-uuid';
        $hashedToken = hash('sha256', $token);

        $thread = GuestChatThread::create([
            'session_token' => $hashedToken,
            'name' => 'Budi Sync',
            'no_hp' => '081234567890',
            'status' => 'active',
        ]);

        Cache::flush();

        // 1. Close thread via admin
        $responseClose = $this->actingAs($admin)
            ->postJson(route('admin.guest-chats.close', $thread->id));

        $responseClose->assertStatus(200);

        // Assert status is cached as closed immediately
        $this->assertEquals('closed', Cache::get("guest_chat_closed_status_{$hashedToken}"));

        // 2. Destroy thread via admin
        $responseDestroy = $this->actingAs($admin)
            ->deleteJson(route('admin.guest-chats.destroy', $thread->id));

        $responseDestroy->assertStatus(200);

        // Assert cache is completely cleared for this token
        $this->assertFalse(Cache::has("guest_chat_closed_status_{$hashedToken}"));
        $this->assertFalse(Cache::has("guest_chat_messages_{$hashedToken}"));
    }

    /**
     * Test closing a thread is idempotent and does not create duplicate system messages.
     */
    public function test_admin_close_thread_is_idempotent(): void
    {
        $admin = $this->createAdmin();

        $token = 'idempotent-close-uuid';
        $hashedToken = hash('sha256', $token);

        $thread = GuestChatThread::create([
            'session_token' => $hashedToken,
            'name' => 'Budi Idempotent',
            'no_hp' => '081234567890',
            'status' => 'active',
        ]);

        // First close
        $response1 = $this->actingAs($admin)
            ->postJson(route('admin.guest-chats.close', $thread->id));
        $response1->assertStatus(200);

        // Second close
        $response2 = $this->actingAs($admin)
            ->postJson(route('admin.guest-chats.close', $thread->id));
        $response2->assertStatus(200);

        // Verify only 1 system message is created
        $msgCount = GuestChatMessage::where('guest_chat_thread_id', $thread->id)
            ->where('message', '___CHAT_CLOSED___')
            ->count();
        $this->assertEquals(1, $msgCount);
    }

    /**
     * Test chat-guest:prune command deletes closed threads older than 90 days.
     */
    public function test_prune_command_deletes_threads_older_than_90_days(): void
    {
        // 1. Closed thread, 95 days old (Should be deleted)
        $oldClosedThread = GuestChatThread::create([
            'session_token' => 'old-closed',
            'name' => 'Old Closed',
            'no_hp' => '081234567890',
            'status' => 'closed',
        ]);
        $oldClosedThread->updated_at = now()->subDays(95);
        $oldClosedThread->created_at = now()->subDays(95);
        $oldClosedThread->timestamps = false;
        $oldClosedThread->save();

        $oldMsg = GuestChatMessage::create([
            'guest_chat_thread_id' => $oldClosedThread->id,
            'sender_type' => 'guest',
            'message' => 'Pesan kuno',
        ]);
        $oldMsg->created_at = now()->subDays(95);
        $oldMsg->timestamps = false;
        $oldMsg->save();

        // 2. Closed thread, 5 days old (Should remain)
        $recentClosedThread = GuestChatThread::create([
            'session_token' => 'recent-closed',
            'name' => 'Recent Closed',
            'no_hp' => '081234567890',
            'status' => 'closed',
        ]);
        $recentClosedThread->updated_at = now()->subDays(5);
        $recentClosedThread->created_at = now()->subDays(5);
        $recentClosedThread->timestamps = false;
        $recentClosedThread->save();

        // 3. Active thread, 95 days old (Should remain)
        $oldActiveThread = GuestChatThread::create([
            'session_token' => 'old-active',
            'name' => 'Old Active',
            'no_hp' => '081234567890',
            'status' => 'active',
        ]);
        $oldActiveThread->updated_at = now()->subDays(95);
        $oldActiveThread->created_at = now()->subDays(95);
        $oldActiveThread->timestamps = false;
        $oldActiveThread->save();

        // Run the command
        Artisan::call('chat-guest:prune');

        // Assert database records
        $this->assertDatabaseMissing('guest_chat_threads', ['id' => $oldClosedThread->id]);
        $this->assertDatabaseMissing('guest_chat_messages', ['id' => $oldMsg->id]); // cascade delete verified

        $this->assertDatabaseHas('guest_chat_threads', ['id' => $recentClosedThread->id]);
        $this->assertDatabaseHas('guest_chat_threads', ['id' => $oldActiveThread->id]);
    }
}
