<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPortalSecurityAuditTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

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
    }

    /**
     * Test 1: Verify Midtrans Webhook Signature Verification with floats/whole numbers
     */
    public function test_midtrans_webhook_signature_verification_handles_decimal_mismatch(): void
    {
        config(['midtrans.server_key' => 'dummy-midtrans-key-123']);

        // Midtrans computes signature with 2 decimal places (e.g. "150000.00")
        $expectedSignature = hash('sha512', 'order-abc-123' . '200' . '150000.00' . 'dummy-midtrans-key-123');

        // Test sending with whole number float (150000) - signature should still match!
        $response = $this->postJson(route('api.midtrans.callback'), [
            'order_id' => 'order-abc-123',
            'status_code' => '200',
            'gross_amount' => 150000, // Sends as integer
            'signature_key' => $expectedSignature
        ]);

        // It should NOT return 403 Forbidden (mismatch signature)
        $response->assertStatus(404); // 404 is acceptable since the order_id doesn't exist in DB
    }

    /**
     * Test 2: Validation of Promo Package duration limits
     */
    public function test_promo_package_validation_limits(): void
    {
        // 1. Bulanan: max 12 (13 should fail)
        $response = $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), [
                'logo_text' => 'Asri Kost',
                'logo_icon' => '🏡',
                'hero_tagline' => 'Hunian Nyaman',
                'hero_title' => 'Asri Boarding House',
                'hero_description' => 'Kost terbaik dekat kampus.',
                'contact_address' => 'Jl. Kampus Raya No. 10',
                'contact_whatsapp' => '081234567890',
                'contact_email' => 'admin@gmail.com',
                'google_maps_embed' => '<iframe src="https://www.google.com/maps/embed?pb=1"></iframe>',
                'bank_name' => 'BCA',
                'bank_account_number' => '1234567890',
                'bank_account_owner' => 'Asri Kost',
                'about_title' => 'Tentang Kami',
                'about_description' => 'Asri Boarding House adalah...',
                'about_visi' => 'Menjadi kost terpercaya',
                'about_misi_1' => 'Misi 1',
                'about_misi_2' => 'Misi 2',
                'about_misi_3' => 'Misi 3',
                'about_misi_4' => 'Misi 4',
                'promo_pkg1_name' => 'Promo Bulanan',
                'promo_pkg1_type' => 'bulanan',
                'promo_pkg1_duration' => 13, // Over limit 12!
                'promo_pkg1_discount' => 10,
                'promo_pkg1_desc' => 'Diskon sewa bulanan',
            ]);

        $response->assertSessionHasErrors('promo_pkg1_duration');

        // 2. Mingguan: max 8 (9 should fail)
        $response2 = $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), [
                'logo_text' => 'Asri Kost',
                'logo_icon' => '🏡',
                'hero_tagline' => 'Hunian Nyaman',
                'hero_title' => 'Asri Boarding House',
                'hero_description' => 'Kost terbaik dekat kampus.',
                'contact_address' => 'Jl. Kampus Raya No. 10',
                'contact_whatsapp' => '081234567890',
                'contact_email' => 'admin@gmail.com',
                'google_maps_embed' => '<iframe src="https://www.google.com/maps/embed?pb=1"></iframe>',
                'bank_name' => 'BCA',
                'bank_account_number' => '1234567890',
                'bank_account_owner' => 'Asri Kost',
                'about_title' => 'Tentang Kami',
                'about_description' => 'Asri Boarding House adalah...',
                'about_visi' => 'Menjadi kost terpercaya',
                'about_misi_1' => 'Misi 1',
                'about_misi_2' => 'Misi 2',
                'about_misi_3' => 'Misi 3',
                'about_misi_4' => 'Misi 4',
                'promo_pkg1_name' => 'Promo Mingguan',
                'promo_pkg1_type' => 'mingguan',
                'promo_pkg1_duration' => 9, // Over limit 8!
                'promo_pkg1_discount' => 5,
                'promo_pkg1_desc' => 'Diskon sewa mingguan',
            ]);

        $response2->assertSessionHasErrors('promo_pkg1_duration');
    }

    /**
     * Test 3: Soft-deleting tenant with 16-digit NIK releases constraints without truncation crash
     */
    public function test_tenant_soft_delete_with_long_nik_does_not_truncate(): void
    {
        // 1. Create a dummy Kamar
        $kamar = Kamar::create([
            'nomor_kamar' => 'A101',
            'lantai' => 1,
            'tipe' => 'standard',
            'harga_bulan' => 1000000,
            'status' => 'tersedia',
            'luas_m2' => 12,
        ]);

        // 2. Create Penyewa User
        $penyewaUser = User::create([
            'nama' => 'Penyewa Dummy',
            'email' => 'penyewa@gmail.com',
            'password' => bcrypt('PenyewaPassword123'),
            'no_hp' => '628987654321',
            'nik' => '1234567890123555', // 16 digit
            'nama_wali' => 'Wali Dummy',
            'no_wali' => '628987654322',
            'role' => 'penyewa',
            'is_active' => 1
        ]);

        // 3. Create Penyewa Record
        $penyewa = Penyewa::create([
            'user_id' => $penyewaUser->id,
            'kamar_id' => $kamar->id,
            'nik' => '1234567890123555',
            'nama_wali' => 'Wali Dummy',
            'no_wali' => '628987654322',
            'tanggal_masuk' => now()->toDateString(),
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
            'harga_sewa' => 1000000,
            'deposit' => 1000000,
            'status' => 'aktif',
        ]);

        // 4. Send DELETE request to soft delete the tenant
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.penyewa.destroy', $penyewa->id));

        $response->assertRedirect(route('admin.penyewa.index'));
        $response->assertSessionHas('success', 'Penyewa berhasil dihapus.');

        // 5. Verify databases: they must be soft deleted and contain the appended suffix without error
        $deletedPenyewa = Penyewa::onlyTrashed()->find($penyewa->id);
        $this->assertNotNull($deletedPenyewa);
        $this->assertStringContainsString('1234567890123555_deleted_', $deletedPenyewa->nik);

        $deletedUser = User::onlyTrashed()->find($penyewaUser->id);
        $this->assertNotNull($deletedUser);
        $this->assertStringContainsString('628987654321_deleted_', $deletedUser->no_hp);
        $this->assertStringContainsString('penyewa@gmail.com_deleted_', $deletedUser->email);
    }
}
