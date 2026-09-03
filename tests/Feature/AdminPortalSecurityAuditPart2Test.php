<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\GuestChatThread;
use App\Models\GuestChatMessage;
use App\Models\LogNotifikasi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminPortalSecurityAuditPart2Test extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private GuestChatThread $activeThread;
    private GuestChatThread $closedThread;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin user
        $this->admin = User::create([
            'nama' => 'Admin Utama',
            'email' => 'admin.kost@gmail.com',
            'password' => bcrypt('AdminPassword123'),
            'no_hp' => '6281233334444',
            'role' => 'admin',
            'is_active' => 1,
            'require_password_change' => false
        ]);

        // Create active chat thread
        $this->activeThread = GuestChatThread::create([
            'session_token' => hash('sha256', 'token-active-123'),
            'name' => 'Penyewa Aktif',
            'no_hp' => '6281299999999',
            'status' => 'active'
        ]);

        // Create closed chat thread
        $this->closedThread = GuestChatThread::create([
            'session_token' => hash('sha256', 'token-closed-456'),
            'name' => 'Penyewa Selesai',
            'no_hp' => '6281288888888',
            'status' => 'closed'
        ]);
    }

    /**
     * Test 1: Guest trying to post message to a closed thread returns 403 Forbidden
     */
    public function test_guest_cannot_send_message_to_closed_chat_thread(): void
    {
        // Use X-Guest-Chat-Token header to bypass cookie encryption issues in feature tests
        $response = $this->withHeader('X-Guest-Chat-Token', 'token-closed-456')
            ->postJson(route('guest-chat.send'), [
                'message' => 'Halo, saya butuh bantuan lagi.'
            ]);

        $response->assertStatus(403);
        $response->assertJsonFragment([
            'success' => false,
            'message' => 'Obrolan ini telah ditutup secara permanen oleh Admin.'
        ]);
    }

    /**
     * Test 2: Guest IDOR check (attempting to read messages of other thread by supplying token)
     */
    public function test_guest_chat_idor_protection_cross_read(): void
    {
        // Add a message in the closed thread
        GuestChatMessage::create([
            'guest_chat_thread_id' => $this->closedThread->id,
            'sender_type' => 'guest',
            'message' => 'Rahasia Tamu Lain'
        ]);

        // Accessing using active thread header, but trying to query/bypass with closed thread parameter
        $response = $this->withHeader('X-Guest-Chat-Token', 'token-active-123')
            ->getJson(route('guest-chat.messages', ['session_token' => 'token-closed-456']));

        // It should load the messages of the active thread (empty) rather than the closed thread's messages
        $response->assertStatus(200);
        $response->assertJsonMissing([
            'message' => 'Rahasia Tamu Lain'
        ]);
    }

    /**
     * Test 3: Log clean commands execution (log-notifikasi:clear and chat-guest:prune)
     */
    public function test_log_and_chat_guest_pruning_commands(): void
    {
        // Create dummy Kamar & Penyewa to satisfy foreign keys
        $kamar = Kamar::create([
            'nomor_kamar' => 'B101',
            'lantai' => 1,
            'tipe' => 'standard',
            'harga_bulan' => 1000000,
            'status' => 'tersedia',
            'luas_m2' => 12,
        ]);

        $penyewaUser = User::create([
            'nama' => 'Penyewa Test',
            'email' => 'penyewatest@gmail.com',
            'password' => bcrypt('Password123'),
            'no_hp' => '628987654311',
            'nik' => '1234567890123111',
            'nama_wali' => 'Wali Test',
            'no_wali' => '628987654312',
            'role' => 'penyewa',
            'is_active' => 1
        ]);

        $penyewa = Penyewa::create([
            'user_id' => $penyewaUser->id,
            'kamar_id' => $kamar->id,
            'nik' => '1234567890123111',
            'nama_wali' => 'Wali Test',
            'no_wali' => '628987654312',
            'tanggal_masuk' => now()->toDateString(),
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
            'harga_sewa' => 1000000,
            'deposit' => 1000000,
            'status' => 'aktif',
        ]);

        // 1. Create a log older than 90 days using raw query to bypass Eloquent timestamp overwrite
        $oldLogId = DB::table('log_notifikasi')->insertGetId([
            'penyewa_id' => $penyewa->id,
            'channel' => 'whatsapp',
            'event' => 'tagihan_baru',
            'status' => 'sukses',
            'pesan' => 'Pesan Lama Sekali',
            'created_at' => now()->subDays(95),
        ]);

        // 2. Create a log newer than 90 days
        $newLogId = DB::table('log_notifikasi')->insertGetId([
            'penyewa_id' => $penyewa->id,
            'channel' => 'whatsapp',
            'event' => 'tagihan_baru',
            'status' => 'sukses',
            'pesan' => 'Pesan Baru',
            'created_at' => now()->subDays(10),
        ]);

        // 3. Create a closed guest chat thread older than 90 days using raw query
        $oldThreadId = DB::table('guest_chat_threads')->insertGetId([
            'session_token' => hash('sha256', 'old-token-789'),
            'name' => 'Guest Lama',
            'no_hp' => '6281233330000',
            'status' => 'closed',
            'created_at' => now()->subDays(95),
            'updated_at' => now()->subDays(95),
        ]);

        DB::table('guest_chat_messages')->insert([
            'guest_chat_thread_id' => $oldThreadId,
            'sender_type' => 'guest',
            'message' => 'Pesan Obrolan Lama',
            'created_at' => now()->subDays(95),
            'updated_at' => now()->subDays(95),
        ]);

        // Run the pruning commands
        $exitCodeClear = Artisan::call('log-notifikasi:clear');
        $exitCodePrune = Artisan::call('chat-guest:prune');

        $this->assertEquals(0, $exitCodeClear);
        $this->assertEquals(0, $exitCodePrune);

        // Verify the old entries are deleted and new ones remain
        $this->assertDatabaseMissing('log_notifikasi', ['id' => $oldLogId]);
        $this->assertDatabaseHas('log_notifikasi', ['id' => $newLogId]);

        $this->assertDatabaseMissing('guest_chat_threads', ['id' => $oldThreadId]);
        $this->assertDatabaseMissing('guest_chat_messages', ['message' => 'Pesan Obrolan Lama']);
    }

    /**
     * Test 4: Admin phone number normalization on profile settings update
     */
    public function test_admin_phone_number_normalization_on_profile_update(): void
    {
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.profile.update'), [
                'nama' => 'Admin Utama Baru',
                'email' => 'admin.kost@gmail.com',
                'no_hp' => '0812-3456-7890', // dirty format
            ]);

        $response->assertRedirect(route('admin.profile.edit'));
        $response->assertSessionHas('success', 'Profil admin berhasil diperbarui!');

        // Check if DB stored normalized value
        $this->admin->refresh();
        $this->assertEquals('6281234567890', $this->admin->no_hp);
        $this->assertEquals('Admin Utama Baru', $this->admin->nama);
    }
}
