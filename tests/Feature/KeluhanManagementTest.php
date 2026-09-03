<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use App\Models\Penyewa;
use App\Models\Keluhan;
use App\Events\KeluhanDibuat;
use App\Events\KeluhanDitanggapi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class KeluhanManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $tenantUser;
    private User $adminUser;
    private Penyewa $penyewa;
    private Kamar $kamar;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create room
        $this->kamar = Kamar::create([
            'nomor_kamar' => '102',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 800000,
            'status' => 'tersedia',
        ]);

        // 2. Create tenant user
        $this->tenantUser = User::create([
            'nama' => 'Rafif Arsya',
            'email' => 'rafif@example.com',
            'password' => bcrypt('password123'),
            'no_hp' => '6281234567890',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        // 3. Create penyewa record
        $this->penyewa = Penyewa::create([
            'user_id' => $this->tenantUser->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '3201234567890002',
            'tanggal_masuk' => '2026-06-01',
            'status' => 'aktif',
            'tanggal_billing' => 1,
            'deposit' => 300000,
            'nama_wali' => 'Bapak Rafif',
            'no_wali' => '6281298765431',
        ]);

        // 4. Create admin user
        $this->adminUser = User::create([
            'nama' => 'Admin Kost',
            'email' => 'admin@asri.com',
            'password' => bcrypt('password123'),
            'no_hp' => '62895330031313',
            'role' => 'admin',
            'is_active' => 1,
        ]);

        // Fake WhatsApp notifications
        Http::fake([
            'api.fonnte.com/*' => Http::response([
                'status' => true,
                'message' => 'Pesan terkirim (Mock)',
            ], 200),
        ]);

        Storage::fake('public');
    }

    /**
     * Test tenant can access complaint index and create form.
     */
    public function test_tenant_can_access_complaint_pages(): void
    {
        $response = $this->actingAs($this->tenantUser)
            ->get(route('penyewa.keluhan.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->tenantUser)
            ->get(route('penyewa.keluhan.create'));
        $response->assertStatus(200);
    }

    /**
     * Test tenant can submit a new complaint.
     */
    public function test_tenant_can_submit_complaint_successfully(): void
    {
        Event::fake([KeluhanDibuat::class]);

        $file = UploadedFile::fake()->create('damage.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($this->tenantUser)
            ->post(route('penyewa.keluhan.store'), [
                'judul' => 'Kran Wastafel Bocor',
                'kategori' => 'kamar',
                'deskripsi' => 'Kran wastafel di dalam kamar mandi bocor terus menerus.',
                'foto_bukti' => $file,
            ]);

        $response->assertRedirect(route('penyewa.keluhan.index'));
        $response->assertSessionHas('success', 'Keluhan Anda berhasil dikirim.');

        // Assert data exists in database
        $this->assertDatabaseHas('keluhan', [
            'penyewa_id' => $this->penyewa->id,
            'judul' => 'Kran Wastafel Bocor',
            'kategori' => 'kamar',
            'status' => 'pending',
        ]);

        $keluhan = Keluhan::first();
        $this->assertNotNull($keluhan->foto_bukti);
        Storage::disk('public')->assertExists($keluhan->foto_bukti);

        Event::assertDispatched(KeluhanDibuat::class, function ($event) use ($keluhan) {
            return $event->keluhan->id === $keluhan->id;
        });
    }

    /**
     * Test tenant cannot view another tenant's complaint.
     */
    public function test_tenant_cannot_view_others_complaint(): void
    {
        // Create another tenant
        $otherUser = User::create([
            'nama' => 'Tenant Lain',
            'email' => 'other@example.com',
            'password' => bcrypt('password123'),
            'no_hp' => '62899999999',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);
        $otherPenyewa = Penyewa::create([
            'user_id' => $otherUser->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '3201234567899999',
            'tanggal_masuk' => '2026-06-01',
            'status' => 'aktif',
            'tanggal_billing' => 1,
            'deposit' => 300000,
            'nama_wali' => 'Bapak Other',
            'no_wali' => '6281298765430',
        ]);

        $otherKeluhan = Keluhan::create([
            'penyewa_id' => $otherPenyewa->id,
            'judul' => 'Wifi Mati',
            'kategori' => 'fasilitas_bersama',
            'deskripsi' => 'Wifi lantai 2 mati.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->tenantUser)
            ->get(route('penyewa.keluhan.show', $otherKeluhan));

        $response->assertStatus(403);
    }

    /**
     * Test admin can view all complaints and filter by status.
     */
    public function test_admin_can_view_and_filter_complaints(): void
    {
        // Create complaints with different statuses
        Keluhan::create([
            'penyewa_id' => $this->penyewa->id,
            'judul' => 'Keluhan A',
            'kategori' => 'kamar',
            'deskripsi' => 'Detail A',
            'status' => 'pending',
        ]);

        Keluhan::create([
            'penyewa_id' => $this->penyewa->id,
            'judul' => 'Keluhan B',
            'kategori' => 'kamar',
            'deskripsi' => 'Detail B',
            'status' => 'selesai',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.keluhan.index'));
        $response->assertStatus(200);
        $response->assertSee('Keluhan A');
        $response->assertSee('Keluhan B');

        // Filter pending
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.keluhan.index', ['status' => 'pending']));
        $response->assertSee('Keluhan A');
        $response->assertDontSee('Keluhan B');
    }

    /**
     * Test admin can reply and update complaint status.
     */
    public function test_admin_can_respond_and_resolve_complaint(): void
    {
        Event::fake([KeluhanDitanggapi::class]);

        $keluhan = Keluhan::create([
            'penyewa_id' => $this->penyewa->id,
            'judul' => 'AC Kurang Dingin',
            'kategori' => 'kamar',
            'deskripsi' => 'AC di kamar 102 tidak mendinginkan.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.keluhan.update', $keluhan), [
                'status' => 'selesai',
                'tanggapan_admin' => 'AC sudah diservis dan diganti freonnya.',
            ]);

        $response->assertRedirect(route('admin.keluhan.show', $keluhan));
        $response->assertSessionHas('success', 'Tanggapan keluhan berhasil diperbarui.');

        $keluhan->refresh();
        $this->assertEquals('selesai', $keluhan->status);
        $this->assertEquals('AC sudah diservis dan diganti freonnya.', $keluhan->tanggapan_admin);
        $this->assertNotNull($keluhan->tanggal_selesai);

        Event::assertDispatched(KeluhanDitanggapi::class, function ($event) use ($keluhan) {
            return $event->keluhan->id === $keluhan->id;
        });
    }

    /**
     * Test WA notification flows are triggered on events.
     */
    public function test_wa_notifications_sent_via_listeners(): void
    {
        // Enable real event/listener flows for this test
        $keluhan = Keluhan::create([
            'penyewa_id' => $this->penyewa->id,
            'judul' => 'Lampu Mati',
            'kategori' => 'kamar',
            'deskripsi' => 'Lampu kamar mandi mati.',
            'status' => 'pending',
        ]);

        // 1. Trigger KeluhanDibuat event manually to test listener
        event(new KeluhanDibuat($keluhan));

        // Verify Fonnte API was hit for admin
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.fonnte.com/send') &&
                   $request['target'] === '62895330031313' && // admin_wa
                   str_contains($request['message'], 'LAPORAN KELUHAN BARU') &&
                   str_contains($request['message'], '102') &&
                   str_contains($request['message'], 'Lampu Mati');
        });

        // 2. Trigger KeluhanDitanggapi event manually to test listener
        $keluhan->update([
            'status' => 'diproses',
            'tanggapan_admin' => 'Akan dikerjakan besok pagi.',
        ]);
        event(new KeluhanDitanggapi($keluhan));

        // Verify Fonnte API was hit for tenant
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.fonnte.com/send') &&
                   $request['target'] === '6281234567890' && // tenant no_hp
                   str_contains($request['message'], 'UPDATE KELUHAN PENYEWA') &&
                   str_contains($request['message'], 'diproses') &&
                   str_contains($request['message'], 'Akan dikerjakan besok pagi.');
        });
    }

    /**
     * Test admin clears tanggal_selesai when reopening complaint.
     */
    public function test_admin_clears_tanggal_selesai_when_reopening_complaint(): void
    {
        $keluhan = Keluhan::create([
            'penyewa_id' => $this->penyewa->id,
            'judul' => 'AC Kurang Dingin',
            'kategori' => 'kamar',
            'deskripsi' => 'AC di kamar 102 tidak mendinginkan.',
            'status' => 'selesai',
            'tanggal_selesai' => now()->subDay(),
            'tanggapan_admin' => 'AC sudah diservis dan diganti freonnya.',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.keluhan.update', $keluhan), [
                'status' => 'diproses',
                'tanggapan_admin' => 'Reopened: Teknisi harus mengecek kembali karena masih ada kebocoran.',
            ]);

        $response->assertRedirect(route('admin.keluhan.show', $keluhan));
        $response->assertSessionHas('success', 'Tanggapan keluhan berhasil diperbarui.');

        $keluhan->refresh();
        $this->assertEquals('diproses', $keluhan->status);
        $this->assertNull($keluhan->tanggal_selesai);
    }

    /**
     * Test non-active tenant cannot submit or create complaint.
     */
    public function test_non_active_tenant_cannot_create_or_store_complaint(): void
    {
        // 1. Create a non-active tenant user (status: nonaktif)
        $nonActiveUser = User::create([
            'nama' => 'Penyewa Non-Aktif',
            'email' => 'nonactive@example.com',
            'password' => bcrypt('password123'),
            'no_hp' => '6281234567891',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);
        $nonActivePenyewa = Penyewa::create([
            'user_id' => $nonActiveUser->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '3201234567890005',
            'tanggal_masuk' => '2026-06-01',
            'status' => 'nonaktif',
            'tanggal_billing' => 1,
            'deposit' => 300000,
            'nama_wali' => 'Wali Non-Aktif',
            'no_wali' => '6281298765432',
        ]);

        // Accessing create form should redirect back with error
        $response = $this->actingAs($nonActiveUser)
            ->get(route('penyewa.keluhan.create'));
        $response->assertRedirect(route('penyewa.keluhan.index'));
        $response->assertSessionHas('error', 'Akses ditolak. Hanya penyewa aktif yang dapat membuat keluhan.');

        // Submitting store should redirect back with error
        $response = $this->actingAs($nonActiveUser)
            ->post(route('penyewa.keluhan.store'), [
                'judul' => 'Masalah Air',
                'kategori' => 'kamar',
                'deskripsi' => 'Air tidak mengalir sama sekali.',
            ]);
        $response->assertRedirect(route('penyewa.keluhan.index'));
        $response->assertSessionHas('error', 'Akses ditolak. Hanya penyewa aktif yang dapat membuat keluhan.');

        // Assert no complaint was created in the database
        $this->assertDatabaseMissing('keluhan', [
            'penyewa_id' => $nonActivePenyewa->id,
            'judul' => 'Masalah Air',
        ]);
    }
}
