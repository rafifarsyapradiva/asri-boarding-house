<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use App\Models\Penyewa;
use App\Models\Reservasi;
use App\Models\Tagihan;
use App\Models\ChatMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SoftDeletesAuditTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that soft-deleting a Room releases its room number,
     * allowing a new room to be created with the same number.
     */
    public function test_kamar_deletion_releases_nomor_kamar(): void
    {
        $admin = User::create([
            'nama' => 'Admin Kost',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567890',
            'role' => 'admin',
            'is_active' => 1,
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => 'A101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 15,
            'harga_bulan' => 1500000,
            'status' => 'tersedia',
        ]);

        // Delete room via controller
        $response = $this->actingAs($admin)
            ->delete(route('admin.kamar.destroy', $kamar));

        $response->assertRedirect(route('admin.kamar.index'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('kamar', ['id' => $kamar->id]);

        $deletedKamar = Kamar::withTrashed()->find($kamar->id);
        $this->assertStringContainsString('_deleted_', $deletedKamar->nomor_kamar);

        // Now we should be able to create a new Room with the same number 'A101'
        $newKamar = Kamar::create([
            'nomor_kamar' => 'A101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 15,
            'harga_bulan' => 1500000,
            'status' => 'tersedia',
        ]);

        $this->assertDatabaseHas('kamar', [
            'id' => $newKamar->id,
            'nomor_kamar' => 'A101',
        ]);
    }

    /**
     * Test that user self-deletion releases email and phone number.
     */
    public function test_user_self_deletion_releases_email_and_no_hp(): void
    {
        $user = User::create([
            'nama' => 'Penyewa Biasa',
            'email' => 'biasa@gmail.com',
            'password' => bcrypt('password'),
            'no_hp' => '081223344550',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        // Simulasikan penghapusan lewat ProfileController destroy
        $response = $this->actingAs($user)
            ->delete(route('profile.destroy'), [
                'password' => 'password',
            ]);

        $response->assertRedirect('/');

        $this->assertSoftDeleted('users', ['id' => $user->id]);

        $deletedUser = User::withTrashed()->find($user->id);
        $this->assertStringContainsString('_deleted_', $deletedUser->email);
        $this->assertStringContainsString('_deleted_', $deletedUser->no_hp);

        // Allow registering again with same email/no_hp
        $newUser = User::create([
            'nama' => 'Penyewa Baru',
            'email' => 'biasa@gmail.com',
            'password' => bcrypt('password'),
            'no_hp' => '081223344550',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $newUser->id,
            'email' => 'biasa@gmail.com',
        ]);
    }

    /**
     * Test that relationships with soft-deleted entities return the model using withTrashed.
     */
    public function test_relationships_include_soft_deleted_entities(): void
    {
        $kamar = Kamar::create([
            'nomor_kamar' => 'A105',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 15,
            'harga_bulan' => 1500000,
            'status' => 'tersedia',
        ]);

        $user = User::create([
            'nama' => 'Joko',
            'email' => 'joko@gmail.com',
            'password' => bcrypt('password'),
            'no_hp' => '081223344559',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $penyewa = Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'nik' => '1234567890123459',
            'tanggal_masuk' => now()->toDateString(),
            'status' => 'aktif',
            'nama_wali' => 'Wali Joko',
            'no_wali' => '081223344557',
        ]);

        $reservasi = Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonth()->toDateString(),
            'durasi' => 1,
            'total_harga' => 1500000,
            'status' => 'pending',
            'is_dp' => false,
            'order_id' => 'RSV-JOKO',
        ]);

        $tagihan = Tagihan::create([
            'penyewa_id' => $penyewa->id,
            'order_id' => 'INV-JOKO',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(7)->toDateString(),
            'nominal_pokok' => 1500000,
            'nominal_total' => 1500000,
            'status' => 'pending',
        ]);

        $chat = ChatMessage::create([
            'reservasi_id' => $reservasi->id,
            'sender_id' => $user->id,
            'message' => 'Halo',
        ]);

        // Soft delete user, room, tenant, and reservation
        $kamar->delete();
        $user->delete();
        $penyewa->delete();
        $reservasi->delete();

        // Verify that relationships on non-deleted records still resolve soft-deleted records via withTrashed()
        $freshTagihan = Tagihan::find($tagihan->id);
        $this->assertNotNull($freshTagihan->penyewa);
        $this->assertEquals($penyewa->id, $freshTagihan->penyewa->id);

        $freshChat = ChatMessage::find($chat->id);
        $this->assertNotNull($freshChat->reservasi);
        $this->assertEquals($reservasi->id, $freshChat->reservasi->id);
        $this->assertNotNull($freshChat->sender);
        $this->assertEquals($user->id, $freshChat->sender->id);

        // Verify relationships on soft-deleted records resolve using withTrashed
        $freshPenyewa = Penyewa::withTrashed()->find($penyewa->id);
        $this->assertNotNull($freshPenyewa->kamar);
        $this->assertEquals($kamar->id, $freshPenyewa->kamar->id);
        $this->assertNotNull($freshPenyewa->user);
        $this->assertEquals($user->id, $freshPenyewa->user->id);

        $freshReservasi = Reservasi::withTrashed()->find($reservasi->id);
        $this->assertNotNull($freshReservasi->kamar);
        $this->assertEquals($kamar->id, $freshReservasi->kamar->id);
        $this->assertNotNull($freshReservasi->user);
        $this->assertEquals($user->id, $freshReservasi->user->id);
    }

    /**
     * Test that soft deleting a User model automatically transitions their Penyewa record's status to nonaktif.
     */
    public function test_user_soft_delete_cascades_penyewa_status_to_nonaktif(): void
    {
        $kamar = Kamar::create([
            'nomor_kamar' => '106-AUDIT',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        $user = User::create([
            'nama' => 'Penyewa Cascade',
            'email' => 'cascade@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '089876543210',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $penyewa = Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'nik' => '9876543210987654',
            'tanggal_masuk' => date('Y-m-d'),
            'nama_wali' => 'Wali Cascade',
            'no_wali' => '089876543219',
            'deposit' => 1000000,
            'status' => 'aktif',
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        // Soft delete the user
        $user->delete();

        // Refresh and check status in database
        $penyewa->refresh();
        $this->assertEquals('nonaktif', $penyewa->status);
    }

    /**
     * Test that deleting a cancelled reservation (status = batal) performs a force delete
     * instead of a soft delete, and cascades to delete related chat messages.
     */
    public function test_cancelled_reservasi_deletion_performs_force_delete_and_cascades_chats(): void
    {
        $admin = User::create([
            'nama' => 'Admin Test',
            'email' => 'admin.cancel.test@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567990',
            'role' => 'admin',
            'is_active' => 1,
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => '107-AUDIT',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        $user = User::create([
            'nama' => 'Penyewa Batal',
            'email' => 'batal@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567991',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $reservasi = Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'order_id' => 'REV-BATAL-TEST',
            'durasi_bulan' => 1,
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'total_harga' => 1000000,
            'status' => 'batal', // Cancelled status
        ]);

        $chat = \App\Models\ChatMessage::create([
            'reservasi_id' => $reservasi->id,
            'sender_id' => $user->id,
            'message' => 'Test chat message',
        ]);

        // Send delete request
        $response = $this->actingAs($admin)
            ->delete(route('admin.reservasi.destroy', $reservasi->id));

        $response->assertRedirect(route('admin.reservasi.index'));

        // Assert it is completely missing (force deleted), not soft deleted
        $this->assertDatabaseMissing('reservasi', ['id' => $reservasi->id]);
        $this->assertDatabaseMissing('chat_messages', ['id' => $chat->id]);
    }
}
