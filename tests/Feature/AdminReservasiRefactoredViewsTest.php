<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Reservasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReservasiRefactoredViewsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'nama' => 'Admin Test',
            'email' => 'admin_refactor@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567890',
            'role' => 'admin',
            'is_active' => 1,
        ]);
    }

    /**
     * Test index view renders correctly with search and filter parameters.
     */
    public function test_admin_reservasi_index_view_renders_successfully(): void
    {
        $kamar = Kamar::create([
            'nomor_kamar' => '102',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 1200000,
            'status' => 'tersedia',
        ]);

        $user = User::create([
            'nama' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567891',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $reservasi = Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1200000,
            'is_dp' => false,
            'status' => 'pending',
            'order_id' => 'RSV-VIEW-TEST-1',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reservasi.index', ['status' => 'PENDING', 'search' => 'John']));

        $response->assertStatus(200);
        $response->assertSee('Daftar Reservasi Kamar');
        $response->assertSee('RSV-VIEW-TEST-1');
        $response->assertSee('John Doe');
    }

    /**
     * Test show view renders safely when user relationship is null (prevents 500 error).
     */
    public function test_admin_reservasi_show_view_renders_safely_when_user_is_null(): void
    {
        $kamar = Kamar::create([
            'nomor_kamar' => '103',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.5,
            'harga_bulan' => 1200000,
            'status' => 'tersedia',
        ]);

        $user = User::create([
            'nama' => 'User Deleted',
            'email' => 'deleted@example.com',
            'password' => bcrypt('password'),
            'no_hp' => '081234567899',
            'role' => 'penyewa',
            'is_active' => 1,
        ]);

        $reservasi = Reservasi::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'tipe_sewa' => 'bulanan',
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+1 month')),
            'durasi' => 1,
            'total_harga' => 1200000,
            'is_dp' => true,
            'nominal_dp' => 360000,
            'nominal_sisa' => 840000,
            'status' => 'dp',
            'order_id' => 'RSV-VIEW-TEST-NULL-USER',
        ]);

        // Simulasikan relasi user bernilai null (misal user terhapus atau bermasalah)
        $reservasi->setRelation('user', null);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.reservasi.show', $reservasi->id));

        $response->assertStatus(200);
        $response->assertSee('Detail Reservasi');
        $response->assertSee('RSV-VIEW-TEST-NULL-USER');
    }

    /**
     * Test reservation status badge component renders expected badges.
     */
    public function test_status_badge_component_renders_correct_badge_classes(): void
    {
        $viewPending = $this->blade('<x-reservation-status-badge status="pending" />');
        $viewPending->assertSee('admin-badge-warning');
        $viewPending->assertSee('PENDING');

        $viewLunas = $this->blade('<x-reservation-status-badge status="lunas" />');
        $viewLunas->assertSee('admin-badge-success');
        $viewLunas->assertSee('LUNAS');

        $viewBatal = $this->blade('<x-reservation-status-badge status="batal" />');
        $viewBatal->assertSee('admin-badge-danger');
        $viewBatal->assertSee('BATAL');
    }
}
