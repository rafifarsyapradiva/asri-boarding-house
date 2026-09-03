<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Kamar;
use App\Services\DashboardAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class DashboardAnalyticsServiceTest extends TestCase
{
    use RefreshDatabase;

    private DashboardAnalyticsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DashboardAnalyticsService::class);
    }

    #[Test]
    public function it_calculates_room_occupancy_metrics_correctly()
    {
        Kamar::create(['nomor_kamar' => '101', 'lantai' => 1, 'tipe' => 'standar', 'luas_m2' => 12, 'harga_bulan' => 1000000, 'status' => 'terisi']);
        Kamar::create(['nomor_kamar' => '102', 'lantai' => 1, 'tipe' => 'standar', 'luas_m2' => 12, 'harga_bulan' => 1000000, 'status' => 'terisi']);
        Kamar::create(['nomor_kamar' => '103', 'lantai' => 1, 'tipe' => 'standar', 'luas_m2' => 12, 'harga_bulan' => 1000000, 'status' => 'tersedia']);
        Kamar::create(['nomor_kamar' => '104', 'lantai' => 1, 'tipe' => 'standar', 'luas_m2' => 12, 'harga_bulan' => 1000000, 'status' => 'maintenance']);

        $metrics = $this->service->getDashboardMetrics();

        $this->assertEquals(4, $metrics['totalKamar']);
        $this->assertEquals(2, $metrics['kamarTerisi']);
        $this->assertEquals(1, $metrics['kamarTersedia']);
        $this->assertEquals(1, $metrics['kamarMaintenance']);
        $this->assertEquals(50.0, $metrics['occupancyRate']);
    }

    #[Test]
    public function it_returns_12_months_chart_arrays()
    {
        $metrics = $this->service->getDashboardMetrics();

        $this->assertCount(12, $metrics['labelsBulan']);
        $this->assertCount(12, $metrics['dataPemasukan']);
        $this->assertCount(12, $metrics['dataPengeluaran']);
    }
}
