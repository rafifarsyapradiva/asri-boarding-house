<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\LogNotifikasi;
use App\Models\Penyewa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenyewaNotifikasiViewTest extends TestCase
{
    use RefreshDatabase;

    private function createPenyewaUser(string $name, string $email, string $phone, string $roomNumber): array
    {
        $user = User::create([
            'nama' => $name,
            'email' => $email,
            'password' => bcrypt('password'),
            'no_hp' => $phone,
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => $roomNumber,
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'terisi',
        ]);

        $penyewa = Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'nik' => '123456789' . rand(1000, 9999),
            'tanggal_masuk' => now()->toDateString(),
            'nama_wali' => 'Wali ' . $name,
            'no_wali' => '08123456' . rand(100, 999),
            'deposit' => 200000,
            'status' => 'aktif',
            'tanggal_billing' => 1,
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        return [$user, $penyewa];
    }

    public function test_penyewa_can_view_notification_history_index_page(): void
    {
        [$user, $penyewa] = $this->createPenyewaUser('Penyewa Notif A', 'notif.a@example.com', '628123456701', 'N101');

        LogNotifikasi::create([
            'penyewa_id' => $penyewa->id,
            'channel' => 'whatsapp',
            'event' => 'tagihan_dibuat',
            'status' => 'sukses',
            'pesan' => 'Tagihan sewa bulan ini telah terbit.',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('penyewa.notifikasi.index'));

        $response->assertOk();
        $response->assertSee('Riwayat Notifikasi Saya');
        $response->assertSee('tagihan dibuat');
        $response->assertSee('WhatsApp');
        $response->assertSee('Tagihan sewa bulan ini telah terbit.');
    }

    public function test_log_notifikasi_model_accessors_and_modal_payload(): void
    {
        $log = new LogNotifikasi([
            'channel' => 'email',
            'event' => 'reservasi_dikonfirmasi',
            'status' => 'sukses',
            'pesan' => 'Reservasi "Kamar A1" berhasil!',
            'created_at' => now(),
        ]);

        $this->assertEquals('reservasi dikonfirmasi', $log->formatted_event);
        $this->assertEquals('Reservasi "Kamar A1" berhasil!', $log->pesan_display);
        $this->assertNotEmpty($log->formatted_created_at);

        $payload = $log->toModalPayload();
        $this->assertIsArray($payload);
        $this->assertEquals('email', $payload['channel']);
        $this->assertEquals('reservasi dikonfirmasi', $payload['event']);
        $this->assertEquals('sukses', $payload['status']);
        $this->assertEquals('Reservasi "Kamar A1" berhasil!', $payload['pesan']);
    }

    public function test_empty_notification_logs_renders_empty_state_message(): void
    {
        [$user, $penyewa] = $this->createPenyewaUser('Penyewa Empty', 'empty@example.com', '628123456702', 'N102');

        $response = $this->actingAs($user)->get(route('penyewa.notifikasi.index'));

        $response->assertOk();
        $response->assertSee('Belum ada riwayat notifikasi yang dikirimkan untuk Anda.');
    }

    public function test_penyewa_cannot_see_other_tenants_notifications(): void
    {
        [$userA, $penyewaA] = $this->createPenyewaUser('Penyewa A', 'user.a@example.com', '628123456703', 'N103');
        [$userB, $penyewaB] = $this->createPenyewaUser('Penyewa B', 'user.b@example.com', '628123456704', 'N104');

        LogNotifikasi::create([
            'penyewa_id' => $penyewaB->id,
            'channel' => 'email',
            'event' => 'rahasia_penyewa_b',
            'status' => 'sukses',
            'pesan' => 'Pesan rahasia untuk Penyewa B',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($userA)->get(route('penyewa.notifikasi.index'));

        $response->assertOk();
        $response->assertDontSee('rahasia penyewa b');
        $response->assertDontSee('Pesan rahasia untuk Penyewa B');
    }
}
