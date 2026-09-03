<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\NotifikasiKhusus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNotificationKhususTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $tenantUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin User
        $this->admin = User::create([
            'nama' => 'Admin Kost',
            'email' => 'admin.notif@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567891',
            'role' => 'admin',
            'is_active' => 1,
        ]);

        // Create Tenant User
        $this->tenantUser = User::create([
            'nama' => 'Penyewa Aktif',
            'email' => 'penyewa.notif@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567892',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);
    }

    public function test_admin_bisa_mengakses_halaman_notifikasi_khusus(): void
    {
        // Seed a dummy notification
        NotifikasiKhusus::log('reservasi', 'reservasi_baru', 'Reservasi baru dibuat oleh Test User', ['id' => 1], $this->tenantUser->id);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.notifikasi-khusus.index'));

        $response->assertStatus(200);
        $response->assertSee('Pusat Pemantauan &amp; Notifikasi Khusus', false);
        $response->assertSee('🚨 MONITOR AKTIVITAS SISTEM');
        $response->assertSee('Reservasi baru dibuat oleh Test User');
    }

    public function test_non_admin_tidak_bisa_mengakses_halaman_notifikasi_khusus(): void
    {
        $response = $this->actingAs($this->tenantUser)
            ->get(route('admin.notifikasi-khusus.index'));

        $response->assertStatus(403);
    }

    public function test_admin_bisa_memfilter_notifikasi_khusus_berdasarkan_sumber(): void
    {
        // Seed logs with different sources
        NotifikasiKhusus::create([
            'sumber' => 'reservasi',
            'tipe_aktivitas' => 'reservasi_baru',
            'deskripsi' => 'Reservasi Baru A',
            'user_id' => $this->tenantUser->id
        ]);

        NotifikasiKhusus::create([
            'sumber' => 'tagihan',
            'tipe_aktivitas' => 'tagihan_lunas',
            'deskripsi' => 'Tagihan Lunas B',
            'user_id' => $this->tenantUser->id
        ]);

        // Filter by source: reservasi
        $response = $this->actingAs($this->admin)
            ->get(route('admin.notifikasi-khusus.index', ['sumber' => 'reservasi']));

        $response->assertStatus(200);
        $response->assertSee('Reservasi Baru A');
        $response->assertDontSee('Tagihan Lunas B');

        // Filter by source: tagihan
        $response = $this->actingAs($this->admin)
            ->get(route('admin.notifikasi-khusus.index', ['sumber' => 'tagihan']));

        $response->assertStatus(200);
        $response->assertDontSee('Reservasi Baru A');
        $response->assertSee('Tagihan Lunas B');
    }

    public function test_admin_bisa_memfilter_notifikasi_khusus_berdasarkan_kata_kunci(): void
    {
        NotifikasiKhusus::create([
            'sumber' => 'admin',
            'tipe_aktivitas' => 'kamar_dibuat',
            'deskripsi' => 'Kamar 101 telah ditambahkan',
            'user_id' => $this->admin->id
        ]);

        NotifikasiKhusus::create([
            'sumber' => 'admin',
            'tipe_aktivitas' => 'kamar_dihapus',
            'deskripsi' => 'Kamar 999 telah dihapus',
            'user_id' => $this->admin->id
        ]);

        // Filter by keyword: ditambahkan
        $response = $this->actingAs($this->admin)
            ->get(route('admin.notifikasi-khusus.index', ['search' => 'ditambahkan']));

        $response->assertStatus(200);
        $response->assertSee('Kamar 101 telah ditambahkan');
        $response->assertDontSee('Kamar 999 telah dihapus');
    }

    public function test_admin_bisa_memfilter_notifikasi_khusus_berdasarkan_tanggal(): void
    {
        // Create logs on specific dates
        $log1 = new NotifikasiKhusus([
            'sumber' => 'admin',
            'tipe_aktivitas' => 'kamar_dibuat',
            'deskripsi' => 'Kamar 101 ditambahkan tanggal 10',
            'user_id' => $this->admin->id,
        ]);
        $log1->timestamps = false;
        $log1->created_at = '2026-06-10 10:00:00';
        $log1->save();

        $log2 = new NotifikasiKhusus([
            'sumber' => 'admin',
            'tipe_aktivitas' => 'kamar_dibuat',
            'deskripsi' => 'Kamar 102 ditambahkan tanggal 15',
            'user_id' => $this->admin->id,
        ]);
        $log2->timestamps = false;
        $log2->created_at = '2026-06-15 10:00:00';
        $log2->save();

        // Filter: 2026-06-12 to 2026-06-18
        $response = $this->actingAs($this->admin)
            ->get(route('admin.notifikasi-khusus.index', [
                'tanggal_mulai' => '2026-06-12',
                'tanggal_selesai' => '2026-06-18'
            ]));

        $response->assertStatus(200);
        $response->assertDontSee('Kamar 101 ditambahkan tanggal 10');
        $response->assertSee('Kamar 102 ditambahkan tanggal 15');
    }

    public function test_admin_bisa_menghapus_satu_log_notifikasi_khusus(): void
    {
        $log = NotifikasiKhusus::create([
            'sumber' => 'admin',
            'tipe_aktivitas' => 'kamar_dibuat',
            'deskripsi' => 'Kamar 101 ditambahkan',
            'user_id' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.notifikasi-khusus.destroy', $log->id));

        $response->assertRedirect(route('admin.notifikasi-khusus.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('notifikasi_khusus', [
            'id' => $log->id
        ]);
    }

    public function test_admin_bisa_membersihkan_semua_log_notifikasi_khusus(): void
    {
        NotifikasiKhusus::create([
            'sumber' => 'admin',
            'tipe_aktivitas' => 'kamar_dibuat',
            'deskripsi' => 'Kamar 101 ditambahkan',
            'user_id' => $this->admin->id
        ]);

        NotifikasiKhusus::create([
            'sumber' => 'admin',
            'tipe_aktivitas' => 'kamar_dihapus',
            'deskripsi' => 'Kamar 102 dihapus',
            'user_id' => $this->admin->id
        ]);

        $this->assertDatabaseCount('notifikasi_khusus', 2);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.notifikasi-khusus.clear'));

        $response->assertRedirect(route('admin.notifikasi-khusus.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseCount('notifikasi_khusus', 0);
    }

    public function test_notifikasi_khusus_model_accessors_dan_detail_payload(): void
    {
        $logWithUser = NotifikasiKhusus::create([
            'sumber' => 'reservasi',
            'tipe_aktivitas' => 'pembayaran_diterima',
            'deskripsi' => 'Pembayaran DP telah berhasil',
            'data_detail' => ['amount' => 500000],
            'user_id' => $this->tenantUser->id
        ]);

        $this->assertEquals('PEMBAYARAN DITERIMA', $logWithUser->formatted_tipe_aktivitas);
        $this->assertEquals('Penyewa Aktif (Penyewa)', $logWithUser->pemicu_text);
        $this->assertStringContainsString('bg-emerald-100', $logWithUser->sumber_badge_class);

        $payload = $logWithUser->toDetailPayload();
        $this->assertArrayHasKey('id', $payload);
        $this->assertEquals('RESERVASI', $payload['sumber']);
        $this->assertEquals('PEMBAYARAN DITERIMA', $payload['tipe']);
        $this->assertEquals('Penyewa Aktif (Penyewa)', $payload['user']);
        $this->assertEquals(['amount' => 500000], $payload['detail']);

        $logSystem = NotifikasiKhusus::create([
            'sumber' => 'admin',
            'tipe_aktivitas' => 'system_cron',
            'deskripsi' => 'Cron job auto reminder',
            'user_id' => null
        ]);

        $this->assertEquals('System / Webhook Callback', $logSystem->pemicu_text);
    }
}

