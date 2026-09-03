<?php

namespace App\Services;

use App\Models\Pembayaran;
use Illuminate\Support\Facades\Storage;

class PdfNotaService
{
    /**
     * Injeksi dependensi PdfGeneratorInterface.
     */
    public function __construct(
        protected PdfGeneratorInterface $pdfGenerator
    ) {}

    /**
     * Pembuatan nota pembayaran otomatis menggunakan generator PDF yang diabstraksi.
     */
    public function generate(Pembayaran $pembayaran): string
    {
        // 1. Eager load relasi lengkap
        $pembayaran->load(['tagihan.penyewa.user', 'tagihan.penyewa.kamar']);

        // 2. Tentukan nama file
        $filename = 'nota/' . $pembayaran->transaction_id . '.pdf';

        // 3. Render HTML & generate PDF via generator interface
        $tagihan = $pembayaran->tagihan;
        $nominalDeposit = $tagihan->nominal_total - $tagihan->nominal_pokok - $tagihan->nominal_denda;
        $appName = \App\Models\Setting::get('logo_text', 'Asri Boarding House');
        $contactAddress = \App\Models\Setting::get('contact_address');
        $contactWhatsapp = \App\Models\Setting::get('contact_whatsapp');

        $pdfOutput = $this->pdfGenerator->generate('pdf.nota-pembayaran', [
            'pembayaran'      => $pembayaran,
            'tagihan'         => $tagihan,
            'penyewa'         => $tagihan->penyewa,
            'nominalDeposit'  => $nominalDeposit,
            'appName'         => $appName,
            'contactAddress'  => $contactAddress,
            'contactWhatsapp' => $contactWhatsapp,
        ], 'A5', 'portrait');

        // 4. Simpan PDF ke storage (direktori lokal public/nota/)
        Storage::put('public/' . $filename, $pdfOutput);

        // 5. Perbarui kolom pdf_path pada tabel pembayaran
        $pembayaran->update([
            'pdf_path' => $filename,
        ]);

        return $filename;
    }

    /**
     * Get the PDF file path, generating it if it doesn't exist or is empty.
     */
    public function getOrGeneratePdfPath(Pembayaran $pembayaran): string
    {
        $pdfPath = $pembayaran->pdf_path;

        if (empty($pdfPath) || !Storage::exists('public/' . $pdfPath)) {
            $pdfPath = $this->generate($pembayaran);
        }

        return 'public/' . $pdfPath;
    }
}
