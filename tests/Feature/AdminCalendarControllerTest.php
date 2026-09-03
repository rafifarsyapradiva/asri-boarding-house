<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use App\Models\Reservasi;
use App\Models\Tagihan;
use App\Models\Penyewa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Carbon;

class AdminCalendarControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test admin can access the calendar index page.
     */
    public function test_admin_bisa_mengakses_halaman_kalender(): void
    {
        $admin = User::create([
            'nama' => 'Admin Utama',
            'email' => 'admin-cal@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '6281234567891',
            'role' => 'admin',
            'is_active' => 1,
            'require_password_change' => false,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.calendar.index'));

        $response->assertStatus(200);
        $response->assertSee('Kalender Kontrol Visual');
    }

    /**
     * Test tenant cannot access the admin calendar.
     */
    public function test_penyewa_tidak_bisa_mengakses_halaman_kalender_admin(): void
    {
        $penyewa = User::create([
            'nama' => 'Penyewa Biasa',
            'email' => 'penyewa-cal@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567891',
            'role' => 'penyewa',
            'is_active' => 1,
            'require_password_change' => false,
        ]);

        $response = $this->actingAs($penyewa)
            ->get(route('admin.calendar.index'));

        $response->assertStatus(403);
    }

    /**
     * Test calendar event API returns correct event mappings.
     */
    public function test_api_kalender_mengembalikan_data_yang_sesuai(): void
    {
        $admin = User::create([
            'nama' => 'Admin Utama',
            'email' => 'admin-cal@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '6281234567891',
            'role' => 'admin',
            'is_active' => 1,
            'require_password_change' => false,
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => '201',
            'lantai' => 2,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1200000,
            'status' => 'tersedia'
        ]);

        $today = Carbon::today();

        // 1. Create a confirmed check-in/out reservasi
        $reservasi = Reservasi::create([
            'user_id' => $admin->id, // dummy association
            'kamar_id' => $kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => $today->copy()->toDateString(),
            'tanggal_selesai' => $today->copy()->addMonths(1)->toDateString(),
            'durasi' => 1,
            'total_harga' => 1200000,
            'status' => 'dikonfirmasi',
            'order_id' => 'RSV-CONF-CAL'
        ]);

        // 2. Create a pending survey reservasi
        $survey = Reservasi::create([
            'user_id' => $admin->id,
            'kamar_id' => $kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => $today->copy()->addDays(5)->toDateString(),
            'tanggal_selesai' => $today->copy()->addDays(6)->toDateString(),
            'durasi' => 1,
            'total_harga' => 1200000,
            'status' => 'pending',
            'catatan_user' => 'Saya mau survei lokasi dulu hari sabtu',
            'order_id' => 'RSV-PEND-CAL'
        ]);

        // 3. Create active Penyewa & late Tagihan (M2: late 2 months)
        $tenantUser = User::create([
            'nama' => 'Penyewa Kalender',
            'email' => 'penyewa-kal@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567899',
            'role' => 'penyewa',
            'is_active' => 1,
            'require_password_change' => false,
        ]);

        $penyewa = Penyewa::create([
            'user_id' => $tenantUser->id,
            'kamar_id' => $kamar->id,
            'nik' => '1234567890123457',
            'tanggal_masuk' => $today->copy()->subMonths(2)->toDateString(),
            'status' => 'aktif',
            'tipe_sewa' => 'bulanan',
            'durasi' => 3,
            'deposit' => 200000,
            'no_wali' => '081234567888',
            'nama_wali' => 'Bapak Wali',
        ]);

        $tagihan = Tagihan::create([
            'penyewa_id' => $penyewa->id,
            'order_id' => 'TGH-LATE-M2',
            'periode_bulan' => $today->month,
            'periode_tahun' => $today->year,
            'tanggal_tagihan' => $today->copy()->subDays(20)->toDateString(),
            'tanggal_jatuh_tempo' => $today->copy()->toDateString(),
            'nominal_pokok' => 1200000,
            'nominal_denda' => 0,
            'nominal_total' => 1200000,
            'bulan_keterlambatan' => 2,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->getJson(route('admin.calendar.events', [
                'start_date' => $today->copy()->subDays(10)->toDateString(),
                'end_date' => $today->copy()->addDays(40)->toDateString()
            ]));

        $response->assertStatus(200);

        // Verify JSON returns exact structures and values for events and top-level deep links
        $response->assertJsonFragment([
            'type' => 'check-in',
            'status' => 'Dikonfirmasi',
            'url' => route('admin.reservasi.show', $reservasi->id),
        ]);

        $response->assertJsonFragment([
            'type' => 'survey',
            'status' => 'Pending / Rencana Survei',
            'url' => route('admin.reservasi.show', $survey->id),
        ]);

        $response->assertJsonFragment([
            'type' => 'tagihan',
            'status' => 'Terlambat 2 Bulan - Notifikasi Wali via Fonnte',
            'color' => 'bg-yellow-300 text-yellow-900 border-yellow-500',
            'url' => route('admin.tagihan.show', $tagihan->id),
        ]);
    }
}
