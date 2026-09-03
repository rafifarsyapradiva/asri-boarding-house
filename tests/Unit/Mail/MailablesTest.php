<?php

namespace Tests\Unit\Mail;

use App\Mail\NotificationMail;
use App\Mail\TagihanBulanMail;
use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\Tagihan;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class MailablesTest extends TestCase
{
    public function test_tagihan_bulan_mail_envelope_and_content_render_correctly()
    {
        // Arrange
        $user = new User(['nama' => 'Budi Santoso']);
        $kamar = new Kamar(['nomor_kamar' => 'A-101']);
        $penyewa = new Penyewa();
        $penyewa->setRelation('user', $user);
        $penyewa->setRelation('kamar', $kamar);

        $tagihan = new Tagihan([
            'order_id' => 'INV-2026-001',
            'periode_bulan' => 7,
            'periode_tahun' => 2026,
            'nominal_pokok' => 1500000,
            'nominal_denda' => 50000,
            'nominal_total' => 1650000,
            'tanggal_jatuh_tempo' => Carbon::create(2026, 8, 5),
        ]);
        $tagihan->setRelation('penyewa', $penyewa);

        // Act
        $mailable = new TagihanBulanMail($tagihan);
        $envelope = $mailable->envelope();
        $content = $mailable->content();

        // Assert
        $this->assertStringContainsString('INV-2026-001', $envelope->subject);
        $this->assertEquals('emails.tagihan-baru', $content->view);
        $this->assertEquals('Budi Santoso', $content->with['namaPenyewa']);
        $this->assertEquals('A-101', $content->with['nomorKamar']);
        $this->assertEquals('Rp 1.500.000', $content->with['nominalPokokFormatted']);
        $this->assertEquals('Rp 50.000', $content->with['nominalDendaFormatted']);
        $this->assertEquals('Rp 100.000', $content->with['nominalDepositFormatted']); // 1.65m - 1.5m - 50k
        $this->assertEquals('05 Aug 2026', $content->with['tanggalJatuhTempoFormatted']);
        $this->assertEquals($tagihan, $mailable->getTagihan());
    }

    public function test_tagihan_bulan_mail_handles_null_relations_and_dates_gracefully()
    {
        // Arrange
        $tagihan = new Tagihan([
            'order_id' => null,
            'periode_bulan' => null,
            'periode_tahun' => null,
            'nominal_pokok' => null,
            'nominal_denda' => null,
            'nominal_total' => null,
            'tanggal_jatuh_tempo' => null,
        ]);

        // Act
        $mailable = new TagihanBulanMail($tagihan);
        $envelope = $mailable->envelope();
        $content = $mailable->content();

        // Assert
        $this->assertStringContainsString('-', $envelope->subject);
        $this->assertEquals('Penyewa', $content->with['namaPenyewa']);
        $this->assertEquals('-', $content->with['nomorKamar']);
        $this->assertEquals('Rp 0', $content->with['nominalPokokFormatted']);
        $this->assertEquals('-', $content->with['tanggalJatuhTempoFormatted']);
    }

    public function test_tagihan_bulan_mail_prevents_negative_deposit_when_total_is_less_than_sum()
    {
        // Arrange: Kasus dimana nominal_total lebih kecil dari (pokok + denda)
        $tagihan = new Tagihan([
            'order_id' => 'INV-2026-ERR',
            'nominal_pokok' => 1000000,
            'nominal_denda' => 200000,
            'nominal_total' => 1100000, // Total < Pokok + Denda (Harusnya deposit 0, bukan negative -100.000)
            'tanggal_jatuh_tempo' => '2026-12-31',
        ]);

        // Act
        $mailable = new TagihanBulanMail($tagihan);
        $content = $mailable->content();

        // Assert
        $this->assertEquals(0.0, $content->with['nominalDeposit']);
        $this->assertEquals('Rp 0', $content->with['nominalDepositFormatted']);
        $this->assertEquals('31 Dec 2026', $content->with['tanggalJatuhTempoFormatted']);
    }

    public function test_notification_mail_binds_subject_view_and_data_correctly()
    {
        // Arrange
        $subject = 'Notifikasi Pembayaran Berhasil';
        $viewName = 'emails.pembayaran-sukses';
        $viewData = ['nama' => 'Ahmad', 'total' => 500000];

        // Act
        $mailable = new NotificationMail($subject, $viewName, $viewData);

        // Assert
        $this->assertEquals($subject, $mailable->envelope()->subject);
        $this->assertEquals($viewName, $mailable->content()->view);
        $this->assertEquals($viewData, $mailable->content()->with);
        $this->assertEquals($subject, $mailable->getMailSubject());
        $this->assertEquals($viewName, $mailable->getViewName());
        $this->assertEquals($viewData, $mailable->getViewData());
    }
}

