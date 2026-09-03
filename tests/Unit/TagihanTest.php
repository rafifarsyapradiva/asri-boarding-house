<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Tagihan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagihanTest extends TestCase
{
    use RefreshDatabase;

    public function test_nominal_deposit_accessor_calculates_correctly()
    {
        $tagihan = new Tagihan([
            'nominal_total' => 1500000,
            'nominal_pokok' => 1200000,
            'nominal_denda' => 100000,
        ]);

        $this->assertEquals(200000, $tagihan->nominal_deposit);
    }

    public function test_nominal_deposit_accessor_never_returns_negative_values()
    {
        $tagihan = new Tagihan([
            'nominal_total' => 1000000,
            'nominal_pokok' => 1200000,
            'nominal_denda' => 100000,
        ]);

        $this->assertEquals(0, $tagihan->nominal_deposit);
    }

    public function test_status_badge_class_accessor_returns_correct_css_class()
    {
        $tagihanLunas = new Tagihan(['status' => 'lunas']);
        $tagihanPending = new Tagihan(['status' => 'pending']);
        $tagihanTerlambat = new Tagihan(['status' => 'terlambat']);
        $tagihanGagal = new Tagihan(['status' => 'gagal']);

        $this->assertEquals('admin-badge-success', $tagihanLunas->status_badge_class);
        $this->assertEquals('admin-badge-warning', $tagihanPending->status_badge_class);
        $this->assertEquals('admin-badge-danger', $tagihanTerlambat->status_badge_class);
        $this->assertEquals('admin-badge-danger', $tagihanGagal->status_badge_class);
    }

    public function test_wa_confirmation_message_accessor_formats_correctly()
    {
        $tagihan = new Tagihan([
            'order_id' => 'INV-2026-001',
            'periode_bulan' => 7,
            'periode_tahun' => 2026,
            'nominal_total' => 1500000,
        ]);

        $message = $tagihan->wa_confirmation_message;

        $this->assertStringContainsString('INV-2026-001', $message);
        $this->assertStringContainsString('07/2026', $message);
        $this->assertStringContainsString('Rp 1.500.000', $message);
    }

    public function test_computed_status_returns_terlambat_when_past_due_date()
    {
        $tagihan = new Tagihan([
            'status' => 'pending',
            'tanggal_jatuh_tempo' => now()->subDays(2),
        ]);

        $this->assertEquals('terlambat', $tagihan->computed_status);
        $this->assertEquals('admin-badge-danger', $tagihan->status_badge_class);
    }

    public function test_can_be_confirmed_manually_returns_true_only_for_pending_or_terlambat()
    {
        $pendingTagihan = new Tagihan(['status' => 'pending']);
        $terlambatTagihan = new Tagihan(['status' => 'terlambat']);
        $lunasTagihan = new Tagihan(['status' => 'lunas']);
        $kadaluarsaTagihan = new Tagihan(['status' => 'kadaluarsa']);

        $this->assertTrue($pendingTagihan->canBeConfirmedManually());
        $this->assertTrue($terlambatTagihan->canBeConfirmedManually());
        $this->assertFalse($lunasTagihan->canBeConfirmedManually());
        $this->assertFalse($kadaluarsaTagihan->canBeConfirmedManually());
    }
}
