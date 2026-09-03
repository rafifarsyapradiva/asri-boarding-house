<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\GuestChatThread;
use App\Models\GuestChatMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestChatTest extends TestCase
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

    private function createPenyewa(): User
    {
        return User::create([
            'nama' => 'Penyewa Dummy',
            'email' => 'penyewa@dummy.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567890',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);
    }

    /**
     * Test guest can start a chat thread with valid data.
     */
    public function test_guest_bisa_memulai_thread_chat_dengan_data_valid(): void
    {
        $response = $this->postJson(route('guest-chat.start'), [
            'name' => 'Budi Santoso',
            'no_hp' => '081234567890',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'token', 'thread']);

        $this->assertDatabaseHas('guest_chat_threads', [
            'name' => 'Budi Santoso',
            'no_hp' => '081234567890',
            'status' => 'active',
        ]);
    }

    /**
     * Test guest start chat validation fails with invalid data.
     */
    public function test_guest_memulai_chat_validasi_gagal_jika_data_tidak_valid(): void
    {
        $response = $this->postJson(route('guest-chat.start'), [
            'name' => '',
            'no_hp' => 'invalid-phone',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'no_hp']);
    }

    /**
     * Test guest can send messages when token is valid.
     */
    public function test_guest_bisa_mengirim_pesan_dengan_token_valid(): void
    {
        $thread = GuestChatThread::create([
            'session_token' => hash('sha256', 'test-token-uuid-1234'),
            'name' => 'Budi Santoso',
            'no_hp' => '081234567890',
            'status' => 'active',
        ]);

        $response = $this->withHeaders(['X-Guest-Chat-Token' => 'test-token-uuid-1234'])
            ->postJson(route('guest-chat.send'), [
                'message' => 'Halo, apakah kamar VIP kosong?',
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['success' => true]);

        $this->assertDatabaseHas('guest_chat_messages', [
            'guest_chat_thread_id' => $thread->id,
            'sender_type' => 'guest',
            'message' => 'Halo, apakah kamar VIP kosong?',
        ]);
    }

    /**
     * Test guest cannot send messages with invalid token.
     */
    public function test_guest_tidak_bisa_mengirim_pesan_tanpa_token_valid(): void
    {
        $response = $this->postJson(route('guest-chat.send'), [
            'message' => 'Halo, apakah kamar VIP kosong?',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test guest can fetch messages.
     */
    public function test_guest_bisa_mengambil_pesan_dan_pesan_admin_ditandai_terbaca(): void
    {
        $admin = $this->createAdmin();

        $thread = GuestChatThread::create([
            'session_token' => hash('sha256', 'test-token-uuid-1234'),
            'name' => 'Budi Santoso',
            'no_hp' => '081234567890',
            'status' => 'active',
        ]);

        $msg1 = GuestChatMessage::create([
            'guest_chat_thread_id' => $thread->id,
            'sender_type' => 'guest',
            'message' => 'Halo Admin',
            'is_read' => false,
        ]);

        $msg2 = GuestChatMessage::create([
            'guest_chat_thread_id' => $thread->id,
            'sender_type' => 'admin',
            'sender_id' => $admin->id,
            'message' => 'Halo Budi, ya ada yang bisa kami bantu?',
            'is_read' => false,
        ]);

        $response = $this->withHeaders(['X-Guest-Chat-Token' => 'test-token-uuid-1234'])
            ->getJson(route('guest-chat.messages'));

        $response->assertStatus(200)
            ->assertJsonStructure(['messages', 'status'])
            ->assertJsonCount(2, 'messages');

        $this->assertTrue($msg2->fresh()->is_read);
    }

    /**
     * Test admin can access guest chats index page.
     */
    public function test_admin_bisa_mengakses_halaman_manajemen_obrolan_guest(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)
            ->get(route('admin.guest-chats.index'));

        $response->assertStatus(200);
    }

    /**
     * Test non-admin role (tenant) cannot access guest chats page.
     */
    public function test_non_admin_tidak_bisa_mengakses_halaman_manajemen_obrolan_guest(): void
    {
        $penyewa = $this->createPenyewa();

        $response = $this->actingAs($penyewa)
            ->get(route('admin.guest-chats.index'));

        // Non-admin will trigger role middleware which redirects or aborts (403)
        $response->assertStatus(403);
    }

    /**
     * Test admin can list all guest threads.
     */
    public function test_admin_bisa_mengambil_daftar_thread_guest_via_ajax(): void
    {
        $admin = $this->createAdmin();

        $thread = GuestChatThread::create([
            'session_token' => 'token-1',
            'name' => 'Budi Santoso',
            'no_hp' => '081234567890',
            'status' => 'active',
        ]);

        GuestChatMessage::create([
            'guest_chat_thread_id' => $thread->id,
            'sender_type' => 'guest',
            'message' => 'Tanya Kamar',
            'is_read' => false,
        ]);

        $response = $this->actingAs($admin)
            ->getJson(route('admin.guest-chats.threads'));

        $response->assertStatus(200)
            ->assertJsonStructure(['threads' => ['data']])
            ->assertJsonCount(1, 'threads.data')
            ->assertJsonFragment([
                'name' => 'Budi Santoso',
                'unread_count' => 1,
            ]);
    }

    /**
     * Test admin can fetch messages and guest messages are marked as read.
     */
    public function test_admin_bisa_mengambil_pesan_thread_dan_pesan_guest_ditandai_terbaca(): void
    {
        $admin = $this->createAdmin();

        $thread = GuestChatThread::create([
            'session_token' => 'token-1',
            'name' => 'Budi Santoso',
            'no_hp' => '081234567890',
            'status' => 'active',
        ]);

        $msg = GuestChatMessage::create([
            'guest_chat_thread_id' => $thread->id,
            'sender_type' => 'guest',
            'message' => 'Halo Admin',
            'is_read' => false,
        ]);

        $response = $this->actingAs($admin)
            ->getJson(route('admin.guest-chats.messages', $thread->id));

        $response->assertStatus(200)
            ->assertJsonStructure(['messages', 'status'])
            ->assertJsonCount(1, 'messages');

        $this->assertTrue($msg->fresh()->is_read);
    }

    /**
     * Test admin can send reply message.
     */
    public function test_admin_bisa_mengirim_pesan_balasan(): void
    {
        $admin = $this->createAdmin();

        $thread = GuestChatThread::create([
            'session_token' => 'token-1',
            'name' => 'Budi Santoso',
            'no_hp' => '081234567890',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)
            ->postJson(route('admin.guest-chats.send', $thread->id), [
                'message' => 'Jawaban Admin',
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['success' => true]);

        $this->assertDatabaseHas('guest_chat_messages', [
            'guest_chat_thread_id' => $thread->id,
            'sender_type' => 'admin',
            'sender_id' => $admin->id,
            'message' => 'Jawaban Admin',
        ]);
    }

    /**
     * Test admin can close thread.
     */
    public function test_admin_bisa_menutup_obrolan(): void
    {
        $admin = $this->createAdmin();

        $thread = GuestChatThread::create([
            'session_token' => 'token-1',
            'name' => 'Budi Santoso',
            'no_hp' => '081234567890',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)
            ->postJson(route('admin.guest-chats.close', $thread->id));

        $response->assertStatus(200)
            ->assertJsonFragment(['success' => true]);

        $this->assertEquals('closed', $thread->fresh()->status);
        $this->assertDatabaseHas('guest_chat_messages', [
            'guest_chat_thread_id' => $thread->id,
            'message' => '___CHAT_CLOSED___',
        ]);
    }

    /**
     * Test admin can delete guest chat thread permanently.
     */
    public function test_admin_bisa_menghapus_obrolan_secara_permanen(): void
    {
        $admin = $this->createAdmin();

        $thread = GuestChatThread::create([
            'session_token' => 'token-1',
            'name' => 'Budi Santoso',
            'no_hp' => '081234567890',
            'status' => 'active',
        ]);

        GuestChatMessage::create([
            'guest_chat_thread_id' => $thread->id,
            'sender_type' => 'guest',
            'message' => 'Pesan Budi',
            'is_read' => false,
        ]);

        $response = $this->actingAs($admin)
            ->deleteJson(route('admin.guest-chats.destroy', $thread->id));

        $response->assertStatus(200)
            ->assertJsonFragment(['success' => true]);

        $this->assertDatabaseMissing('guest_chat_threads', ['id' => $thread->id]);
        $this->assertDatabaseMissing('guest_chat_messages', ['guest_chat_thread_id' => $thread->id]);
    }

    /**
     * Test that guest fetchMessages limits the initial payload to the last 50 messages.
     */
    public function test_guest_fetch_messages_limits_to_latest_50_messages(): void
    {
        $thread = GuestChatThread::create([
            'session_token' => hash('sha256', 'limit-test-token'),
            'name' => 'Budi Santoso',
            'no_hp' => '081234567890',
            'status' => 'active',
        ]);

        // Create 60 messages
        for ($i = 1; $i <= 60; $i++) {
            GuestChatMessage::create([
                'guest_chat_thread_id' => $thread->id,
                'sender_type' => 'guest',
                'message' => "Message number {$i}",
                'is_read' => false,
            ]);
        }

        // Fetch without 'after' parameter
        $response = $this->withHeaders(['X-Guest-Chat-Token' => 'limit-test-token'])
            ->getJson(route('guest-chat.messages'));

        $response->assertStatus(200)
            ->assertJsonCount(50, 'messages');

        $messages = $response->json('messages');
        // The first message in the returned list should be message number 11 (offset by 10)
        $this->assertEquals('Message number 11', $messages[0]['message']);
        // The last message in the returned list should be message number 60
        $this->assertEquals('Message number 60', $messages[49]['message']);

        // Fetch with 'after' parameter (e.g. after message ID of Message 10)
        $tenthMessageId = GuestChatMessage::where('message', 'Message number 10')->first()->id;
        $responseWithAfter = $this->withHeaders(['X-Guest-Chat-Token' => 'limit-test-token'])
            ->getJson(route('guest-chat.messages') . "?after={$tenthMessageId}");

        // It should return 50 messages (Message 11 to 60)
        $responseWithAfter->assertStatus(200)
            ->assertJsonCount(50, 'messages');
    }

    /**
     * Test that guest sendMessage returns 403 when thread is deleted.
     */
    public function test_guest_send_message_fails_gracefully_with_403_when_thread_is_deleted(): void
    {
        $thread = GuestChatThread::create([
            'session_token' => hash('sha256', 'delete-race-token'),
            'name' => 'Budi Race',
            'no_hp' => '081234567890',
            'status' => 'active',
        ]);

        // Simulating the thread being deleted before message creation
        $threadId = $thread->id;
        $thread->delete();

        $response = $this->withHeaders(['X-Guest-Chat-Token' => 'delete-race-token'])
            ->postJson(route('guest-chat.send'), [
                'message' => 'Should fail with 403',
            ]);

        $response->assertStatus(403)
            ->assertJsonFragment(['success' => false, 'message' => 'Sesi chat telah kedaluwarsa atau tidak valid.']);

        $this->assertDatabaseMissing('guest_chat_messages', [
            'guest_chat_thread_id' => $threadId,
            'message' => 'Should fail with 403',
        ]);
    }

    /**
     * Test that guest cannot send message when thread is closed.
     */
    public function test_guest_tidak_bisa_mengirim_pesan_pada_thread_closed(): void
    {
        $thread = GuestChatThread::create([
            'session_token' => hash('sha256', 'closed-test-token'),
            'name' => 'Budi Closed',
            'no_hp' => '081234567890',
            'status' => 'closed',
        ]);

        $response = $this->withHeaders(['X-Guest-Chat-Token' => 'closed-test-token'])
            ->postJson(route('guest-chat.send'), [
                'message' => 'Halo, apakah kamar VIP kosong?',
            ]);

        $response->assertStatus(403)
            ->assertJsonFragment([
                'success' => false,
                'message' => 'Obrolan ini telah ditutup secara permanen oleh Admin.'
            ]);

        $this->assertDatabaseMissing('guest_chat_messages', [
            'guest_chat_thread_id' => $thread->id,
            'message' => 'Halo, apakah kamar VIP kosong?',
        ]);
    }

    /**
     * Test that admin can send message when thread is closed, and it automatically becomes active.
     */
    public function test_admin_bisa_mengirim_pesan_pada_thread_closed_dan_otomatis_aktif(): void
    {
        $admin = $this->createAdmin();

        $thread = GuestChatThread::create([
            'session_token' => 'closed-test-token-2',
            'name' => 'Budi Closed 2',
            'no_hp' => '081234567890',
            'status' => 'closed',
        ]);

        $response = $this->actingAs($admin)
            ->postJson(route('admin.guest-chats.send', $thread->id), [
                'message' => 'Halo Budi, ada yang bisa saya bantu?',
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'success' => true
            ]);

        $this->assertEquals('active', $thread->fresh()->status);

        $this->assertDatabaseHas('guest_chat_messages', [
            'guest_chat_thread_id' => $thread->id,
            'message' => 'Halo Budi, ada yang bisa saya bantu?',
        ]);
    }
}
