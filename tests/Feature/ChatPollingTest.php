<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use App\Models\Reservasi;
use App\Models\ChatMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatPollingTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private User $otherUser;
    private User $admin;
    private Kamar $kamar;
    private Reservasi $reservasi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::create([
            'nama' => 'Owner Penyewa',
            'email' => 'owner@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567891',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $this->otherUser = User::create([
            'nama' => 'Other Penyewa',
            'email' => 'other@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567892',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $this->admin = User::create([
            'nama' => 'Admin Kost',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567893',
            'role' => 'admin',
            'is_active' => 1,
        ]);

        $this->kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        $this->reservasi = Reservasi::create([
            'user_id' => $this->owner->id,
            'kamar_id' => $this->kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1000000,
            'is_dp' => false,
            'status' => 'pending',
            'order_id' => 'RSV-CHAT-TEST'
        ]);
    }

    /**
     * Test guest cannot access chat endpoints.
     */
    public function test_guest_tidak_dapat_mengakses_chat(): void
    {
        $this->getJson(route('chat.fetch', $this->reservasi->id))
            ->assertStatus(401);

        $this->postJson(route('chat.send', $this->reservasi->id), ['message' => 'Hello'])
            ->assertStatus(401);
    }

    /**
     * Test other user cannot access chat endpoints (403).
     */
    public function test_calon_penyewa_hanya_bisa_membaca_chat_reservasi_milik_sendiri(): void
    {
        $this->actingAs($this->otherUser)
            ->getJson(route('chat.fetch', $this->reservasi->id))
            ->assertStatus(403);

        $this->actingAs($this->otherUser)
            ->postJson(route('chat.send', $this->reservasi->id), ['message' => 'Hello'])
            ->assertStatus(403);
    }

    /**
     * Test owner and admin can fetch and send messages.
     */
    public function test_owner_dan_admin_dapat_mengakses_dan_mengirim_chat(): void
    {
        // 1. Send message as Owner
        $response = $this->actingAs($this->owner)
            ->postJson(route('chat.send', $this->reservasi->id), [
                'message' => 'Halo Admin, kamar ini masih tersedia?'
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['success' => true]);

        $this->assertDatabaseHas('chat_messages', [
            'reservasi_id' => $this->reservasi->id,
            'sender_id' => $this->owner->id,
            'message' => 'Halo Admin, kamar ini masih tersedia?',
            'is_read' => false
        ]);

        // 2. Fetch messages as Admin
        $responseFetch = $this->actingAs($this->admin)
            ->getJson(route('chat.fetch', $this->reservasi->id));

        $responseFetch->assertStatus(200)
            ->assertJsonStructure(['messages'])
            ->assertJsonCount(1, 'messages');

        // 3. Admin replying
        $responseReply = $this->actingAs($this->admin)
            ->postJson(route('chat.send', $this->reservasi->id), [
                'message' => 'Ya, kamar tersedia dan siap dihuni.'
            ]);

        $responseReply->assertStatus(200);

        // 4. Owner fetching messages, which should mark admin message as read
        $responseOwnerFetch = $this->actingAs($this->owner)
            ->getJson(route('chat.fetch', $this->reservasi->id));

        $responseOwnerFetch->assertStatus(200)
            ->assertJsonCount(2, 'messages');

        // Assert that the message sent by Admin is marked as read now
        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $this->admin->id,
            'is_read' => true
        ]);
    }

    /**
     * Test message validation.
     */
    public function test_pesan_chat_wajib_diisi_dan_maksimal_1000_karakter(): void
    {
        // Empty message
        $this->actingAs($this->owner)
            ->postJson(route('chat.send', $this->reservasi->id), ['message' => ''])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['message']);

        // Message too long (1001 chars)
        $this->actingAs($this->owner)
            ->postJson(route('chat.send', $this->reservasi->id), ['message' => str_repeat('a', 1001)])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    }

    /**
     * Test that fetching messages for a reservation limits the initial payload to the last 50 messages.
     */
    public function test_reservation_chat_fetch_messages_limits_to_latest_50_messages(): void
    {
        // Create 60 messages in the database
        for ($i = 1; $i <= 60; $i++) {
            ChatMessage::create([
                'reservasi_id' => $this->reservasi->id,
                'sender_id' => $this->owner->id,
                'message' => "Reservasi message {$i}",
                'is_read' => false,
            ]);
        }

        // Fetch without 'after' parameter
        $response = $this->actingAs($this->owner)
            ->getJson(route('chat.fetch', $this->reservasi->id));

        $response->assertStatus(200)
            ->assertJsonCount(50, 'messages');

        $messages = $response->json('messages');
        // The first message in the returned list should be message number 11
        $this->assertEquals('Reservasi message 11', $messages[0]['message']);
        // The last message in the returned list should be message number 60
        $this->assertEquals('Reservasi message 60', $messages[49]['message']);

        // Fetch with 'after' parameter
        $tenthMessageId = ChatMessage::where('message', 'Reservasi message 10')->first()->id;
        $responseWithAfter = $this->actingAs($this->owner)
            ->getJson(route('chat.fetch', $this->reservasi->id) . "?after={$tenthMessageId}");

        // It should return 50 messages (Message 11 to 60)
        $responseWithAfter->assertStatus(200)
            ->assertJsonCount(50, 'messages');
    }

    /**
     * Test that user cannot send message when reservation status is closed (e.g. dikonfirmasi or batal).
     */
    public function test_pembatasan_kirim_pesan_pada_reservasi_chat_closed(): void
    {
        // Cancel the reservation
        $this->reservasi->update(['status' => 'batal']);

        $response = $this->actingAs($this->owner)
            ->postJson(route('chat.send', $this->reservasi->id), [
                'message' => 'Halo Admin, masih bisa diproses?',
            ]);

        $response->assertStatus(403)
            ->assertJsonFragment([
                'message' => 'Obrolan ini telah ditutup secara permanen oleh Admin.'
            ]);

        $this->assertDatabaseMissing('chat_messages', [
            'reservasi_id' => $this->reservasi->id,
            'message' => 'Halo Admin, masih bisa diproses?',
        ]);
    }

    /**
     * Test chat box only loads if reservation status is pending.
     */
    public function test_chat_box_hanya_bisa_dimuat_jika_status_reservasi_pending(): void
    {
        // 1. Pending status: Chat box should be visible
        $this->reservasi->update(['status' => 'pending']);
        $responsePending = $this->actingAs($this->owner)
            ->get(route('reservasi.show', $this->reservasi->id));

        $responsePending->assertStatus(200);
        $responsePending->assertSee('Obrolan Diskusi Reservasi');

        // 2. DP status: Chat box should not be visible (should redirect to private chat room link)
        $this->reservasi->update(['status' => 'dp']);
        $responseDp = $this->actingAs($this->owner)
            ->get(route('reservasi.show', $this->reservasi->id));

        $responseDp->assertStatus(200);
        $responseDp->assertDontSee('Obrolan Diskusi Reservasi');
        $responseDp->assertSee('Obrolan detail dinonaktifkan. Silakan berdiskusi di');
    }

    /**
     * Test polling returns latest messages based on pointer after_id.
     */
    public function test_polling_chat_mengembalikan_pesan_terakhir_berdasarkan_pointer_after_id(): void
    {
        // Create 3 messages
        $msg1 = ChatMessage::create([
            'reservasi_id' => $this->reservasi->id,
            'sender_id' => $this->owner->id,
            'message' => 'Message 1',
            'is_read' => false
        ]);

        $msg2 = ChatMessage::create([
            'reservasi_id' => $this->reservasi->id,
            'sender_id' => $this->admin->id,
            'message' => 'Message 2',
            'is_read' => false
        ]);

        $msg3 = ChatMessage::create([
            'reservasi_id' => $this->reservasi->id,
            'sender_id' => $this->owner->id,
            'message' => 'Message 3',
            'is_read' => false
        ]);

        // Request with after = $msg1->id
        $response = $this->actingAs($this->owner)
            ->getJson(route('chat.fetch', $this->reservasi->id) . "?after={$msg1->id}");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'messages');

        $messages = $response->json('messages');
        $this->assertEquals('Message 2', $messages[0]['message']);
        $this->assertEquals('Message 3', $messages[1]['message']);
    }

    /**
     * Test guest cannot access /api/chat endpoints.
     */
    public function test_guest_tidak_dapat_mengakses_api_chat(): void
    {
        $this->getJson(route('api.chat.fetch', $this->reservasi->id))
            ->assertStatus(401);

        $this->postJson(route('api.chat.send', $this->reservasi->id), ['message' => 'Hello'])
            ->assertStatus(401);
    }

    /**
     * Test other user cannot access /api/chat endpoints (IDOR protection).
     */
    public function test_calon_penyewa_hanya_bisa_membaca_api_chat_reservasi_milik_sendiri(): void
    {
        $this->actingAs($this->otherUser)
            ->getJson(route('api.chat.fetch', $this->reservasi->id))
            ->assertStatus(403);

        $this->actingAs($this->otherUser)
            ->postJson(route('api.chat.send', $this->reservasi->id), ['message' => 'Hello'])
            ->assertStatus(403);
    }

    /**
     * Test owner and admin can access and send via /api/chat endpoints.
     */
    public function test_owner_dan_admin_dapat_mengakses_dan_mengirim_api_chat(): void
    {
        // 1. Fetch as Owner
        $responseFetch = $this->actingAs($this->owner)
            ->getJson(route('api.chat.fetch', $this->reservasi->id));
        $responseFetch->assertStatus(200);

        // 2. Send as Owner
        $responseSend = $this->actingAs($this->owner)
            ->postJson(route('api.chat.send', $this->reservasi->id), ['message' => 'Hello Admin']);
        $responseSend->assertStatus(200);

        // 3. Fetch as Admin
        $responseAdminFetch = $this->actingAs($this->admin)
            ->getJson(route('api.chat.fetch', $this->reservasi->id));
        $responseAdminFetch->assertStatus(200);

        // 4. Send as Admin
        $responseAdminSend = $this->actingAs($this->admin)
            ->postJson(route('api.chat.send', $this->reservasi->id), ['message' => 'Hello Tenant']);
        $responseAdminSend->assertStatus(200);
    }
}

