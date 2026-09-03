<?php

namespace Tests\Feature;


use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PdfViewTest extends TestCase
{
    #[Test]
    public function it_renders_laporan_keuangan_pdf_view_without_errors()
    {
        $view = $this->view('pdf.laporan-keuangan', [
            'appName' => 'Asri Boarding House',
            'filterPeriode' => 'Juli 2026',
            'filterStatus' => 'Semua',
            'totalMasuk' => 15000000,
            'totalKeluar' => 5000000,
            'saldoBersih' => 10000000,
            'tagihan' => collect([
                (object)[
                    'order_id' => 'INV-001',
                    'periode_bulan' => 7,
                    'periode_tahun' => 2026,
                    'nominal_pokok' => 1500000,
                    'nominal_denda' => 0,
                    'nominal_total' => 1500000,
                    'metode_pembayaran' => 'transfer',
                    'status' => 'lunas',
                    'penyewa' => null
                ]
            ]),
            'pengeluaran' => collect([]),
            'totalPokokTabel' => 1500000,
            'totalDendaTabel' => 0,
            'totalTotalTabel' => 1500000,
            'totalPengeluaranTabel' => 0,
        ]);

        $view->assertSee('Laporan Keuangan & Penagihan', false);
        $view->assertSee('Rp 15.000.000');
        $view->assertSee('INV-001');
    }

    #[Test]
    public function it_renders_laporan_pengeluaran_pdf_view_without_errors()
    {
        $view = $this->view('pdf.laporan-pengeluaran', [
            'appName' => 'Asri Boarding House',
            'filterKategori' => 'Semua',
            'filterTanggal' => 'Semua Waktu',
            'totalPengeluaran' => 250000,
            'pengeluaran' => collect([
                (object)[
                    'tanggal_pengeluaran' => now(),
                    'nama_pengeluaran' => 'Beli Sapu',
                    'kategori' => 'operasional',
                    'nominal' => 250000,
                    'keterangan' => 'Pembersihan area kost'
                ]
            ]),
        ]);

        $view->assertSee('Laporan Pengeluaran Operasional');
        $view->assertSee('Beli Sapu');
        $view->assertSee('Rp 250.000');
    }

    #[Test]
    public function it_renders_laporan_penyewa_pdf_view_without_errors()
    {
        $view = $this->view('pdf.laporan-penyewa', [
            'appName' => 'Asri Boarding House',
            'totalDeposit' => 1000000,
            'dendaPersen' => 5,
            'penyewaList' => collect([
                (object)[
                    'user' => (object)['nama' => 'Budi Santoso', 'email' => 'budi@example.com', 'no_hp' => '08123456789'],
                    'kamar' => (object)['nomor_kamar' => '101', 'tipe' => 'Deluxe'],
                    'nik' => '3171000000000000',
                    'tipe_sewa' => 'bulanan',
                    'durasi_formatted' => '1 Bulan',
                    'tanggal_masuk' => now(),
                    'status' => 'aktif',
                    'deposit' => 1000000
                ]
            ])
        ]);

        $view->assertSee('Laporan Data Penyewa');
        $view->assertSee('Budi Santoso');
        $view->assertSee('Kamar 101');
    }

    #[Test]
    public function it_handles_invalid_month_period_gracefully_in_nota_pembayaran()
    {
        $view = $this->view('pdf.nota-pembayaran', [
            'appName' => 'Asri Boarding House',
            'contactAddress' => 'Jl. Merdeka No. 10',
            'contactWhatsapp' => '08123456789',
            'penyewa' => null,
            'pembayaran' => (object)[
                'transaction_id' => 'TRX-999',
                'tanggal_bayar' => null,
                'payment_type' => 'cash',
                'bank' => null,
                'va_number' => null
            ],
            'tagihan' => (object)[
                'periode_bulan' => 99, // Edge case bulan invalid
                'periode_tahun' => 2026,
                'nominal_pokok' => 1000000,
                'nominal_denda' => 50000,
                'nominal_total' => 1050000,
            ],
            'nominalDeposit' => 0
        ]);

        $view->assertSee('TRX-999');
        $view->assertSee('LUNAS');
    }
}
