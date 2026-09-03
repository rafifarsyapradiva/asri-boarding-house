<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use App\Models\Penyewa;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Reservasi;
use App\Events\ReminderPenyewa;
use App\Events\NotifikasiWali;
use App\Events\DendaDikenakan;
use App\Events\TagihanDibuat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class BusinessPolicyEnforcementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Kamar $kamar;

    protected function setUp(): void
    {
        parent::setUp();

        // Prevent external HTTP requests
        Http::fake([
            'api.fonnte.com/*' => Http::response([
                'status' => true,
                'message' => 'Pesan berhasil diisolasikan oleh Anti-Gravity Guard (HP Fisik Aman)',
            ], 200),
            'app.sandbox.midtrans.com/*' => Http::response(['token' => 'fake_midtrans_snap_token_123'], 200),
            'app.midtrans.com/*' => Http::response(['token' => 'fake_midtrans_snap_token_123'], 200),
        ]);

        Config::set('midtrans.server_key', 'test_midtrans_server_key');

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
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);
    }

    /**
     * Helper to create a user and a tenant.
     */
    private function createTenant(string $email = 'tenant@example.com', string $noHp = '081234567892', string $nik = '1234567890123456'): Penyewa
    {
        $user = User::create([
            'nama' => 'Penyewa Dummy',
            'email' => $email,
            'password' => bcrypt('password'),
            'no_hp' => $noHp,
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        return Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $this->kamar->id,
            'nik' => $nik,
            'tanggal_masuk' => Carbon::now()->toDateString(),
            'nama_wali' => 'Wali Dummy',
            'no_wali' => '081122334455',
            'deposit' => 200000,
            'status' => 'aktif',
            'tanggal_billing' => 1,
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);
    }

    /**
     * Kebijakan Baru: Mesin Denda Kalender & Idempotency Guard (Anti Double-Denda Harian)
     */
    public function test_graduated_late_engine_and_delay_tracking(): void
    {
        Event::fake([
            ReminderPenyewa::class,
            NotifikasiWali::class,
            DendaDikenakan::class
        ]);

        $penyewa = $this->createTenant();

        // Set baseline date: 1 Juni 2026
        Carbon::setTestNow(Carbon::parse('2026-06-01 00:00:00'));

        // Buat tagihan pending dengan bulan_keterlambatan = 0
        $tagihan = Tagihan::create([
            'penyewa_id' => $penyewa->id,
            'order_id' => 'TGH-TEST-LATE-1',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-06-01',
            'tanggal_jatuh_tempo' => '2026-06-10',
            'nominal_pokok' => 1000000,
            'nominal_total' => 1000000,
            'nominal_denda' => 0,
            'bulan_keterlambatan' => 0,
            'status' => 'pending'
        ]);

        // --- BULAN 1: MASA KERINGANAN (Bulan Berjalan Yang Sama) ---
        // Simulasikan melewati jatuh tempo tetapi masih di bulan Juni (11 Juni 2026)
        Carbon::setTestNow(Carbon::parse('2026-06-11 00:00:00'));

        // Jalankan Late Engine
        $this->artisan('tagihan:proses-keterlambatan');

        $tagihan->refresh();

        // Assert denda tetap 0, status berubah jadi 'terlambat', event ReminderPenyewa terpicu, bulan_keterlambatan = 1
        $this->assertEquals(0, (float)$tagihan->nominal_denda);
        $this->assertEquals('terlambat', $tagihan->status);
        $this->assertEquals(1, $tagihan->bulan_keterlambatan);
        Event::assertDispatched(ReminderPenyewa::class, function ($event) use ($tagihan) {
            return $event->tagihan->id === $tagihan->id;
        });

        // --- BULAN 2: PENEGAKAN DENDA DI BULAN BERIKUTNYA ---
        // Majukan waktu ke bulan berikutnya (1 Juli 2026)
        Carbon::setTestNow(Carbon::parse('2026-07-01 00:00:00'));

        // Jalankan Late Engine
        $this->artisan('tagihan:proses-keterlambatan');

        $tagihan->refresh();

        // Assert denda flat 5% langsung diberlakukan, nominal_total terupdate, status tetap 'terlambat', event DendaDikenakan terpicu
        $this->assertEquals(50000, (float)$tagihan->nominal_denda);
        $this->assertEquals(1050000, (float)$tagihan->nominal_total);
        $this->assertEquals('terlambat', $tagihan->status);
        $this->assertEquals(3, $tagihan->bulan_keterlambatan);
        Event::assertDispatched(DendaDikenakan::class, function ($event) use ($tagihan) {
            return $event->tagihan->id === $tagihan->id;
        });

        // --- HARI BERIKUTNYA (IDEMPOTENCY GUARD CHECK) ---
        // Majukan waktu 1 hari (2 Juli 2026)
        Carbon::setTestNow(Carbon::parse('2026-07-02 00:00:00'));

        // Reset fake events untuk melacak event baru
        Event::fake([
            ReminderPenyewa::class,
            DendaDikenakan::class
        ]);

        // Jalankan Late Engine kembali
        $this->artisan('tagihan:proses-keterlambatan');

        $tagihan->refresh();

        // Assert denda tidak bertambah (tetap 50,000 / 5%), nominal_total tidak bertambah, event ReminderPenyewa terpicu untuk notifikasi berkala
        $this->assertEquals(50000, (float)$tagihan->nominal_denda);
        $this->assertEquals(1050000, (float)$tagihan->nominal_total);
        Event::assertNotDispatched(DendaDikenakan::class);
        Event::assertDispatched(ReminderPenyewa::class, function ($event) use ($tagihan) {
            return $event->tagihan->id === $tagihan->id;
        });

        // Reset time
        Carbon::setTestNow();
    }

    /**
     * Kebijakan 2: Validasi Data Wali Wajib (Form Input Manual Admin)
     */
    public function test_mandatory_guardian_data_for_manual_tenant(): void
    {
        // 1. Missing nama_wali
        $payloadNoNamaWali = [
            'nama' => 'Penyewa Manual A',
            'email' => 'manual.a@example.com',
            'no_hp' => '081223344556',
            'nik' => '1234567890123451',
            'kamar_id' => $this->kamar->id,
            'tanggal_masuk' => date('Y-m-d'),
            'no_wali' => '081122334455',
            'deposit' => 200000,
            'tipe_sewa' => 'bulanan',
            'durasi' => 3,
        ];

        $response1 = $this->actingAs($this->admin)
            ->post(route('admin.penyewa.store'), $payloadNoNamaWali);

        $response1->assertStatus(302); // Redirect back due to validation error
        $response1->assertSessionHasErrors(['nama_wali']);
        $this->assertDatabaseMissing('users', ['email' => 'manual.a@example.com']);

        // 2. Missing no_wali
        $payloadNoNoWali = [
            'nama' => 'Penyewa Manual B',
            'email' => 'manual.b@example.com',
            'no_hp' => '081223344557',
            'nik' => '1234567890123452',
            'kamar_id' => $this->kamar->id,
            'tanggal_masuk' => date('Y-m-d'),
            'nama_wali' => 'Nama Wali B',
            'deposit' => 200000,
            'tipe_sewa' => 'bulanan',
            'durasi' => 3,
        ];

        $response2 = $this->actingAs($this->admin)
            ->post(route('admin.penyewa.store'), $payloadNoNoWali);

        $response2->assertStatus(302);
        $response2->assertSessionHasErrors(['no_wali']);
        $this->assertDatabaseMissing('users', ['email' => 'manual.b@example.com']);
        
        // 3. Successful manual tenant creation when fields are complete
        $payloadComplete = [
            'nama' => 'Penyewa Manual C',
            'email' => 'manual.c@example.com',
            'no_hp' => '081223344558',
            'nik' => '1234567890123453',
            'kamar_id' => $this->kamar->id,
            'tanggal_masuk' => date('Y-m-d'),
            'nama_wali' => 'Nama Wali C',
            'no_wali' => '081122334455',
            'deposit' => 200000,
            'tipe_sewa' => 'bulanan',
            'durasi' => 3,
        ];

        $response3 = $this->actingAs($this->admin)
            ->post(route('admin.penyewa.store'), $payloadComplete);

        $response3->assertRedirect(route('admin.penyewa.index'));
        $this->assertDatabaseHas('users', ['email' => 'manual.c@example.com']);
        $this->assertDatabaseHas('penyewa', ['nik' => '1234567890123453', 'nama_wali' => 'Nama Wali C']);
    }

    /**
     * Kebijakan 3 & 4: Siklus Tagihan Serentak Tanggal 1 & Jatuh Tempo Tanggal 10
     */
    public function test_monthly_billing_cycle_and_due_date(): void
    {
        Event::fake([TagihanDibuat::class]);

        // User A enters on June 5th
        Carbon::setTestNow(Carbon::parse('2026-06-05 08:00:00'));
        $userA = User::create([
            'nama' => 'User A',
            'email' => 'usera@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081299998888',
            'role' => 'penyewa',
        ]);
        $penyewaA = Penyewa::create([
            'user_id' => $userA->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1122334455667788',
            'tanggal_masuk' => '2026-06-05',
            'nama_wali' => 'Wali A',
            'no_wali' => '081122334455',
            'status' => 'aktif',
            'tanggal_billing' => 1,
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        // User B enters on June 25th
        Carbon::setTestNow(Carbon::parse('2026-06-25 08:00:00'));
        $userB = User::create([
            'nama' => 'User B',
            'email' => 'userb@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081299998889',
            'role' => 'penyewa',
        ]);
        $penyewaB = Penyewa::create([
            'user_id' => $userB->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1122334455667789',
            'tanggal_masuk' => '2026-06-25',
            'nama_wali' => 'Wali B',
            'no_wali' => '081122334456',
            'status' => 'aktif',
            'tanggal_billing' => 1,
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        // User C enters on June 28th (harian)
        Carbon::setTestNow(Carbon::parse('2026-06-28 08:00:00'));
        $userC = User::create([
            'nama' => 'User C',
            'email' => 'userc@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081299998890',
            'role' => 'penyewa',
        ]);
        $penyewaC = Penyewa::create([
            'user_id' => $userC->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1122334455667790',
            'tanggal_masuk' => '2026-06-28',
            'nama_wali' => 'Wali C',
            'no_wali' => '081122334457',
            'status' => 'aktif',
            'tanggal_billing' => 1,
            'tipe_sewa' => 'harian',
            'durasi' => 5,
        ]);

        // Set cron execution date to July 1st, at 00:05
        Carbon::setTestNow(Carbon::parse('2026-07-01 00:05:00'));

        // Run auto-billing cron
        $this->artisan('tagihan:generate-bulanan');

        // Assert both tenants received bills for July 2026
        $tagihanA = Tagihan::where('penyewa_id', $penyewaA->id)
            ->where('periode_bulan', 7)
            ->where('periode_tahun', 2026)
            ->first();

        $tagihanB = Tagihan::where('penyewa_id', $penyewaB->id)
            ->where('periode_bulan', 7)
            ->where('periode_tahun', 2026)
            ->first();

        $tagihanC = Tagihan::where('penyewa_id', $penyewaC->id)
            ->where('periode_bulan', 7)
            ->where('periode_tahun', 2026)
            ->first();

        $this->assertNotNull($tagihanA);
        $this->assertNotNull($tagihanB);
        $this->assertNull($tagihanC); // User C (harian) should not have an auto-generated monthly tagihan

        // Assert due date is locked to July 10, 2026 for both
        $this->assertEquals('2026-07-10', $tagihanA->tanggal_jatuh_tempo->toDateString());
        $this->assertEquals('2026-07-10', $tagihanB->tanggal_jatuh_tempo->toDateString());

        Event::assertDispatched(TagihanDibuat::class, 2);

        Carbon::setTestNow();
    }

    /**
     * Kebijakan 5: Opsi Pembayaran (Cash & Midtrans)
     */
    public function test_payment_options_cash_and_midtrans(): void
    {
        $penyewa = $this->createTenant();

        $tagihan = Tagihan::create([
            'penyewa_id' => $penyewa->id,
            'order_id' => 'TGH-PAY-OPTION-1',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-06-01',
            'tanggal_jatuh_tempo' => '2026-06-10',
            'nominal_pokok' => 1000000,
            'nominal_total' => 1000000,
            'status' => 'pending'
        ]);

        // Path A: Admin confirms Cash payment
        $responseCash = $this->actingAs($this->admin)
            ->post(route('admin.tagihan.konfirmasiCash', $tagihan->id), [
                'catatan' => 'Cash received.'
            ]);

        $responseCash->assertRedirect();
        $tagihan->refresh();
        $this->assertEquals('lunas', $tagihan->status);
        $this->assertEquals('cash', $tagihan->metode_pembayaran);
        $this->assertDatabaseHas('pembayaran', [
            'tagihan_id' => $tagihan->id,
            'payment_type' => 'cash',
            'nominal' => 1000000
        ]);

        // Reset tagihan to pending for Path B
        $tagihan->update(['status' => 'pending', 'metode_pembayaran' => null]);
        Pembayaran::where('tagihan_id', $tagihan->id)->delete();

        // Path B: Midtrans Webhook Callback
        $orderId = $tagihan->order_id;
        $signature = hash('sha512', $orderId . '200' . '1000000' . config('midtrans.server_key'));

        $payloadMidtrans = [
            'order_id' => $orderId,
            'status_code' => '200',
            'gross_amount' => '1000000',
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
            'transaction_id' => 'tx-midtrans-test-111'
        ];

        $responseMidtrans = $this->postJson(route('api.midtrans.callback'), $payloadMidtrans);
        $responseMidtrans->assertStatus(200);

        $tagihan->refresh();
        $this->assertEquals('lunas', $tagihan->status);
        $this->assertEquals('midtrans', $tagihan->metode_pembayaran);
        $this->assertDatabaseHas('pembayaran', [
            'tagihan_id' => $tagihan->id,
            'payment_type' => 'bank_transfer',
            'nominal' => 1000000
        ]);
    }

    /**
     * Kebijakan 6: Status Kamar Saat Penyewa Keluar (Kamar TETAP "terisi")
     */
    public function test_room_status_remains_occupied_on_checkout(): void
    {
        $penyewa = $this->createTenant();

        // Verify room is occupied when tenant is active
        $this->assertEquals('terisi', $this->kamar->refresh()->status);

        // Perform checkout
        $response = $this->actingAs($this->admin)
            ->post(route('admin.penyewa.checkout.process', $penyewa->id), [
                'apakah_ada_kerusakan' => 0,
            ]);

        $response->assertRedirect();
        
        $penyewa->refresh();
        $this->assertEquals('nonaktif', $penyewa->status);

        // Assert Kamar status remains 'terisi' (requires manual admin action later)
        $this->assertEquals('terisi', $this->kamar->refresh()->status);
    }

    /**
     * Kebijakan 7: Deposit is correctly recorded
     */
    public function test_deposit_recording(): void
    {
        $penyewa = $this->createTenant();
        $this->assertEquals(200000, (float)$penyewa->deposit);
    }

    /**
     * Resolusi Aturan Durasi Minimum & Maksimum
     */
    public function test_rental_duration_limits(): void
    {
        $tenantUser = User::create([
            'nama' => 'Penyewa Reservasi',
            'email' => 'reservasi@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081299998811',
            'role' => 'penyewa',
        ]);

        // 1. Duration too short (0)
        $payloadShort = [
            'kamar_id' => $this->kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'durasi' => 0,
            'is_dp' => 0,
        ];

        $response1 = $this->actingAs($tenantUser)
            ->post(route('penyewa.reservasi.store'), $payloadShort);

        $response1->assertStatus(302);
        $response1->assertSessionHasErrors(['durasi']);

        // 2. Duration too long (13 months, max is 12)
        $payloadLong = [
            'kamar_id' => $this->kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'durasi' => 13,
            'is_dp' => 0,
        ];

        $response2 = $this->actingAs($tenantUser)
            ->post(route('penyewa.reservasi.store'), $payloadLong);

        $response2->assertStatus(302);
        $response2->assertSessionHasErrors(['durasi']);
        
        // 3. Valid duration (2 months)
        $payloadValid = [
            'kamar_id' => $this->kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'durasi' => 2,
            'is_dp' => 0,
        ];

        $response3 = $this->actingAs($tenantUser)
            ->post(route('penyewa.reservasi.store'), $payloadValid);

        $response3->assertStatus(302);
        $response3->assertSessionHasNoErrors();
    }

    /**
     * Test that billing events are correctly handled by listeners and dispatch queued jobs.
     */
    public function test_billing_events_dispatch_queued_jobs(): void
    {
        \Illuminate\Support\Facades\Queue::fake();

        $penyewa = $this->createTenant();
        
        $tagihan = Tagihan::create([
            'penyewa_id' => $penyewa->id,
            'order_id' => 'TGH-TEST-EVENTS',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-06-01',
            'tanggal_jatuh_tempo' => '2026-06-10',
            'nominal_pokok' => 1000000,
            'nominal_total' => 1000000,
            'nominal_denda' => 0,
            'bulan_keterlambatan' => 0,
            'status' => 'pending'
        ]);

        // Execute listener handlers to test listener-to-job wiring
        (new \App\Listeners\HandleTagihanDibuat())->handle(new \App\Events\TagihanDibuat($tagihan));
        (new \App\Listeners\HandleReminderPenyewa())->handle(new \App\Events\ReminderPenyewa($tagihan));
        (new \App\Listeners\HandleNotifikasiWali())->handle(new \App\Events\NotifikasiWali($tagihan));
        (new \App\Listeners\HandleDendaDikenakan())->handle(new \App\Events\DendaDikenakan($tagihan));

        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\KirimNotifikasiTagihanJob::class, function ($job) use ($tagihan) {
            return $job->tagihan->id === $tagihan->id;
        });

        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\KirimReminderJatuhTempoJob::class, 2); // Dispatched by ReminderPenyewa and DendaDikenakan

        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\KirimNotifikasiWaliJob::class, function ($job) use ($tagihan) {
            return $job->tagihan->id === $tagihan->id;
        });
    }

    /**
     * Test that billing notifications send emails and log successfully.
     */
    public function test_billing_notifications_send_emails_and_logs(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $penyewa = $this->createTenant();
        
        $tagihan = Tagihan::create([
            'penyewa_id' => $penyewa->id,
            'order_id' => 'TGH-TEST-NOTIF',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-06-01',
            'tanggal_jatuh_tempo' => '2026-06-10',
            'nominal_pokok' => 1000000,
            'nominal_total' => 1000000,
            'nominal_denda' => 0,
            'bulan_keterlambatan' => 1,
            'status' => 'pending'
        ]);

        $notifikasiService = app(\App\Services\NotifikasiService::class);

        // Test Kirim Reminder (bulan_keterlambatan = 1) -> WA + Email
        $notifikasiService->kirimReminderJatuhTempo($tagihan);

        $this->assertDatabaseHas('log_notifikasi', [
            'penyewa_id' => $penyewa->id,
            'tagihan_id' => $tagihan->id,
            'channel' => 'email',
            'event' => 'reminder_penyewa',
            'status' => 'sukses'
        ]);

        $this->assertDatabaseHas('log_notifikasi', [
            'penyewa_id' => $penyewa->id,
            'tagihan_id' => $tagihan->id,
            'channel' => 'whatsapp',
            'event' => 'reminder_penyewa',
            'status' => 'sukses'
        ]);

        // Test Kirim Denda (bulan_keterlambatan = 3) -> WA + Email
        $tagihan->update(['bulan_keterlambatan' => 3, 'nominal_denda' => 50000, 'nominal_total' => 1050000]);
        $notifikasiService->kirimReminderJatuhTempo($tagihan);

        $this->assertDatabaseHas('log_notifikasi', [
            'penyewa_id' => $penyewa->id,
            'tagihan_id' => $tagihan->id,
            'channel' => 'email',
            'event' => 'denda_dikenakan',
            'status' => 'sukses'
        ]);

        $this->assertDatabaseHas('log_notifikasi', [
            'penyewa_id' => $penyewa->id,
            'tagihan_id' => $tagihan->id,
            'channel' => 'whatsapp',
            'event' => 'denda_dikenakan',
            'status' => 'sukses'
        ]);

        $this->assertDatabaseHas('log_notifikasi', [
            'penyewa_id' => $penyewa->id,
            'tagihan_id' => $tagihan->id,
            'channel' => 'whatsapp',
            'event' => 'notifikasi_wali_eskalasi',
            'status' => 'sukses'
        ]);
    }

    /**
     * Test that tenant billing pages and dashboard render successfully.
     */
    public function test_tenant_billing_pages_and_dashboard_render_successfully(): void
    {
        $penyewa = $this->createTenant('tenant.page.test@example.com', '081234567899', '1234567890123499');
        
        $tagihan = Tagihan::create([
            'penyewa_id' => $penyewa->id,
            'order_id' => 'TGH-PAGE-TEST-1',
            'periode_bulan' => 6,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => '2026-06-01',
            'tanggal_jatuh_tempo' => '2026-06-10',
            'nominal_pokok' => 1000000,
            'nominal_total' => 1000000,
            'nominal_denda' => 0,
            'bulan_keterlambatan' => 0,
            'status' => 'pending'
        ]);

        $this->actingAs($penyewa->user);

        // 1. Visit tenant dashboard
        $responseDashboard = $this->get(route('penyewa.dashboard'));
        $responseDashboard->assertStatus(200);
        $responseDashboard->assertSee('TGH-PAGE-TEST-1');

        // 2. Visit tenant bill index
        $responseIndex = $this->get(route('penyewa.tagihan.index'));
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('TGH-PAGE-TEST-1');

        // 3. Visit tenant bill show page
        $responseShow = $this->get(route('penyewa.tagihan.show', $tagihan->id));
        $responseShow->assertStatus(200);
        $responseShow->assertSee('TGH-PAGE-TEST-1');

        // 4. Visit tenant profile edit page
        $responseProfile = $this->get(route('penyewa.profile.edit'));
        $responseProfile->assertStatus(200);
    }

    /**
     * Skenario Pengujian: Penyewa harian dan mingguan dieksklusi mutlak dari penagihan otomatis bulanan.
     */
    public function test_exclude_harian_mingguan_from_auto_billing(): void
    {
        // 1. Penyewa Harian Aktif
        $userHarian = User::create([
            'nama' => 'Penyewa Harian',
            'email' => 'harian@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081299991111',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);
        $penyewaHarian = Penyewa::create([
            'user_id' => $userHarian->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1122334455667701',
            'tanggal_masuk' => '2026-06-25',
            'tanggal_keluar_seharusnya' => '2026-06-30',
            'nama_wali' => 'Wali Harian',
            'no_wali' => '081122334401',
            'status' => 'aktif',
            'tipe_sewa' => 'harian',
            'durasi' => 5,
        ]);

        // 2. Penyewa Mingguan Aktif
        $userMingguan = User::create([
            'nama' => 'Penyewa Mingguan',
            'email' => 'mingguan@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081299992222',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);
        $penyewaMingguan = Penyewa::create([
            'user_id' => $userMingguan->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1122334455667702',
            'tanggal_masuk' => '2026-06-20',
            'tanggal_keluar_seharusnya' => '2026-06-27',
            'nama_wali' => 'Wali Mingguan',
            'no_wali' => '081122334402',
            'status' => 'aktif',
            'tipe_sewa' => 'mingguan',
            'durasi' => 1,
        ]);

        // Simulasikan tanggal berjalan adalah tanggal 1 bulan berikutnya (1 Juli 2026)
        Carbon::setTestNow(Carbon::parse('2026-07-01 00:05:00'));

        // Jalankan scheduler generate bulanan
        $this->artisan('tagihan:generate-bulanan');

        // Pastikan tidak ada tagihan untuk periode 07-2026 untuk penyewa harian & mingguan
        $tagihanHarian = Tagihan::where('penyewa_id', $penyewaHarian->id)
            ->where('periode_bulan', 7)
            ->where('periode_tahun', 2026)
            ->first();

        $tagihanMingguan = Tagihan::where('penyewa_id', $penyewaMingguan->id)
            ->where('periode_bulan', 7)
            ->where('periode_tahun', 2026)
            ->first();

        $this->assertNull($tagihanHarian);
        $this->assertNull($tagihanMingguan);

        Carbon::setTestNow();
    }

    /**
     * Skenario Pengujian: Perpanjangan manual dan strict unit matching.
     */
    public function test_manual_extension_validations_and_invoice_creation(): void
    {
        \Illuminate\Support\Facades\Event::fake([\App\Events\TagihanDibuat::class]);

        $userHarian = User::create([
            'nama' => 'Penyewa Harian Baru',
            'email' => 'harian.baru@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081299993333',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);
        $penyewaHarian = Penyewa::create([
            'user_id' => $userHarian->id,
            'kamar_id' => $this->kamar->id,
            'nik' => '1122334455667703',
            'tanggal_masuk' => '2026-06-25',
            'tanggal_keluar_seharusnya' => '2026-06-30',
            'nama_wali' => 'Wali Harian Baru',
            'no_wali' => '081122334403',
            'status' => 'aktif',
            'tipe_sewa' => 'harian',
            'durasi' => 5,
        ]);

        // 1. Test validation error when trying to extend with different unit ('mingguan')
        $response1 = $this->actingAs($this->admin)
            ->post(route('admin.penyewa.perpanjang', $penyewaHarian->id), [
                'durasi_tambahan' => 2,
                'tipe_sewa' => 'mingguan',
            ]);
        $response1->assertSessionHas('error', 'Unit perpanjangan harus sama dengan tipe sewa awal penyewa.');

        // 2. Test successful extension with correct unit ('harian')
        $response2 = $this->actingAs($this->admin)
            ->post(route('admin.penyewa.perpanjang', $penyewaHarian->id), [
                'durasi_tambahan' => 3,
                'tipe_sewa' => 'harian',
            ]);

        $response2->assertRedirect();
        $response2->assertSessionHas('success', 'Kontrak penyewa berhasil diperpanjang secara manual.');

        $penyewaHarian->refresh();
        $this->assertEquals(8, $penyewaHarian->durasi);
        $this->assertEquals('2026-07-03', $penyewaHarian->tanggal_keluar_seharusnya->toDateString());

        // Cek tagihan baru
        $tagihan = Tagihan::where('penyewa_id', $penyewaHarian->id)
            ->where('order_id', 'like', 'TGH-EXT-%')
            ->first();

        $this->assertNotNull($tagihan);
        $this->assertEquals('pending', $tagihan->status);
        
        // Cek nominal tagihan perpanjangan: 3 hari
        // Tarif kamar per bulan: 1000000. Tarif harian: 1000000/30 = 33333.33 -> ceil = 33334. Total 3 hari = 100000
        $this->assertEquals(100000, (float)$tagihan->nominal_total);

        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\TagihanDibuat::class);
    }

    /**
     * Test checkout dengan pemotongan deposit (Skenario B) dan pencatatan pengeluaran otomatis.
     */
    public function test_tenant_checkout_with_deposit_deduction_and_expense_recording(): void
    {
        $penyewa = $this->createTenant();
        $this->assertEquals(200000.00, (float)$penyewa->deposit);

        // Create a fake file instead of an image to bypass GD extension requirement
        $file = \Illuminate\Http\UploadedFile::fake()->create('nota.jpg', 100, 'image/jpeg');

        // Kirim request checkout dengan potongan deposit 150.000 karena kerusakan
        $response = $this->actingAs($this->admin)
            ->post(route('admin.penyewa.checkout.process', $penyewa->id), [
                'apakah_ada_kerusakan' => 1,
                'nominal_potongan' => 150000,
                'keterangan' => 'Kunci kamar patah.',
                'bukti_nota' => $file,
            ]);

        $response->assertRedirect(route('admin.penyewa.index'));
        $penyewa->refresh();

        // Deposit menjadi 0 karena sisa 50.000 sudah dikembalikan ke penyewa via pengeluaran
        $this->assertEquals(0, (float)$penyewa->deposit);
        $this->assertEquals('nonaktif', $penyewa->status);

        // Pengeluaran tercatat
        $pengeluaran = \App\Models\Pengeluaran::where('kategori', 'maintenance')->first();
        $this->assertNotNull($pengeluaran);
        $this->assertEquals(150000.00, (float)$pengeluaran->nominal);
        $this->assertStringContainsString('Kamar ' . $penyewa->kamar->nomor_kamar, $pengeluaran->nama_pengeluaran);
        $this->assertEquals('Kunci kamar patah.', $pengeluaran->keterangan);
    }

    /**
     * Test blocking of nonaktif tenants from login and dashboard access via RoleMiddleware.
     */
    public function test_nonaktif_tenant_middleware_login_rejection(): void
    {
        $penyewa = $this->createTenant();

        // Perform checkout
        $this->actingAs($this->admin)
            ->post(route('admin.penyewa.checkout.process', $penyewa->id), [
                'apakah_ada_kerusakan' => 0,
            ]);

        $penyewa->refresh();
        $this->assertEquals('nonaktif', $penyewa->status);

        // Attempt access to tenant dashboard
        $user = $penyewa->user;
        $response = $this->actingAs($user)
            ->get(route('penyewa.dashboard'));

        // Should be redirected to landing page with error
        $response->assertRedirect(route('landing.index'));
    }
}

