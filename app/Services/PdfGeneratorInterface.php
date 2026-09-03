<?php

namespace App\Services;

interface PdfGeneratorInterface
{
    /**
     * Generate PDF from a view path and data.
     *
     * @param string $view
     * @param array $data
     * @param string $paper
     * @param string $orientation
     * @return string Raw PDF binary data
     */
    public function generate(string $view, array $data = [], string $paper = 'A4', string $orientation = 'portrait'): string;
}
