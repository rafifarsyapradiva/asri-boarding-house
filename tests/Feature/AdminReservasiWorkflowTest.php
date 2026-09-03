<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\User;
use App\Models\Reservasi;
use App\Models\Penyewa;
use App\Models\Tagihan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReservasiWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user;
    private Kamar $kamar101;
    private Kamar $kamar102;

    protected function setUp(): void
    {
        parent::setUp();

        // Admin Account
        $this->admin = User::create([
            'nama' => 'Admin Asep',
            'email' => 'asri-asep@gmail.com',
            'password' => bcrypt('asriasep48'),
            'no_hp' => '6281234567890',
            'role' => 'admin',
            'require_password_change' => false,
            'is_active' => 1,
        ]);

        // Tenant/Guest User Account
        $this->user = User::create([
            'nama' => 'Rudi Gunawan',
            'email' => 'rudi@example.com',
            'password' => bcrypt('password123'),
            'no_hp' => '081234567895',
            'role' => 'penyewa',
            'is_active' => 1,
            'require_password_change' => false,
        ]);

        // Seed Rooms
        $this->kamar101 = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'vip',
            'luas_m2' => 24.0,
            'harga_bulan' => 1400000,
            'status' => 'tersedia',
        ]);

        $this->kamar102 = Kamar::create([
            'nomor_kamar' => '102',
            'lantai' => 1,
            'tipe' => 'deluxe',
            'luas_m2' => 18.0,
            'harga_bulan' => 950000,
            'status' => 'tersedia',
        ]);
    }

    /**
     * Test admin can access reservation index page and see list.
     */
    public function test_admin_can_access_index_and_see_reservations(): void
    {
        $reservasi = Reservasi::create([
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar101->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1400000,
            'status' => 'dp',
            'is_dp' => true,
            'nominal_dp' => 420000,
            'nominal_sisa' => 980000,
            'order_id' => 'RSV-DP-RUDI'
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reservasi.index'));

        $response->assertStatus(200);
        $response->assertSee('RSV-DP-RUDI');
        $response->assertSee('Rudi Gunawan');
        $response->assertSee('Kamar 101');
        $response->assertSee('DP');
    }

    /**
     * Test admin can view reservation detail page.
     */
    public function test_admin_can_view_reservation_detail(): void
    {
        $reservasi = Reservasi::create([
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar101->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1400000,
            'status' => 'dp',
            'is_dp' => true,
            'nominal_dp' => 420000,
            'nominal_sisa' => 980000,
            'order_id' => 'RSV-DP-RUDI'
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reservasi.show', $reservasi->id));

        $response->assertStatus(200);
        $response->assertSee('RSV-DP-RUDI');
        $response->assertSee('Rp 1.400.000');
        $response->assertSee('Uang Muka (DP): Rp 420.000');
        $response->assertSee('Sisa Tagihan: Rp 980.000');
    }

    /**
     * Test NIK validation constraints.
     */
    public function test_nik_validation_constraints_on_confirmation(): void
    {
        $reservasi = Reservasi::create([
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar101->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1400000,
            'status' => 'dp',
            'is_dp' => true,
            'nominal_dp' => 420000,
            'nominal_sisa' => 980000,
            'order_id' => 'RSV-DP-RUDI'
        ]);

        // 1. NIK shorter than 16 digits
        $response = $this->actingAs($this->admin)
            ->from(route('admin.reservasi.show', $reservasi->id))
            ->post(route('admin.reservasi.konfirmasi', $reservasi->id), [
                'nik' => '12345',
                'nama_wali' => 'Wali Rudi',
                'no_wali' => '081234567896',
            ]);

        $response->assertRedirect(route('admin.reservasi.show', $reservasi->id));
        $response->assertSessionHasErrors(['nik']);

        // 2. NIK non-numeric
        $response = $this->actingAs($this->admin)
            ->from(route('admin.reservasi.show', $reservasi->id))
            ->post(route('admin.reservasi.konfirmasi', $reservasi->id), [
                'nik' => 'abcdefghijklmnop',
                'nama_wali' => 'Wali Rudi',
                'no_wali' => '081234567896',
            ]);

        $response->assertRedirect(route('admin.reservasi.show', $reservasi->id));
        $response->assertSessionHasErrors(['nik']);

        // 3. No HP wali validation (regex check)
        $response = $this->actingAs($this->admin)
            ->from(route('admin.reservasi.show', $reservasi->id))
            ->post(route('admin.reservasi.konfirmasi', $reservasi->id), [
                'nik' => '1234567890123456',
                'nama_wali' => 'Wali Rudi',
                'no_wali' => '1234567', // invalid start
            ]);

        $response->assertRedirect(route('admin.reservasi.show', $reservasi->id));
        $response->assertSessionHasErrors(['no_wali']);
    }

    /**
     * Test successful reservation confirmation and transition.
     */
    public function test_successful_reservation_confirmation_and_transition(): void
    {
        $reservasi = Reservasi::create([
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar101->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1400000,
            'status' => 'dp',
            'is_dp' => true,
            'nominal_dp' => 420000,
            'nominal_sisa' => 980000,
            'order_id' => 'RSV-DP-RUDI'
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.reservasi.konfirmasi', $reservasi->id), [
                'nik' => '1234567890123456',
                'nama_wali' => 'Wali Rudi',
                'no_wali' => '081234567896',
                'catatan_admin' => 'Dokumen lengkap dan verified.',
            ]);

        $response->assertRedirect(route('admin.reservasi.index'));
        $response->assertSessionHas('success');

        // Check reservasi status and fields
        $reservasi->refresh();
        $this->assertEquals('dikonfirmasi', $reservasi->status);
        $this->assertEquals('Dokumen lengkap dan verified.', $reservasi->catatan_admin);
        $this->assertEquals($this->admin->id, $reservasi->dikonfirmasi_oleh);

        // Check Kamar status transition to terisi
        $this->kamar101->refresh();
        $this->assertEquals('terisi', $this->kamar101->status);

        // Check Penyewa record created
        $penyewa = Penyewa::where('user_id', $this->user->id)->first();
        $this->assertNotNull($penyewa);
        $this->assertEquals('1234567890123456', $penyewa->nik);
        $this->assertEquals('Wali Rudi', $penyewa->nama_wali);
        $this->assertEquals('081234567896', $penyewa->no_wali);
        $this->assertEquals('aktif', $penyewa->status);

        // Check Tagihan generated for nominal_sisa
        $this->assertDatabaseHas('tagihan', [
            'penyewa_id' => $penyewa->id,
            'nominal_total' => 980000,
            'status' => 'pending'
        ]);
    }

    /**
     * Test reservation cancellation by admin.
     */
    public function test_admin_can_cancel_reservation(): void
    {
        $reservasi = Reservasi::create([
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar101->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1400000,
            'status' => 'pending',
            'is_dp' => true,
            'nominal_dp' => 420000,
            'nominal_sisa' => 980000,
            'order_id' => 'RSV-PENDING-RUDI'
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.reservasi.batal', $reservasi->id));

        $response->assertRedirect(route('admin.reservasi.index'));
        $response->assertSessionHas('success');

        $reservasi->refresh();
        $this->assertEquals('batal', $reservasi->status);
    }

    /**
     * Test permanent deletion of cancelled reservation.
     */
    public function test_admin_can_permanently_delete_cancelled_reservation(): void
    {
        $reservasi = Reservasi::create([
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar101->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1400000,
            'status' => 'batal',
            'is_dp' => true,
            'nominal_dp' => 420000,
            'nominal_sisa' => 980000,
            'order_id' => 'RSV-BATAL-RUDI'
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.reservasi.destroy', $reservasi->id));

        $response->assertRedirect(route('admin.reservasi.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('reservasi', [
            'id' => $reservasi->id
        ]);
    }

    /**
     * Test non-admin cannot access admin reservation routes.
     */
    public function test_non_admin_cannot_access_reservation_routes(): void
    {
        $reservasi = Reservasi::create([
            'user_id' => $this->user->id,
            'kamar_id' => $this->kamar101->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1400000,
            'status' => 'dp',
            'is_dp' => true,
            'nominal_dp' => 420000,
            'nominal_sisa' => 980000,
            'order_id' => 'RSV-DP-RUDI'
        ]);

        // Try access index
        $response = $this->actingAs($this->user)
            ->get(route('admin.reservasi.index'));
        $response->assertStatus(403);

        // Try access show
        $response = $this->actingAs($this->user)
            ->get(route('admin.reservasi.show', $reservasi->id));
        $response->assertStatus(403);

        // Try post confirmation
        $response = $this->actingAs($this->user)
            ->post(route('admin.reservasi.konfirmasi', $reservasi->id), [
                'nik' => '1234567890123456',
                'nama_wali' => 'Wali Rudi',
                'no_wali' => '081234567896',
            ]);
        $response->assertStatus(403);
    }
}
