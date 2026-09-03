<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Tagihan;
use App\Models\User;
use App\Models\Penyewa;
use App\Models\Kamar;
use App\Services\TagihanService;
use App\Events\PembayaranCashDikonfirmasi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;

class TagihanServiceTest extends TestCase
{
    use RefreshDatabase;

    private TagihanService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(TagihanService::class);
    }

    #[Test]
    public function it_successfully_confirms_cash_payment_and_dispatches_event()
    {
        Event::fake();

        $admin = User::create([
            'nama' => 'Admin Test',
            'email' => 'admin@example.com',
            'no_hp' => '08123456789',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $userPenyewa = User::create([
            'nama' => 'Penyewa Test',
            'email' => 'penyewa@example.com',
            'no_hp' => '08987654321',
            'password' => bcrypt('password'),
            'role' => 'penyewa',
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => '101',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'terisi',
        ]);

        $penyewa = Penyewa::create([
            'user_id' => $userPenyewa->id,
            'kamar_id' => $kamar->id,
            'harga_sewa' => 1000000,
            'nik' => '1234567890123456',
            'tanggal_masuk' => now()->toDateString(),
            'tanggal_keluar_seharusnya' => now()->addMonth()->toDateString(),
            'nama_wali' => 'Wali Test',
            'no_wali' => '081299998888',
            'deposit' => 1000000,
            'status' => 'aktif',
            'tanggal_billing' => 1,
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        $tagihan = Tagihan::create([
            'penyewa_id' => $penyewa->id,
            'order_id' => 'INV-TEST-001',
            'periode_bulan' => now()->month,
            'periode_tahun' => now()->year,
            'tanggal_tagihan' => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(5)->toDateString(),
            'nominal_pokok' => 1000000,
            'nominal_denda' => 0,
            'nominal_total' => 1000000,
            'bulan_keterlambatan' => 0,
            'status' => 'pending',
        ]);

        $pembayaran = $this->service->confirmCashPayment(
            tagihan: $tagihan,
            adminId: $admin->id,
            catatan: 'Pembayaran cash diterima lunas'
        );

        $this->assertDatabaseHas('tagihan', [
            'id' => $tagihan->id,
            'status' => 'lunas',
            'metode_pembayaran' => 'cash',
        ]);

        $this->assertDatabaseHas('pembayaran', [
            'tagihan_id' => $tagihan->id,
            'nominal' => 1000000,
            'status_midtrans' => 'cash_confirmed',
            'dikonfirmasi_oleh' => $admin->id,
        ]);

        Event::assertDispatched(PembayaranCashDikonfirmasi::class, function ($e) use ($pembayaran) {
            return $e->pembayaran->id === $pembayaran->id;
        });
    }

    #[Test]
    public function it_correctly_filters_paginated_invoices()
    {
        $userPenyewa = User::create([
            'nama' => 'John Doe',
            'email' => 'john@example.com',
            'no_hp' => '08111111111',
            'password' => bcrypt('password'),
            'role' => 'penyewa',
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => '102',
            'lantai' => 1,
            'tipe' => 'standar',
            'luas_m2' => 12.0,
            'harga_bulan' => 1000000,
            'status' => 'terisi',
        ]);

        $penyewa = Penyewa::create([
            'user_id' => $userPenyewa->id,
            'kamar_id' => $kamar->id,
            'harga_sewa' => 1000000,
            'nik' => '1234567890123457',
            'tanggal_masuk' => now()->toDateString(),
            'tanggal_keluar_seharusnya' => now()->addMonth()->toDateString(),
            'nama_wali' => 'Wali John',
            'no_wali' => '081199998888',
            'deposit' => 1000000,
            'status' => 'aktif',
            'tanggal_billing' => 1,
            'tipe_sewa' => 'bulanan',
            'durasi' => 1,
        ]);

        Tagihan::create([
            'penyewa_id' => $penyewa->id,
            'order_id' => 'INV-MATCH-001',
            'periode_bulan' => now()->month,
            'periode_tahun' => now()->year,
            'tanggal_tagihan' => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(5)->toDateString(),
            'nominal_pokok' => 1000000,
            'nominal_denda' => 0,
            'nominal_total' => 1000000,
            'bulan_keterlambatan' => 0,
            'status' => 'pending',
        ]);

        $result = $this->service->getFilteredPaginatedTagihan(['search' => 'INV-MATCH-001']);

        $this->assertEquals(1, $result->total());
        $this->assertEquals('INV-MATCH-001', $result->first()->order_id);
    }
}
