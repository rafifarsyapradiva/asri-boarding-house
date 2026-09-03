<?php

namespace Tests\Feature\Mail;

use App\Mail\NotificationMail;
use App\Mail\TagihanBulanMail;
use App\Models\Kamar;
use App\Models\Penyewa;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmailTemplateTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function tagihan_baru_mail_renders_correctly_with_full_data(): void
    {
        $user = User::create([
            'nama' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => bcrypt('password'),
            'role' => 'penyewa',
        ]);

        $kamar = Kamar::create([
            'nomor_kamar' => '102',
            'tipe' => 'Standard',
            'harga_bulan' => 1500000,
            'status' => 'terisi',
            'luas_m2' => 16,
            'lantai' => 1,
            'deskripsi' => 'Kamar nyaman',
        ]);

        $penyewa = Penyewa::create([
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'nik' => '3301234567890001',
            'no_hp' => '08123456789',
            'no_wali' => '08987654321',
            'nama_wali' => 'Wali Budi',
            'pekerjaan' => 'Swasta',
            'tanggal_masuk' => now(),
            'status' => 'aktif',
        ]);

        $tagihan = Tagihan::create([
            'penyewa_id' => $penyewa->id,
            'order_id' => 'INV-2026-001',
            'nominal_pokok' => 1500000,
            'nominal_denda' => 0,
            'nominal_total' => 1500000,
            'periode_bulan' => 8,
            'periode_tahun' => 2026,
            'tanggal_tagihan' => now(),
            'tanggal_jatuh_tempo' => now()->addDays(5),
            'status' => 'pending',
        ]);

        $mailable = new TagihanBulanMail($tagihan);

        $mailable->assertSeeInHtml('Halo, Budi Santoso');
        $mailable->assertSeeInHtml('No. 102');
        $mailable->assertSeeInHtml('#INV-2026-001');
        $mailable->assertSeeInHtml('Rp 1.500.000');
    }

    #[Test]
    public function tagihan_reminder_mail_defaults_safely_to_reminder_mode(): void
    {
        $mailable = new NotificationMail(
            mailSubject: 'Pengingat Tagihan',
            viewName: 'emails.tagihan-reminder',
            viewData: [
                'namaPenyewa' => 'Siti',
                'nomorKamar' => 'A-01',
                'periodeSewa' => '08/2026',
                'nominalPokokFormatted' => 'Rp 1.000.000',
                'urlTagihan' => 'https://example.com/pay',
                // type disengaja tidak diisi untuk menguji fallback
            ]
        );

        $html = $mailable->render();

        $this->assertStringContainsString('PENGINGAT MASA KERINGANAN', $html);
        $this->assertStringNotContainsString('DENDA KETERLAMBATAN DIBERLAKUKAN', $html);
    }

    #[Test]
    public function tagihan_reminder_mail_renders_denda_mode_when_type_is_denda(): void
    {
        $mailable = new NotificationMail(
            mailSubject: 'Penegakan Denda',
            viewName: 'emails.tagihan-reminder',
            viewData: [
                'type' => 'denda',
                'namaPenyewa' => 'Siti',
                'nomorKamar' => 'A-01',
                'periodeSewa' => '08/2026',
                'nominalPokokFormatted' => 'Rp 1.000.000',
                'nominalDendaFormatted' => 'Rp 50.000',
                'nominalTotalFormatted' => 'Rp 1.050.000',
                'urlTagihan' => 'https://example.com/pay',
            ]
        );

        $html = $mailable->render();

        $this->assertStringContainsString('DENDA KETERLAMBATAN DIBERLAKUKAN', $html);
        $this->assertStringContainsString('Denda Keterlambatan (5%)', $html);
    }

    #[Test]
    public function email_views_render_safely_with_empty_data(): void
    {
        $views = [
            'emails.broadcast-kustom',
            'emails.pembayaran-sukses',
            'emails.reset-password',
            'emails.tagihan-baru',
            'emails.tagihan-reminder',
        ];

        foreach ($views as $view) {
            $html = view($view)->render();
            $this->assertNotEmpty($html);
        }
    }
}

