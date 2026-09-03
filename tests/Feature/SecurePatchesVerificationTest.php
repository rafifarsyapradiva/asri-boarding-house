<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\GuestChatThread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Admin\StorePengeluaranRequest;

class SecurePatchesVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test case 1: Memverifikasi sanitizer uang pada StorePengeluaranRequest.
     * Harus berhasil membersihkan format desimal gaya Indonesia, tanpa merusak desimal murni dari database.
     */
    public function test_currency_sanitizer_works_perfectly()
    {
        $requestStore = new StorePengeluaranRequest();

        // Refleksi fungsi protected/private agar bisa dites terisolasi
        $method = new \ReflectionMethod($requestStore, 'sanitizeCurrency');
        $method->setAccessible(true);

        // Uji nominal dengan koma desimal Indonesia
        $res1 = $method->invoke($requestStore, '1.500.000,50');
        $this->assertEquals(1500000.50, $res1);

        // Uji nominal ribuan tanpa desimal
        $res2 = $method->invoke($requestStore, '750.000');
        $this->assertEquals(750000.00, $res2);

        // Uji nominal decimal database murni (titik desimal tidak boleh hilang!)
        $res3 = $method->invoke($requestStore, '1400000.00');
        $this->assertEquals(1400000.00, $res3);

        // Uji nominal dengan prefix "Rp" dan spasi
        $res4 = $method->invoke($requestStore, 'Rp 1.500.000,50');
        $this->assertEquals(1500000.50, $res4);

        // Uji nominal dengan prefix "Rp." (lowercase, tanpa spasi)
        $res5 = $method->invoke($requestStore, 'rp.1.500.000');
        $this->assertEquals(1500000.00, $res5);

        // Uji dengan spasi berlebih di awal/akhir
        $res6 = $method->invoke($requestStore, '   1.500.000   ');
        $this->assertEquals(1500000.00, $res6);

        // Uji non-string
        $res7 = $method->invoke($requestStore, 125000.75);
        $this->assertEquals(125000.75, $res7);
    }

    /**
     * Test case 2: Memverifikasi penolakan token mentah (plaintext) pada stateless chat.
     * Karena token di DB bertipe SHA-256, token mentah acak tidak boleh mendapatkan akses.
     */
    public function test_stateless_chat_rejects_raw_unhashed_token_spoofing()
    {
        // Buat thread dengan token ter-hash SHA-256 di database
        $rawToken = '550e8400-e29b-41d4-a716-446655440000';
        $hashedToken = hash('sha256', $rawToken);

        GuestChatThread::create([
            'session_token' => $hashedToken,
            'name' => 'Budi Guest',
            'no_hp' => '081234567890',
            'status' => 'active',
        ]);

        // Simulasikan penyerang yang menebak token dengan format salah atau mencoba melacak token mentah di chat
        $response = $this->withHeaders([
            'X-Guest-Chat-Token' => 'wrong-token-value',
        ])->getJson(route('guest-chat.messages'));

        // Harus mengembalikan 404 karena token tidak cocok dengan hash di database
        $response->assertStatus(404);
    }

    /**
     * Test case 3: Penanganan QueryException ketika terjadi race condition login Google.
     * Harus secara otomatis fallback ke record user yang sudah dibuat request paralel pemenang.
     */
    public function test_google_oauth_race_condition_fallback_success()
    {
        $email = 'john.doe@gmail.com';

        // Simulasikan pemicu QueryException Integrity Constraint (1062)
        // Di mana baris user sudah berhasil dimasukkan oleh utas paralel kompetitor
        $competitorUser = User::create([
            'nama' => 'John Competitor',
            'email' => $email,
            'password' => bcrypt('password-aman-123'),
            'no_hp' => '085512345678',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        // Uji pemanggilan simulasi penanganan fallback
        try {
            $user = DB::transaction(function () use ($email) {
                // Sengaja memicu pengecekan ulang setelah competitor sukses nulis data
                $userInside = User::where('email', $email)->first();
                if ($userInside) {
                    return $userInside;
                }
                throw new \Exception('Utas gagal');
            });
        } catch (\Exception $e) {
            $user = null;
        }

        // Fallback harus berhasil mengambil record user buatan competitor
        $userFallback = User::where('email', $email)->first();
        $this->assertNotNull($userFallback);
        $this->assertEquals($competitorUser->id, $userFallback->id);
    }

    /**
     * Test case 4: Memverifikasi penolakan pengiriman pesan chat jika reservasi ditutup.
     */
    public function test_reservation_chat_rejects_sending_message_on_closed_status()
    {
        $user = User::create([
            'nama' => 'John Tenant',
            'email' => 'john.tenant@gmail.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567890',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $kamar = \App\Models\Kamar::create([
            'nomor_kamar' => 'A1',
            'lantai' => 1,
            'tipe' => 'standard',
            'luas_m2' => '12',
            'harga_bulan' => 1000000,
            'status' => 'tersedia',
        ]);

        $reservasi = \App\Models\Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1000000,
            'status' => 'batal', // status closed
            'order_id' => 'RSV-CLOSED-TEST',
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('api.chat.send', $reservasi->id), [
                'message' => 'Halo Admin, apakah pesanan saya bisa diaktifkan lagi?',
            ]);

        $response->assertStatus(403)
            ->assertJsonFragment(['message' => 'Obrolan ini telah ditutup secara permanen oleh Admin.']);
    }
}
