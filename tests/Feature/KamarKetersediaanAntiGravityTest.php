<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Fasilitas;
use App\Models\User;
use App\Models\Reservasi;
use App\Models\Penyewa;
use Database\Seeders\FasilitasSeeder;
use Database\Seeders\KamarSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class KamarKetersediaanAntiGravityTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $tenant;
    private string $serverKey = 'dummy_server_key';

    protected function setUp(): void
    {
        parent::setUp();

        // CONFIGURATION: QUEUE_CONNECTION=sync
        Config::set('queue.default', 'sync');
        Config::set('midtrans.server_key', $this->serverKey);
        Config::set('reservasi.admin_wa', '62895330031313');

        // Prevent network outbound calls with Mocking HTTP
        Http::fake([
            'api.fonnte.com/*' => Http::response([
                'status' => true,
                'message' => 'Pesan berhasil diisolasikan oleh Anti-Gravity Guard (HP Fisik Aman)',
            ], 200),
        ]);

        // Seed data master
        unset($this->seeder);
        $this->seed(FasilitasSeeder::class);
        $this->seed(KamarSeeder::class);

        // Inisialisasi User Admin
        $this->admin = User::create([
            'nama' => 'Admin Kost',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '62895330031313', // ADMIN WA NUMBER
            'role' => 'admin',
            'is_active' => 1,
        ]);

        // Inisialisasi User Penyewa
        $this->tenant = User::create([
            'nama' => 'Penyewa Digital',
            'email' => 'tenant.digital@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '6282219575575', // TARGET TEST NO
            'role' => 'penyewa',
            'nik' => '1234567890123456',
            'nama_wali' => 'Wali Digital',
            'no_wali' => '628111111111',
            'require_password_change' => 0,
            'is_active' => 1,
        ]);
    }

    /**
     * Helper to generate midtrans signature key.
     */
    private function generateSignature(string $orderId, string $statusCode, string $grossAmount): string
    {
        return hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);
    }

    /**
     * Skenario 1: Validasi State Awal Kamar Kosong (Initial Seeding State)
     */
    public function test_initial_seeding_state_is_available_with_correct_facility_counts(): void
    {
        // Assert: seluruh 32 kamar memiliki default status = 'tersedia'
        $this->assertDatabaseCount('kamar', 32);
        
        $kamarTersediaCount = Kamar::where('status', 'tersedia')->count();
        $this->assertEquals(32, $kamarTersediaCount);

        // TODO: Validasi linear pivot relasi fasilitas kamar VIP (7), DELUXE (6), dan STANDAR (6)
        $vipRoom = Kamar::where('tipe', 'vip')->first();
        $this->assertNotNull($vipRoom);
        $this->assertEquals(7, $vipRoom->fasilitas()->count());

        $deluxeRoom = Kamar::where('tipe', 'deluxe')->first();
        $this->assertNotNull($deluxeRoom);
        $this->assertEquals(6, $deluxeRoom->fasilitas()->count());

        $standarRoom = Kamar::where('tipe', 'standar')->first();
        $this->assertNotNull($standarRoom);
        $this->assertEquals(6, $standarRoom->fasilitas()->count());
    }

    /**
     * Skenario 2: Kamar Tetap 'Tersedia' Selama Proses Booking & Pembayaran (Non-Locking State)
     */
    public function test_room_remains_available_during_booking_and_payment_phase(): void
    {
        $kamar = Kamar::where('tipe', 'standar')->first();
        $this->assertNotNull($kamar);

        // 1. Simulasikan booking online
        $payload = [
            'kamar_id' => $kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'durasi' => 1,
            'is_dp' => 0,
        ];

        $response = $this->actingAs($this->tenant)
            ->post(route('penyewa.reservasi.store'), $payload);

        $response->assertStatus(302);
        $reservasi = Reservasi::where('user_id', $this->tenant->id)->first();
        $this->assertNotNull($reservasi);

        // Assert: status kamar tetap tersedia
        $this->assertEquals('tersedia', $kamar->fresh()->status);

        // 2. Simulasikan pembayaran lunas via Midtrans Callback
        $signature = $this->generateSignature($reservasi->order_id, '200', (string)$reservasi->total_harga);

        $callbackResponse = $this->postJson(route('api.midtrans.callback-reservasi'), [
            'order_id' => $reservasi->order_id,
            'status_code' => '200',
            'gross_amount' => (string)$reservasi->total_harga,
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
            'transaction_id' => 'tx-test-locking'
        ]);

        $callbackResponse->assertStatus(200);
        $reservasi->refresh();
        $this->assertEquals('lunas', $reservasi->status);

        // Assert: Kamar tetap tersedia setelah lunas (non-locking state sebelum admin approval)
        $this->assertEquals('tersedia', $kamar->fresh()->status);

        // Landing page check
        $landingResponse = $this->get(route('landing.index'));
        $landingResponse->assertStatus(200);
        $landingResponse->assertSee('Kamar ' . $kamar->nomor_kamar);
        $landingResponse->assertSee('✓ Tersedia');
    }

    /**
     * Skenario 3: Penguncian Otomatis Menjadi 'Terisi' via Admin Approval (Locking Transition)
     */
    public function test_room_automatically_locks_to_occupied_on_admin_approval(): void
    {
        $kamar = Kamar::where('tipe', 'vip')->first();
        $this->assertNotNull($kamar);

        // Buat reservasi lunas
        $reservasi = Reservasi::create([
            'user_id' => $this->tenant->id,
            'kamar_id' => $kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => $kamar->harga_bulan,
            'is_dp' => false,
            'status' => 'lunas',
            'order_id' => 'RSV-CONF-001'
        ]);

        // Simulasikan Admin melakukan konfirmasi reservasi
        $response = $this->actingAs($this->admin)
            ->post(route('admin.reservasi.konfirmasi', $reservasi->id), [
                'nik' => '1234567890123456',
                'nama_wali' => 'Wali Digital',
                'no_wali' => '081234567890',
                'catatan_admin' => 'Approved',
            ]);

        $response->assertRedirect(route('admin.reservasi.index'));

        // Assert: Kamar berubah status menjadi 'terisi' secara instan
        $this->assertEquals('terisi', $kamar->fresh()->status);
        $this->assertDatabaseHas('penyewa', [
            'user_id' => $this->tenant->id,
            'kamar_id' => $kamar->id,
            'status' => 'aktif',
            'nik' => '1234567890123456'
        ]);
    }

    /**
     * Skenario 4: Penegakan "Aturan Sakral" Pasca-Checkout (The Manual Unlock Policy)
     */
    public function test_manual_unlock_policy_enforcement_post_checkout(): void
    {
        $kamar = Kamar::where('tipe', 'deluxe')->first();
        $this->assertNotNull($kamar);

        // Daftarkan penyewa aktif langsung
        $penyewa = Penyewa::create([
            'user_id' => $this->tenant->id,
            'kamar_id' => $kamar->id,
            'nik' => '1234567890123457',
            'tanggal_masuk' => date('Y-m-d'),
            'nama_wali' => 'Wali Deluxe',
            'no_wali' => '08122334455',
            'deposit' => $kamar->harga_bulan,
            'status' => 'aktif',
            'tanggal_billing' => 1,
        ]);

        // Posisikan status kamar terisi
        $kamar->update(['status' => 'terisi']);

        // Aksi Uji A (Proses Checkout)
        $responseCheckout = $this->actingAs($this->admin)
            ->post(route('admin.penyewa.checkout.process', $penyewa->id), [
                'apakah_ada_kerusakan' => 0,
            ]);

        $responseCheckout->assertRedirect(route('admin.penyewa.index'));
        $this->assertEquals('nonaktif', $penyewa->fresh()->status);

        // Assert Kritis: Kamar WAJIB TETAP 'terisi'
        $this->assertEquals('terisi', $kamar->fresh()->status);

        // Aksi Uji B (Pelepasan Status Manual)
        $responseUnlock = $this->actingAs($this->admin)
            ->patch(route('admin.kamar.updateStatus', $kamar->id), [
                'status' => 'preview', // Wait, the validation only allows 'tersedia,terisi,maintenance', let's use 'tersedia'
                'status' => 'tersedia',
            ]);

        $responseUnlock->assertRedirect(route('admin.kamar.index'));

        // Assert Akhir: Status berubah menjadi 'tersedia' setelah pelepasan manual
        $this->assertEquals('tersedia', $kamar->fresh()->status);
    }
}
