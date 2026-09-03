<?php

namespace App\Services;

use Dompdf\Dompdf;

class DompdfGenerator implements PdfGeneratorInterface
{
    /**
     * Generate PDF from a view path and data.
     */
    public function generate(string $view, array $data = [], string $paper = 'A4', string $orientation = 'portrait'): string
    {
        $html = view($view, $data)->render();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper($paper, $orientation);
        $dompdf->render();

        return $dompdf->output();
    }
}
