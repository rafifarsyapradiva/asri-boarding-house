<?php

namespace Tests\Unit\Listeners;

use App\Events\PembayaranBerhasil;
use App\Listeners\GeneratePdfNotaListener;
use App\Models\Pembayaran;
use App\Services\PdfNotaService;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;
use Exception;

class GeneratePdfNotaListenerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_berhasil_memanggil_pdf_service(): void
    {
        $pembayaran = new Pembayaran(['id' => 10]);
        $event = new PembayaranBerhasil($pembayaran);

        $pdfService = Mockery::mock(PdfNotaService::class);
        $pdfService->shouldReceive('generate')
            ->once()
            ->with($pembayaran);

        $listener = new GeneratePdfNotaListener($pdfService);
        $listener->handle($event);

        $this->assertTrue(true);
    }

    public function test_handle_menangani_null_pembayaran_dengan_aman(): void
    {
        $pembayaran = new Pembayaran(); // Pembayaran tanpa ID
        $event = new PembayaranBerhasil($pembayaran);

        $pdfService = Mockery::mock(PdfNotaService::class);
        $pdfService->shouldReceive('generate')
            ->once()
            ->with($pembayaran);

        $listener = new GeneratePdfNotaListener($pdfService);
        $listener->handle($event);

        $this->assertTrue(true);
    }

    public function test_handle_mencatat_log_saat_pdf_service_gagal(): void
    {
        $this->expectException(Exception::class);

        $pembayaran = new Pembayaran(['id' => 99]);
        $event = new PembayaranBerhasil($pembayaran);

        $pdfService = Mockery::mock(PdfNotaService::class);
        $pdfService->shouldReceive('generate')
            ->andThrow(new Exception("PDF Engine Error"));

        Log::shouldReceive('error')->once();

        $listener = new GeneratePdfNotaListener($pdfService);
        $listener->handle($event);
    }
}
