<?php

namespace Tests\Feature;


use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PdfMasterLayoutTest extends TestCase
{
    #[Test]
    public function it_renders_pdf_master_layout_with_default_slots()
    {
        $view = $this->view('pdf.layouts.master', [
            'appName' => 'Asri Kost',
        ]);

        $view->assertSee('Dokumen Laporan Kost');
        $view->assertSee('Laporan ini dibuat otomatis oleh sistem manajemen Asri Kost.');
        $view->assertSee('Dicetak pada:');
    }

    #[Test]
    public function it_supports_deterministic_printed_at_override_for_testing()
    {
        $blade = '
            @extends("pdf.layouts.master")
            @section("printed-at", "01 Jan 2026 10:00:00")
            @section("content")
                <h1>Content Test</h1>
            @endsection
        ';

        $rendered = $this->blade($blade, ['appName' => 'Asri Kost']);

        $rendered->assertSee('Dicetak pada: 01 Jan 2026 10:00:00 oleh Admin');
    }

    #[Test]
    public function it_suppresses_default_footer_when_custom_footer_is_provided()
    {
        $blade = '
            @extends("pdf.layouts.master")
            @section("content")
                <h1>Content Test</h1>
            @endsection
            @section("custom-footer")
                <div id="my-custom-footer">Custom Footer Content</div>
            @endsection
        ';

        $rendered = $this->blade($blade, ['appName' => 'Asri Kost']);

        $rendered->assertSee('Content Test');
        $rendered->assertSee('Custom Footer Content');
        $rendered->assertDontSee('Laporan ini dibuat otomatis oleh sistem');
    }
}
