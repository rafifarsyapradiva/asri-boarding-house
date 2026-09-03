<?php

namespace Tests\Unit;


use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\Tagihan;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagihanBannerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_waiting_payment_banner_when_not_late()
    {
        $tagihan = new Tagihan([
            'status' => 'pending',
            'bulan_keterlambatan' => 0,
            'tanggal_jatuh_tempo' => Carbon::now()->addDays(3),
        ]);

        $banner = $tagihan->banner_status;

        $this->assertEquals('Tagihan Menunggu Pembayaran', $banner['title']);
        $this->assertStringContainsString('3 Hari lagi', $banner['message']);
    }

    #[Test]
    public function it_returns_today_due_message_when_due_date_is_today()
    {
        $tagihan = new Tagihan([
            'status' => 'pending',
            'bulan_keterlambatan' => 0,
            'tanggal_jatuh_tempo' => Carbon::today(),
        ]);

        $banner = $tagihan->banner_status;

        $this->assertEquals('Tagihan Menunggu Pembayaran', $banner['title']);
        $this->assertStringContainsString('Hari ini adalah batas akhir pembayaran', $banner['message']);
    }

    #[Test]
    public function it_returns_overdue_month_1_banner_when_bulan_keterlambatan_is_1()
    {
        $tagihan = new Tagihan([
            'status' => 'terlambat',
            'bulan_keterlambatan' => 1,
            'tanggal_jatuh_tempo' => Carbon::now()->subDays(5),
        ]);

        $banner = $tagihan->banner_status;

        $this->assertEquals('Tagihan Terlambat (Bulan 1)', $banner['title']);
        $this->assertStringContainsString('Belum ada denda', $banner['message']);
    }

    #[Test]
    public function it_returns_overdue_month_2_banner_when_bulan_keterlambatan_is_2()
    {
        $tagihan = new Tagihan([
            'status' => 'terlambat',
            'bulan_keterlambatan' => 2,
            'tanggal_jatuh_tempo' => Carbon::now()->subMonth(),
        ]);

        $banner = $tagihan->banner_status;

        $this->assertEquals('Tagihan Terlambat (Bulan 2)', $banner['title']);
        $this->assertStringContainsString('Orang tua Anda telah dihubungi', $banner['message']);
    }

    #[Test]
    public function it_returns_overdue_month_3_plus_banner_when_bulan_keterlambatan_is_3()
    {
        $tagihan = new Tagihan([
            'status' => 'terlambat',
            'bulan_keterlambatan' => 3,
            'tanggal_jatuh_tempo' => Carbon::now()->subMonths(2),
        ]);

        $banner = $tagihan->banner_status;

        $this->assertEquals('Tagihan Terlambat (Bulan 3+)', $banner['title']);
        $this->assertStringContainsString('Denda keterlambatan 5%', $banner['message']);
    }
}
