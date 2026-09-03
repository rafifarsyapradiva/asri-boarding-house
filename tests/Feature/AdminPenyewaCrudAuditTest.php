<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use App\Models\Penyewa;
use App\Models\Tagihan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPenyewaCrudAuditTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Kamar $kamar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'nama' => 'Admin Kost',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567891',
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
    }

    /**
     * Test creating a new tenant with a custom deposit amount.
     */
    public function test_admin_can_register_new_tenant_with_custom_deposit(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.penyewa.store'), [
                'nama' => 'Tenant Deposit Custom',
                'email' => 'tenantcustom@example.com',
                'no_hp' => '081234567800',
                'nik' => '1234567890123456',
                'kamar_id' => $this->kamar->id,
                'tanggal_masuk' => date('Y-m-d'),
                'nama_wali' => 'Wali Tenant',
                'no_wali' => '081234567899',
                'tipe_sewa' => 'bulanan',
                'durasi' => 2,
                'deposit' => 500000, // Custom deposit
            ]);

        $response->assertRedirect(route('admin.penyewa.index'));
        
        $penyewa = Penyewa::where('nik', '1234567890123456')->first();
        $this->assertNotNull($penyewa);
        $this->assertEquals(500000, (int)$penyewa->deposit);
    }

    /**
     * Test that manual tenant registration automatically creates paid tagihan and pembayaran records.
     */
    public function test_admin_registration_creates_lunas_tagihan_and_pembayaran(): void
    {
        // Fake events if needed, but here we want to ensure everything is actually inserted into the database.
        $response = $this->actingAs($this->admin)
            ->post(route('admin.penyewa.store'), [
                'nama' => 'Tenant Billing Test',
                'email' => 'tenantbilling@example.com',
                'no_hp' => '081234567809',
                'nik' => '1234567890123409',
                'kamar_id' => $this->kamar->id,
                'tanggal_masuk' => date('Y-m-d'),
                'nama_wali' => 'Wali Tenant Billing',
                'no_wali' => '081234567899',
                'tipe_sewa' => 'bulanan',
                'durasi' => 1,
                'harga_sewa' => 1200000, // Custom rent price
                'deposit' => 300000,      // Custom deposit amount
            ]);

        $response->assertRedirect(route('admin.penyewa.index'));
        
        $penyewa = Penyewa::where('nik', '1234567890123409')->first();
        $this->assertNotNull($penyewa);

        // Verify Tagihan created with status 'lunas' and total nominal = 1,200,000 + 300,000 = 1,500,000
        $this->assertDatabaseHas('tagihan', [
            'penyewa_id' => $penyewa->id,
            'nominal_pokok' => 1200000,
            'nominal_total' => 1500000,
            'status' => 'lunas',
            'metode_pembayaran' => 'cash',
        ]);

        $tagihan = Tagihan::where('penyewa_id', $penyewa->id)->first();
        $this->assertNotNull($tagihan);

        // Verify Pembayaran created pointing to the tagihan
        $this->assertDatabaseHas('pembayaran', [
            'tagihan_id' => $tagihan->id,
            'nominal' => 1500000,
            'payment_type' => 'cash',
            'status_midtrans' => 'cash_confirmed',
            'dikonfirmasi_oleh' => $this->admin->id,
        ]);
    }

    /**
     * Test updating a tenant's deposit and details.
     */
    public function test_admin_can_update_tenant_deposit(): void
    {
        // 1. Create a tenant first
        $tenantUser = User::create([
            'nama' => 'Tenant Lawas',
            'email' => 'tenantlawas@example.com',
            'no_hp' => '081234567822',
            'password' => bcrypt('password'),
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $penyewa = Penyewa::create([
            'user_id' => $tenantUser->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1122334455667788',
            'tanggal_masuk' => date('Y-m-d'),
            'nama_wali' => 'Wali Lawas',
            'no_wali' => '081234567899',
            'deposit' => 1000000,
            'status' => 'aktif',
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        // 2. Perform update
        $response = $this->actingAs($this->admin)
            ->put(route('admin.penyewa.update', $penyewa->id), [
                'nama' => 'Tenant Lawas Updated',
                'email' => 'tenantlawas@example.com',
                'no_hp' => '081234567822',
                'nik' => '1122334455667788',
                'kamar_id' => $this->kamar->id,
                'tanggal_masuk' => date('Y-m-d'),
                'nama_wali' => 'Wali Lawas',
                'no_wali' => '081234567899',
                'tipe_sewa' => 'bulanan',
                'durasi' => 1,
                'status' => 'aktif',
                'deposit' => 800000, // Update deposit
                'harga_sewa' => 1000000,
            ]);

        $response->assertRedirect(route('admin.penyewa.index'));
        
        $penyewa->refresh();
        $this->assertEquals(800000, (int)$penyewa->deposit);
        $this->assertEquals('Tenant Lawas Updated', $penyewa->user->nama);
    }

    /**
     * Test room validation when reactivating a tenant.
     */
    public function test_reactivating_tenant_validates_room_availability(): void
    {
        // 1. Create a non-active tenant (checked out)
        $user1 = User::create([
            'nama' => 'Penyewa Nonaktif',
            'email' => 'nonaktif@example.com',
            'no_hp' => '081234567811',
            'password' => bcrypt('password'),
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $penyewaNonaktif = Penyewa::create([
            'user_id' => $user1->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1111222233334444',
            'tanggal_masuk' => date('Y-m-d'),
            'nama_wali' => 'Wali 1',
            'no_wali' => '081234567899',
            'deposit' => 1000000,
            'status' => 'nonaktif', // Non-active
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        // 2. Ensure room status is NOT tersedia (e.g. set to maintenance)
        $this->kamar->update(['status' => 'maintenance']);

        // 3. Reactivate -> Should fail because room is in maintenance
        $response = $this->actingAs($this->admin)
            ->put(route('admin.penyewa.update', $penyewaNonaktif->id), [
                'nama' => 'Penyewa Nonaktif',
                'email' => 'nonaktif@example.com',
                'no_hp' => '081234567811',
                'nik' => '1111222233334444',
                'kamar_id' => $this->kamar->id,
                'tanggal_masuk' => date('Y-m-d'),
                'nama_wali' => 'Wali 1',
                'no_wali' => '081234567899',
                'tipe_sewa' => 'bulanan',
                'durasi' => 1,
                'status' => 'aktif', // Trying to set back to active
                'deposit' => 1000000,
                'harga_sewa' => 1000000,
            ]);

        $response->assertSessionHasErrors(['kamar_id']);

        // 4. Reset room to 'terisi' and create ANOTHER active tenant in it
        $this->kamar->update(['status' => 'terisi']);
        $user2 = User::create([
            'nama' => 'Penyewa Aktif Lain',
            'email' => 'aktiflain@example.com',
            'no_hp' => '081234567833',
            'password' => bcrypt('password'),
            'role' => 'penyewa',
            'is_active' => 1,
        ]);
        Penyewa::create([
            'user_id' => $user2->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '5555666677778888',
            'tanggal_masuk' => date('Y-m-d'),
            'nama_wali' => 'Wali 2',
            'no_wali' => '081234567899',
            'deposit' => 1000000,
            'status' => 'aktif', // Active
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        // 5. Try to reactivate first tenant on same room -> Should fail because room is occupied by another active tenant
        $response = $this->actingAs($this->admin)
            ->put(route('admin.penyewa.update', $penyewaNonaktif->id), [
                'nama' => 'Penyewa Nonaktif',
                'email' => 'nonaktif@example.com',
                'no_hp' => '081234567811',
                'nik' => '1111222233334444',
                'kamar_id' => $this->kamar->id,
                'tanggal_masuk' => date('Y-m-d'),
                'nama_wali' => 'Wali 1',
                'no_wali' => '081234567899',
                'tipe_sewa' => 'bulanan',
                'durasi' => 1,
                'status' => 'aktif', // Trying to set back to active
                'deposit' => 1000000,
                'harga_sewa' => 1000000,
            ]);

        $response->assertSessionHasErrors(['status']);
    }

    /**
     * Test safe deletion constraints.
     */
    public function test_tenant_deletion_restrictions_and_cascade(): void
    {
        // 1. Create a tenant without bills
        $user1 = User::create([
            'nama' => 'Penyewa Bersih',
            'email' => 'bersih@example.com',
            'no_hp' => '081234567855',
            'password' => bcrypt('password'),
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $penyewaClean = Penyewa::create([
            'user_id' => $user1->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '9999888877776666',
            'tanggal_masuk' => date('Y-m-d'),
            'nama_wali' => 'Wali Clean',
            'no_wali' => '081234567899',
            'deposit' => 1000000,
            'status' => 'aktif',
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        // Create a log_notifikasi record pointing to this clean tenant
        \App\Models\LogNotifikasi::create([
            'penyewa_id' => $penyewaClean->id,
            'channel' => 'whatsapp',
            'event' => 'welcome_penyewa',
            'status' => 'gagal',
            'pesan' => 'Pesan welcome',
        ]);

        // 2. Create another tenant with bills
        $user2 = User::create([
            'nama' => 'Penyewa Berhutang',
            'email' => 'berhutang@example.com',
            'no_hp' => '081234567866',
            'password' => bcrypt('password'),
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $penyewaDirty = Penyewa::create([
            'user_id' => $user2->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '9999888877775555',
            'tanggal_masuk' => date('Y-m-d'),
            'nama_wali' => 'Wali Dirty',
            'no_wali' => '081234567899',
            'deposit' => 1000000,
            'status' => 'aktif',
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        $tagihan = Tagihan::create([
            'penyewa_id' => $penyewaDirty->id,
            'order_id' => 'TGH-TEST-DELETE',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => date('Y-m-d'),
            'tanggal_jatuh_tempo' => date('Y-m-d'),
            'nominal_pokok' => 1000000,
            'nominal_total' => 1000000,
            'status' => 'pending',
        ]);

        // 3. Try to delete Penyewa with bills -> Should fail
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.penyewa.destroy', $penyewaDirty->id));

        $response->assertSessionHas('error', 'Penyewa tidak dapat dihapus karena memiliki riwayat tagihan.');
        $this->assertDatabaseHas('penyewa', ['id' => $penyewaDirty->id]);
        $this->assertDatabaseHas('users', ['id' => $user2->id]);

        // 4. Delete Penyewa without bills -> Should succeed and delete user and cascade delete notifications
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.penyewa.destroy', $penyewaClean->id));

        $response->assertRedirect(route('admin.penyewa.index'));
        $response->assertSessionHas('success', 'Penyewa berhasil dihapus.');
        $this->assertSoftDeleted('penyewa', ['id' => $penyewaClean->id]);
        $this->assertSoftDeleted('users', ['id' => $user1->id]);
        $this->assertDatabaseMissing('log_notifikasi', ['penyewa_id' => $penyewaClean->id]);
    }

    /**
     * Test tenant harga_sewa lifecycle and billing behavior.
     */
    public function test_tenant_harga_sewa_lifecycle_and_billing(): void
    {
        // 1. Register a tenant without specifying harga_sewa (defaults to room price)
        $response = $this->actingAs($this->admin)
            ->post(route('admin.penyewa.store'), [
                'nama' => 'Tenant Default Price',
                'email' => 'defaultprice@example.com',
                'no_hp' => '081234567999',
                'nik' => '1234567890123999',
                'kamar_id' => $this->kamar->id,
                'tanggal_masuk' => date('Y-m-d'),
                'nama_wali' => 'Wali Tenant',
                'no_wali' => '081234567899',
                'tipe_sewa' => 'bulanan',
                'durasi' => 1,
            ]);

        $response->assertRedirect(route('admin.penyewa.index'));
        $penyewa = Penyewa::where('nik', '1234567890123999')->first();
        $this->assertNotNull($penyewa);
        // Should default to $this->kamar->harga_bulan (1000000)
        $this->assertEquals(1000000, (int)$penyewa->harga_sewa);

        // 2. Register a tenant with a custom harga_sewa
        // First make room available again (or create a new room)
        $kamar2 = Kamar::create([
            'nomor_kamar' => '102',
            'lantai' => 1,
            'tipe' => 'deluxe',
            'luas_m2' => 15,
            'harga_bulan' => 1500000,
            'status' => 'tersedia'
        ]);

        $response2 = $this->actingAs($this->admin)
            ->post(route('admin.penyewa.store'), [
                'nama' => 'Tenant Custom Price',
                'email' => 'customprice@example.com',
                'no_hp' => '081234567888',
                'nik' => '1234567890123888',
                'kamar_id' => $kamar2->id,
                'tanggal_masuk' => date('Y-m-d'),
                'nama_wali' => 'Wali Custom',
                'no_wali' => '081234567899',
                'tipe_sewa' => 'bulanan',
                'durasi' => 1,
                'harga_sewa' => 1200000, // Custom price
            ]);

        $response2->assertRedirect(route('admin.penyewa.index'));
        $penyewaCustom = Penyewa::where('nik', '1234567890123888')->first();
        $this->assertNotNull($penyewaCustom);
        $this->assertEquals(1200000, (int)$penyewaCustom->harga_sewa);

        // 3. Update the tenant's harga_sewa
        $response3 = $this->actingAs($this->admin)
            ->put(route('admin.penyewa.update', $penyewaCustom->id), [
                'nama' => 'Tenant Custom Price',
                'email' => 'customprice@example.com',
                'no_hp' => '081234567888',
                'nik' => '1234567890123888',
                'kamar_id' => $kamar2->id,
                'tanggal_masuk' => date('Y-m-d'),
                'nama_wali' => 'Wali Custom',
                'no_wali' => '081234567899',
                'tipe_sewa' => 'bulanan',
                'durasi' => 1,
                'status' => 'aktif',
                'deposit' => 1500000,
                'harga_sewa' => 1100000, // Updated price
            ]);

        $response3->assertRedirect(route('admin.penyewa.index'));
        $penyewaCustom->refresh();
        $this->assertEquals(1100000, (int)$penyewaCustom->harga_sewa);

        // 4. Change the room's price globally, and trigger BillingService.
        // The generated bill should use the tenant's individual harga_sewa, not the room's global price.
        $kamar2->update(['harga_bulan' => 2000000]); // Raised globally
        
        // Advance time to next month so that generateTagihanBulanan creates a new tagihan record
        \Illuminate\Support\Carbon::setTestNow(\Illuminate\Support\Carbon::now()->addMonth());

        $billingService = app(\App\Services\BillingService::class);
        $billingService->generateTagihanBulanan();

        // Reset Carbon test now
        \Illuminate\Support\Carbon::setTestNow();

        // Check the latest created Tagihan for this tenant
        $tagihan = Tagihan::where('penyewa_id', $penyewaCustom->id)->orderBy('id', 'desc')->first();
        $this->assertNotNull($tagihan);
        // It should match $penyewaCustom->harga_sewa (1100000), not the new room price (2000000)
        $this->assertEquals(1100000, (int)$tagihan->nominal_pokok);
    }

    /**
     * Test admin can export penyewa data to PDF.
     */
    public function test_admin_can_export_penyewa_pdf(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.penyewa.exportPdf', ['status' => 'aktif']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('laporan-penyewa-', $response->headers->get('Content-Disposition'));
    }

    /**
     * Test admin can export penyewa data to CSV.
     */
    public function test_admin_can_export_penyewa_csv(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.penyewa.exportCsv', ['status' => 'aktif']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('laporan-penyewa-', $response->headers->get('Content-Disposition'));
        
        $content = $response->streamedContent();
        $this->assertStringContainsString("\xEF\xBB\xBF", $content); // UTF-8 BOM
        $this->assertStringContainsString('LAPORAN DATA PENYEWA KOST', $content);
    }

    /**
     * Test admin can export penyewa data with combined filters (search and status).
     */
    public function test_admin_can_export_penyewa_with_combined_filters(): void
    {
        // Register a specific tenant to search for
        $kamar = Kamar::create([
            'nomor_kamar' => '888',
            'lantai' => 2,
            'tipe' => 'vip',
            'luas_m2' => 20.0,
            'harga_bulan' => 2500000,
            'status' => 'terisi'
        ]);

        $user = User::create([
            'nama' => 'Penyewa Unik Ekspor',
            'email' => 'ekspor.unik@example.com',
            'password' => bcrypt('password123'),
            'no_hp' => '628777777777',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'nik' => '9988776655443322',
            'tanggal_masuk' => date('Y-m-d'),
            'status' => 'aktif',
            'no_wali' => '081234567890',
            'nama_wali' => 'Wali Ekspor',
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        // PDF export with combined filter
        $responsePdf = $this->actingAs($this->admin)
            ->get(route('admin.penyewa.exportPdf', ['search' => 'Penyewa Unik Ekspor', 'status' => 'aktif']));
        $responsePdf->assertStatus(200);
        $responsePdf->assertHeader('Content-Type', 'application/pdf');

        // CSV export with combined filter
        $responseCsv = $this->actingAs($this->admin)
            ->get(route('admin.penyewa.exportCsv', ['search' => 'Penyewa Unik Ekspor', 'status' => 'aktif']));
        $responseCsv->assertStatus(200);
        
        $content = $responseCsv->streamedContent();
        $this->assertStringContainsString('Penyewa Unik Ekspor', $content);
        $this->assertStringContainsString('Kamar 888', $content);
    }
}
