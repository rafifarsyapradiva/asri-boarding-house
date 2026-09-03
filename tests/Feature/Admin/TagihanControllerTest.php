<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\Tagihan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class TagihanControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_confirm_cash_payment_successfully()
    {
        $admin = User::create([
            'nama' => 'Admin Controller Test',
            'email' => 'adminctrl@example.com',
            'no_hp' => '081234567890',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $penyewaUser = User::create([
            'nama' => 'Penyewa Controller Test',
            'email' => 'penyewactrl@example.com',
            'no_hp' => '089876543210',
            'password' => bcrypt('password'),
            'role' => 'penyewa',
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => '201',
            'lantai' => 2,
            'tipe' => 'deluxe',
            'luas_m2' => 15.0,
            'harga_bulan' => 1200000,
            'status' => 'terisi',
        ]);

        $penyewa = Penyewa::create([
            'user_id' => $penyewaUser->id,
            'kamar_id' => $kamar->id,
            'harga_sewa' => 1200000,
            'nik' => '1234567890123499',
            'tanggal_masuk' => now()->toDateString(),
            'tanggal_keluar_seharusnya' => now()->addMonth()->toDateString(),
            'nama_wali' => 'Wali Controller',
            'no_wali' => '081288887777',
            'deposit' => 1200000,
            'status' => 'aktif',
            'tanggal_billing' => 1,
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        $tagihan = Tagihan::create([
            'penyewa_id' => $penyewa->id,
            'order_id' => 'INV-CTRL-100',
            'periode_bulan' => now()->month,
            'periode_tahun' => now()->year,
            'tanggal_tagihan' => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(5)->toDateString(),
            'nominal_pokok' => 1200000,
            'nominal_denda' => 0,
            'nominal_total' => 1200000,
            'bulan_keterlambatan' => 0,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.tagihan.konfirmasiCash', $tagihan), [
                'catatan' => 'Lunas via kasir kost',
            ]);

        $response->assertRedirect(route('admin.tagihan.show', $tagihan));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tagihan', [
            'id' => $tagihan->id,
            'status' => 'lunas',
            'metode_pembayaran' => 'cash',
        ]);
    }
}
