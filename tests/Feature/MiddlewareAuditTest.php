<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Penyewa;
use App\Models\Reservasi;
use App\Models\Kamar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class MiddlewareAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Daftarkan rute tiruan (mock routes) untuk pengujian middleware
        Route::middleware(['web', \App\Http\Middleware\EnsurePasswordChanged::class])->group(function () {
            Route::get('/test/password/check', function () { return 'passed'; })->name('some.protected.route');
            Route::get('/test/password/force-admin', function () { return 'passed-force'; })->name('admin.force-change-password');
            Route::get('/test/password/force-penyewa', function () { return 'passed-penyewa'; })->name('profile.edit');
        });

        Route::middleware(['web', \App\Http\Middleware\EnsureProfileIsComplete::class])->group(function () {
            Route::get('/test/profile/check', function () { return 'passed'; })->name('some.profile.protected');
            Route::get('/test/profil/complete', function () { return 'passed-complete'; })->name('profil.complete');
        });

        Route::middleware(['web', \App\Http\Middleware\EnsureTenantIsActive::class])->group(function () {
            Route::get('/test/tenant/check', function () { return 'passed'; })->name('some.tenant.protected');
        });

        Route::middleware(['web', \App\Http\Middleware\RoleMiddleware::class . ':admin'])->group(function () {
            Route::get('/test/role/admin-only', function () { return 'passed'; })->name('admin.only');
        });

        Route::post('/test/midtrans/webhook', function () { return 'passed'; })
            ->middleware(\App\Http\Middleware\VerifyMidtransSignature::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Tests for EnsurePasswordChanged Middleware
    |--------------------------------------------------------------------------
    */

    public function test_ensure_password_changed_allows_guests()
    {
        $response = $this->get('/test/password/check');
        $response->assertStatus(200)->assertSee('passed');
    }

    public function test_ensure_password_changed_allows_user_who_does_not_need_change()
    {
        $user = User::factory()->create(['require_password_change' => false]);
        
        $response = $this->actingAs($user)->get('/test/password/check');
        $response->assertStatus(200)->assertSee('passed');
    }

    public function test_ensure_password_changed_redirects_admin_needing_change()
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'require_password_change' => true,
        ]);

        $response = $this->actingAs($user)->get('/test/password/check');
        $response->assertRedirect(route('admin.force-change-password'));
    }

    public function test_ensure_password_changed_allows_admin_needing_change_on_excluded_route()
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'require_password_change' => true,
        ]);

        $response = $this->actingAs($user)->get('/test/password/force-admin');
        $response->assertStatus(200)->assertSee('passed-force');
    }

    public function test_ensure_password_changed_redirects_penyewa_needing_change()
    {
        $user = User::factory()->create([
            'role' => 'penyewa',
            'require_password_change' => true,
        ]);

        $response = $this->actingAs($user)->get('/test/password/check');
        $response->assertRedirect(route('profile.edit', ['tab' => 'password']));
    }

    /*
    |--------------------------------------------------------------------------
    | Tests for EnsureProfileIsComplete Middleware
    |--------------------------------------------------------------------------
    */

    public function test_ensure_profile_is_complete_allows_guests()
    {
        $response = $this->get('/test/profile/check');
        $response->assertStatus(200)->assertSee('passed');
    }

    public function test_ensure_profile_is_complete_allows_admin()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/test/profile/check');
        $response->assertStatus(200)->assertSee('passed');
    }

    public function test_ensure_profile_is_complete_allows_penyewa_with_complete_profile()
    {
        $user = User::factory()->create([
            'role' => 'penyewa',
            'no_hp' => '08123456789',
            'nik' => '1234567890123456',
            'nama_wali' => 'Wali Test',
            'no_wali' => '08987654321',
        ]);

        $response = $this->actingAs($user)->get('/test/profile/check');
        $response->assertStatus(200)->assertSee('passed');
    }

    public function test_ensure_profile_is_complete_redirects_penyewa_with_incomplete_profile()
    {
        $user = User::factory()->create([
            'role' => 'penyewa',
            'no_hp' => 'temp_123', // temporary no_hp
            'nik' => null,
        ]);

        $response = $this->actingAs($user)->get('/test/profile/check');
        $response->assertRedirect(route('profil.complete'));
    }

    public function test_ensure_profile_is_complete_allows_excluded_routes()
    {
        $user = User::factory()->create([
            'role' => 'penyewa',
            'no_hp' => 'temp_123',
        ]);

        $response = $this->actingAs($user)->get('/test/profil/complete');
        $response->assertStatus(200)->assertSee('passed-complete');
    }

    /*
    |--------------------------------------------------------------------------
    | Tests for EnsureTenantIsActive Middleware
    |--------------------------------------------------------------------------
    */

    public function test_ensure_tenant_is_active_allows_guests()
    {
        $response = $this->get('/test/tenant/check');
        $response->assertStatus(200)->assertSee('passed');
    }

    public function test_ensure_tenant_is_active_allows_admin()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/test/tenant/check');
        $response->assertStatus(200)->assertSee('passed');
    }

    public function test_ensure_tenant_is_active_allows_active_tenant()
    {
        $user = User::factory()->create(['role' => 'penyewa']);
        $kamar = Kamar::create([
            'nomor_kamar' => '102',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => now()->toDateString(),
            'nama_wali' => 'Wali Asli',
            'no_wali' => '081122334455',
            'deposit' => 200000,
            'status' => 'aktif',
            'tanggal_billing' => 1,
        ]);

        $response = $this->actingAs($user)->get('/test/tenant/check');
        $response->assertStatus(200)->assertSee('passed');
    }

    public function test_ensure_tenant_is_active_redirects_inactive_tenant_with_active_reservations()
    {
        $user = User::factory()->create(['role' => 'penyewa']);
        $kamar = Kamar::create([
            'nomor_kamar' => '102',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => now()->toDateString(),
            'nama_wali' => 'Wali Asli',
            'no_wali' => '081122334455',
            'deposit' => 200000,
            'status' => 'nonaktif',
            'tanggal_billing' => 1,
        ]);

        // Simulasikan reservasi yang masih pending
        Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1000000,
            'is_dp' => false,
            'status' => 'pending',
            'order_id' => 'RSV-DUMMY'
        ]);

        $response = $this->actingAs($user)->get('/test/tenant/check');
        $response->assertRedirect(route('penyewa.reservasi.dashboard'));
    }

    public function test_ensure_tenant_is_active_blocks_inactive_tenant_without_active_reservations()
    {
        $user = User::factory()->create(['role' => 'penyewa']);
        $kamar = Kamar::create([
            'nomor_kamar' => '102',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'tersedia'
        ]);

        Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => now()->toDateString(),
            'nama_wali' => 'Wali Asli',
            'no_wali' => '081122334455',
            'deposit' => 200000,
            'status' => 'nonaktif',
            'tanggal_billing' => 1,
        ]);

        // Tidak ada reservasi, atau hanya ada reservasi 'dikonfirmasi' / 'batal'
        Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1000000,
            'is_dp' => false,
            'status' => 'batal',
            'order_id' => 'RSV-DUMMY-BATAL'
        ]);

        $response = $this->actingAs($user)->get('/test/tenant/check');
        $response->assertRedirect(route('landing.index'));
        $response->assertSessionHas('error', 'Akses ditolak. Halaman ini khusus untuk penyewa aktif.');
    }

    /*
    |--------------------------------------------------------------------------
    | Tests for RoleMiddleware
    |--------------------------------------------------------------------------
    */

    public function test_role_middleware_redirects_guest_to_login()
    {
        $response = $this->get('/test/role/admin-only');
        $response->assertRedirect(route('login'));
    }

    public function test_role_middleware_allows_authorized_role()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/test/role/admin-only');
        $response->assertStatus(200)->assertSee('passed');
    }

    public function test_role_middleware_blocks_unauthorized_role()
    {
        $user = User::factory()->create(['role' => 'penyewa']);

        $response = $this->actingAs($user)->get('/test/role/admin-only');
        $response->assertStatus(403);
    }

    /*
    |--------------------------------------------------------------------------
    | Tests for VerifyMidtransSignature Middleware
    |--------------------------------------------------------------------------
    */

    public function test_verify_midtrans_signature_returns_500_if_server_key_missing()
    {
        config(['midtrans.server_key' => '']);

        $response = $this->postJson('/test/midtrans/webhook', [
            'order_id' => '123',
            'status_code' => '200',
            'gross_amount' => '100000',
            'signature_key' => 'some_signature',
        ]);

        $response->assertStatus(500);
        $response->assertJson(['message' => 'Internal Server Error']);
    }

    public function test_verify_midtrans_signature_returns_400_if_payload_fields_missing()
    {
        config(['midtrans.server_key' => 'test_server_key']);

        $response = $this->postJson('/test/midtrans/webhook', [
            'order_id' => '123',
            // missing status_code, gross_amount, signature_key
        ]);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Bad Request']);
    }

    public function test_verify_midtrans_signature_allows_valid_signature_with_decimal_formatting()
    {
        $serverKey = 'test_server_key';
        config(['midtrans.server_key' => $serverKey]);

        $orderId = 'TRX-101';
        $statusCode = '200';
        $grossAmount = 150000;
        
        // Midtrans signature format: SHA512(order_id + status_code + gross_amount + server_key)
        // expectedSignature1 formats gross_amount to 2 decimals: 150000.00
        $payloadString = $orderId . $statusCode . '150000.00' . $serverKey;
        $signatureKey = hash('sha512', $payloadString);

        $response = $this->postJson('/test/midtrans/webhook', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
        ]);

        $response->assertStatus(200)->assertSee('passed');
    }

    public function test_verify_midtrans_signature_allows_valid_signature_without_decimal_formatting()
    {
        $serverKey = 'test_server_key';
        config(['midtrans.server_key' => $serverKey]);

        $orderId = 'TRX-102';
        $statusCode = '200';
        $grossAmount = '150000';
        
        // expectedSignature2 uses gross_amount raw string: 150000
        $payloadString = $orderId . $statusCode . $grossAmount . $serverKey;
        $signatureKey = hash('sha512', $payloadString);

        $response = $this->postJson('/test/midtrans/webhook', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
        ]);

        $response->assertStatus(200)->assertSee('passed');
    }

    public function test_verify_midtrans_signature_blocks_invalid_signature()
    {
        config(['midtrans.server_key' => 'test_server_key']);

        $response = $this->postJson('/test/midtrans/webhook', [
            'order_id' => 'TRX-103',
            'status_code' => '200',
            'gross_amount' => '150000',
            'signature_key' => 'wrong_signature_key_hash',
        ]);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Unauthorized']);
    }
}
