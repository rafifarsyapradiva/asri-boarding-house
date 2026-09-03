<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Penyewa;
use App\Models\Kamar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_reservasi_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/reservasi/login');
        $response->assertStatus(200);
    }

    public function test_admin_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
    }

    public function test_penyewa_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/penyewa/login');
        $response->assertStatus(200);
    }

    public function test_reservasi_users_can_authenticate(): void
    {
        $user = User::factory()->create([
            'role' => 'penyewa',
            'no_hp' => '081234567890',
        ]);

        $response = $this->post('/reservasi/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('landing.index'));
    }

    public function test_admin_can_authenticate_at_admin_portal(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'no_hp' => '081234567891',
        ]);

        $response = $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_non_admin_cannot_authenticate_at_admin_portal(): void
    {
        $user = User::factory()->create([
            'role' => 'penyewa',
            'no_hp' => '081234567890',
        ]);

        $response = $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_active_tenant_can_authenticate_at_penyewa_portal(): void
    {
        $kamar = Kamar::create([
            'nomor_kamar' => '102',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 750000,
            'status' => 'terisi',
        ]);

        $user = User::factory()->create([
            'role' => 'penyewa',
            'no_hp' => '081234567892',
        ]);

        Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => now()->toDateString(),
            'nama_wali' => 'Wali Rian',
            'no_wali' => '081111111111',
            'status' => 'aktif',
        ]);

        $response = $this->post('/penyewa/login', [
            'login' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('penyewa.dashboard'));
    }

    public function test_active_tenant_can_authenticate_with_exact_phone_number_at_penyewa_portal(): void
    {
        $kamar = Kamar::create([
            'nomor_kamar' => '102b',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 750000,
            'status' => 'terisi',
        ]);

        $user = User::factory()->create([
            'role' => 'penyewa',
            'no_hp' => '081234567895',
        ]);

        Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'nik' => '1234567890123457',
            'tanggal_masuk' => now()->toDateString(),
            'nama_wali' => 'Wali Rian',
            'no_wali' => '081111111111',
            'status' => 'aktif',
        ]);

        $response = $this->post('/penyewa/login', [
            'login' => '081234567895',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('penyewa.dashboard'));
    }

    public function test_active_tenant_can_authenticate_with_different_formatted_phone_number_at_penyewa_portal(): void
    {
        $kamar = Kamar::create([
            'nomor_kamar' => '102c',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 750000,
            'status' => 'terisi',
        ]);

        // Stored as +628...
        $user = User::factory()->create([
            'role' => 'penyewa',
            'no_hp' => '+6281234567896',
        ]);

        Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'nik' => '1234567890123458',
            'tanggal_masuk' => now()->toDateString(),
            'nama_wali' => 'Wali Rian',
            'no_wali' => '081111111111',
            'status' => 'aktif',
        ]);

        // Try logging in with 08...
        $response = $this->post('/penyewa/login', [
            'login' => '081234567896',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('penyewa.dashboard'));
    }

    public function test_active_tenant_can_authenticate_with_messy_formatted_phone_number_at_penyewa_portal(): void
    {
        $kamar = Kamar::create([
            'nomor_kamar' => '102d',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 750000,
            'status' => 'terisi',
        ]);

        // Stored as 6281234567897
        $user = User::factory()->create([
            'role' => 'penyewa',
            'no_hp' => '6281234567897',
        ]);

        Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'nik' => '1234567890123459',
            'tanggal_masuk' => now()->toDateString(),
            'nama_wali' => 'Wali Messy',
            'no_wali' => '081111111112',
            'status' => 'aktif',
        ]);

        // Try logging in with spaces, hyphens, and brackets: "+62 (812) 3456-7897"
        $response = $this->post('/penyewa/login', [
            'login' => '+62 (812) 3456-7897',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('penyewa.dashboard'));
    }

    public function test_non_active_tenant_cannot_authenticate_at_penyewa_portal(): void
    {
        $user = User::factory()->create([
            'role' => 'penyewa',
            'no_hp' => '081234567892',
        ]);

        // No penyewa record, or status !== 'aktif'
        $response = $this->post('/penyewa/login', [
            'login' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('login');
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('reservasi.login'));
    }

    public function test_admin_gets_logged_out_when_accessing_reservation_login_portal(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'no_hp' => '081234567891',
        ]);

        $response = $this->actingAs($admin)->get('/reservasi/login');
        
        $this->assertGuest();
        $response->assertRedirect(route('reservasi.login'));
    }

    public function test_reservation_user_gets_logged_out_when_accessing_admin_login_portal(): void
    {
        $user = User::factory()->create([
            'role' => 'penyewa',
            'no_hp' => '081234567890',
        ]);

        $response = $this->actingAs($user)->get('/admin/login');
        
        $this->assertGuest();
        $response->assertRedirect(route('admin.login'));
    }

    public function test_inactive_tenant_cannot_access_tenant_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'penyewa',
            'no_hp' => '081234567890',
        ]);

        $response = $this->actingAs($user)->get('/penyewa/dashboard');
        
        $response->assertRedirect(route('landing.index'));
        $response->assertSessionHas('error');
    }

    public function test_admin_logout_redirects_to_admin_login(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'no_hp' => '081234567891',
        ]);

        $response = $this->actingAs($admin)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('admin.login'));
    }

    public function test_active_tenant_logout_redirects_to_penyewa_login(): void
    {
        $kamar = Kamar::create([
            'nomor_kamar' => '103',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 750000,
            'status' => 'terisi',
        ]);

        $user = User::factory()->create([
            'role' => 'penyewa',
            'no_hp' => '081234567892',
        ]);

        Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'nik' => '1234567890123456',
            'tanggal_masuk' => now()->toDateString(),
            'nama_wali' => 'Wali Rian',
            'no_wali' => '081111111111',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('penyewa.login'));
    }

    public function test_admin_must_change_password_on_first_login(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'require_password_change' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.force-change-password'));
    }

    public function test_admin_cannot_access_other_admin_pages_if_password_change_required(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'require_password_change' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.kamar.index'));

        $response->assertRedirect(route('admin.force-change-password'));
    }

    public function test_admin_can_successfully_change_password_on_first_login(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'require_password_change' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.force-change-password.update'), [
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertFalse($admin->fresh()->require_password_change);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('NewPassword123', $admin->fresh()->password));
    }

    public function test_admin_profile_email_update_validation(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'require_password_change' => false,
        ]);

        // Non-Gmail email should fail
        $response = $this->actingAs($admin)->patch(route('admin.profile.update'), [
            'nama' => $admin->nama,
            'no_hp' => $admin->no_hp,
            'email' => 'admin@yahoo.com',
        ]);
        $response->assertSessionHasErrors('email');

        // Valid Gmail should succeed
        $response = $this->actingAs($admin)->patch(route('admin.profile.update'), [
            'nama' => $admin->nama,
            'no_hp' => $admin->no_hp,
            'email' => 'admin.new@gmail.com',
        ]);
        $response->assertRedirect(route('admin.profile.edit'));
        $this->assertEquals('admin.new@gmail.com', $admin->fresh()->email);
    }

    public function test_admin_profile_password_update_validation(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'require_password_change' => false,
        ]);

        // Weak password (no numbers/letters check) - if min 8 is enforced, less than 8 should fail
        $response = $this->actingAs($admin)->patch(route('admin.profile.update'), [
            'nama' => $admin->nama,
            'no_hp' => $admin->no_hp,
            'email' => 'admin@gmail.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);
        $response->assertSessionHasErrors('password');

        // Strong password should succeed
        $response = $this->actingAs($admin)->patch(route('admin.profile.update'), [
            'nama' => $admin->nama,
            'no_hp' => $admin->no_hp,
            'email' => 'admin@gmail.com',
            'password' => 'NewStrongPassword123',
            'password_confirmation' => 'NewStrongPassword123',
        ]);
        $response->assertRedirect(route('admin.profile.edit'));
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('NewStrongPassword123', $admin->fresh()->password));
    }

    public function test_intended_redirect_to_reservasi_pembayaran_works_after_login(): void
    {
        $user = User::factory()->create([
            'role' => 'penyewa',
            'no_hp' => '081234567890',
        ]);

        $targetUrl = route('penyewa.pembayaran');
        
        $response = $this->withSession(['url.intended' => $targetUrl])
            ->post('/reservasi/login', [
                'email' => $user->email,
                'password' => 'password',
            ]);

        $this->assertAuthenticated();
        $response->assertRedirect($targetUrl);
    }

    public function test_login_preserves_sewa_query_params_and_redirects_to_intended_room_page(): void
    {
        // Set up a mock kamar for the route to resolve if necessary, or just mock route param
        $user = User::factory()->create([
            'role' => 'penyewa',
            'no_hp' => '081234567890',
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => '109T',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 750000,
            'status' => 'tersedia',
        ]);

        $roomPageUrl = route('landing.show', $kamar->id);

        $response = $this->withHeaders(['Referer' => $roomPageUrl])
            ->get('/reservasi/login?tipe_sewa=mingguan&durasi=3');
        $response->assertStatus(200);

        $response = $this->post('/reservasi/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect($roomPageUrl . '?tipe_sewa=mingguan&durasi=3');
    }

    public function test_login_with_array_email_returns_validation_error_instead_of_500(): void
    {
        $response = $this->postJson('/reservasi/login', [
            'email' => ['invalid-array-payload'],
            'password' => 'password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_login_email_is_automatically_trimmed_and_lowercased(): void
    {
        $user = User::factory()->create([
            'role' => 'penyewa',
            'email' => 'testuser@example.com',
            'password' => bcrypt('password123'),
            'no_hp' => '081234567899',
        ]);

        $response = $this->post('/reservasi/login', [
            'email' => '  TESTUSER@EXAMPLE.COM  ',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
    }
}
