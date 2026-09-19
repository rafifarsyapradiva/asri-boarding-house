<?php

namespace App\Services;

use App\Models\Tagihan;
use App\Models\Penyewa;
use App\Models\LogNotifikasi;
use App\Models\Reservasi;
use App\Models\Pembayaran;
use App\Models\Keluhan;
use App\Services\FonnteService;
use App\Mail\TagihanBulanMail;
use App\Mail\NotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\Notifications\NotificationTemplateBuilder;

class NotifikasiService
{
    protected NotificationTemplateBuilder $templateBuilder;

    /**
     * Create a new service instance.
     */
    public function __construct(
        protected FonnteService $fonnte,
        ?NotificationTemplateBuilder $templateBuilder = null
    ) {
        $this->templateBuilder = $templateBuilder ?? new NotificationTemplateBuilder();
    }


    /**
     * Kirim notifikasi tagihan bulanan baru ke penyewa (WhatsApp & Email).
     */
    public function kirimNotifikasiTagihan(Tagihan $tagihan, bool $throwOnError = false): void
    {
        // Eager load penyewa, user, dan kamar
        $tagihan->load(['penyewa.user', 'penyewa.kamar']);
        $penyewa = $tagihan->penyewa;

        if (!$penyewa || !$penyewa->user) {
            Log::warning('Gagal mengirim notifikasi tagihan: Data penyewa atau user tidak lengkap.', [
                'tagihan_id' => $tagihan->id,
            ]);
            return;
        }

        $pesan = $this->templateTagihanBaru($tagihan, $penyewa);
        $failures = [];

        // Kirim WhatsApp
        try {
            $this->kirimDanLog(
                $penyewa,
                $tagihan,
                'whatsapp',
                'tagihan_baru',
                $pesan,
                fn($msg) => $this->fonnte->kirimPesan($penyewa->user->no_hp, $msg),
                true
            );
        } catch (\Throwable $e) {
            $failures['whatsapp'] = $e->getMessage();
        }

        // Kirim Email
        try {
            $this->kirimDanLog(
                $penyewa,
                $tagihan,
                'email',
                'tagihan_baru',
                $pesan,
                fn($msg) => Mail::to($penyewa->user->email)->send(new TagihanBulanMail($tagihan)),
                true
            );
        } catch (\Throwable $e) {
            $failures['email'] = $e->getMessage();
        }

        if ($throwOnError && !empty($failures)) {
            throw new \Exception('Gagal mengirim notifikasi tagihan: ' . json_encode($failures));
        }
    }

    /**
     * Kirim notifikasi welcome penyewa baru (WhatsApp + Email).
     */
    public function kirimNotifikasiWelcomePenyewa(Penyewa $penyewa, bool $throwOnError = false): void
    {
        $penyewa->loadMissing(['user', 'kamar']);

        if (!$penyewa->user) {
            Log::warning('Gagal mengirim notifikasi welcome penyewa: Data user tidak lengkap.', [
                'penyewa_id' => $penyewa->id,
            ]);
            return;
        }

        $noHp = $penyewa->user->no_hp;

        // Kirim WhatsApp (hanya jika nomor HP valid)
        if (!empty($noHp) && !str_starts_with($noHp, 'temp_')) {
            $pesan = $this->templateWelcomePenyewa($penyewa);
            $this->kirimDanLog(
                $penyewa,
                null,
                'whatsapp',
                'welcome_penyewa',
                $pesan,
                fn($msg) => $this->fonnte->kirimPesan($noHp, $msg),
                $throwOnError
            );
        } else {
            Log::info('WhatsApp welcome message skipped: temporary or empty phone number.', [
                'penyewa_id' => $penyewa->id,
                'no_hp' => $noHp,
            ]);
        }

        // Kirim Email (selalu dikirim jika email tersedia)
        $email = $penyewa->user->email ?? null;
        if ($email) {
            $viewData = [
                'namaPenyewa'  => $penyewa->user->nama ?? 'Penyewa',
                'nomorKamar'   => $penyewa->kamar?->nomor_kamar ?? '-',
                'hargaSewa'    => $penyewa->kamar ? ('Rp ' . number_format($penyewa->kamar->harga_bulan ?? 0, 0, ',', '.') . '/bulan') : null,
                'urlDashboard' => rtrim(config('app.url'), '/') . '/penyewa/dashboard',
            ];
            try {
                $this->kirimDanLog(
                    $penyewa,
                    null,
                    'email',
                    'welcome_penyewa',
                    'Email selamat datang penyewa baru.',
                    fn($msg) => Mail::to($email)->send(
                        new NotificationMail('Selamat Datang di Asri Boarding House! 🏠', 'emails.welcome-penyewa', $viewData)
                    ),
                    $throwOnError
                );
            } catch (\Throwable $e) {
                Log::error('Gagal mengirim email welcome penyewa: ' . $e->getMessage(), [
                    'penyewa_id' => $penyewa->id,
                ]);
            }
        }
    }

    /**
     * Kirim notifikasi keluhan baru ke WA Admin.
     */
    public function kirimNotifikasiKeluhanDibuat(Keluhan $keluhan, bool $throwOnError = false): void
    {
        $keluhan->loadMissing('penyewa.kamar');
        $penyewa = $keluhan->penyewa;
        if (!$penyewa) {
            Log::warning('Batal kirim notif keluhan baru: data penyewa kosong.');
            return;
        }

        $nomorKamar = $penyewa->kamar?->nomor_kamar ?? '-';
        $appUrl = rtrim(config('app.url'), '/');
        
        $pesan = "🔔 *LAPORAN KELUHAN BARU* 🔔\n\n" .
                 "Halo Admin, terdapat keluhan baru dari penyewa:\n\n" .
                 "🏠 *Kamar:* Kamar {$nomorKamar}\n" .
                 "📁 *Kategori:* {$keluhan->kategori}\n" .
                 "📝 *Judul:* {$keluhan->judul}\n\n" .
                 "Silakan periksa detailnya melalui tautan berikut:\n" .
                 "🔗 {$appUrl}/admin/keluhan/{$keluhan->id}";

        $adminWa = config('reservasi.admin_wa');
        if ($adminWa) {
            $this->kirimDanLog(
                $penyewa,
                null,
                'whatsapp',
                'keluhan_baru',
                $pesan,
                fn($msg) => $this->fonnte->kirimPesan($adminWa, $msg),
                $throwOnError
            );
        }
    }

    /**
     * Kirim notifikasi keluhan ditanggapi ke WA Penyewa.
     */
    public function kirimNotifikasiKeluhanDitanggapi(Keluhan $keluhan, bool $throwOnError = false): void
    {
        $keluhan->loadMissing('penyewa.user');
        $penyewa = $keluhan->penyewa;
        if (!$penyewa || !$penyewa->user) {
            Log::warning('Batal kirim notif keluhan ditanggapi: data penyewa/user kosong.');
            return;
        }

        $namaPenyewa = $penyewa->user->nama ?? 'Penyewa';
        $tanggapanAdmin = $keluhan->tanggapan_admin ?? '-';
        $appUrl = rtrim(config('app.url'), '/');
        
        $pesan = "🛠️ *UPDATE KELUHAN PENYEWA* 🛠️\n\n" .
                 "Halo *{$namaPenyewa}*,\n\n" .
                 "Keluhan Anda mengenai *'{$keluhan->judul}'* saat ini berstatus: *[{$keluhan->status}]*.\n\n" .
                 "💬 *Tanggapan Admin:*\n" .
                 "\"{$tanggapanAdmin}\"\n\n" .
                 "Silakan cek perkembangan keluhan Anda selengkapnya di:\n" .
                 "🔗 {$appUrl}/penyewa/keluhan/{$keluhan->id}\n\n" .
                 "— *Manajemen Asri Boarding House*";

        if ($penyewa->user->no_hp) {
            $this->kirimDanLog(
                $penyewa,
                null,
                'whatsapp',
                'keluhan_ditanggapi',
                $pesan,
                fn($msg) => $this->fonnte->kirimPesan($penyewa->user->no_hp, $msg),
                $throwOnError
            );
        }
    }

    /**
     * Kirim notifikasi transisi selamat datang penyewa aktif ke WA Penyewa & Email.
     */
    public function kirimNotifikasiTransisiPenyewa(Penyewa $penyewa, string $nomorKamar, bool $throwOnError = false): void
    {
        $penyewa->loadMissing(['user', 'kamar']);
        if (!$penyewa->user) {
            Log::warning('Batal kirim notif transisi penyewa: data user kosong.');
            return;
        }

        $pesan = "🏠 *SELAMAT DATANG PENYEWA BARU* 🏠\n\n" .
                 "Halo *{$penyewa->user->nama}*,\n\n" .
                 "Selamat bergabung sebagai penyewa baru di Asri Boarding House!\n" .
                 "🚪 Kamar Anda (*Kamar {$nomorKamar}*) telah resmi *AKTIF*.\n\n" .
                 "Silakan gunakan portal penyewa Anda untuk mempermudah administrasi selama menyewa.\n\n" .
                 "Selamat beristirahat!\n" .
                 "— *Manajemen Asri Boarding House*";

        if ($penyewa->user->no_hp) {
            $this->kirimDanLog(
                $penyewa,
                null,
                'whatsapp',
                'transisi_aktif',
                $pesan,
                fn($msg) => $this->fonnte->kirimPesan($penyewa->user->no_hp, $msg),
                $throwOnError
            );
        }

        // Kirim Email Welcome
        $email = $penyewa->user->email ?? null;
        if ($email) {
            $viewData = [
                'namaPenyewa'  => $penyewa->user->nama ?? 'Penyewa',
                'nomorKamar'   => $nomorKamar,
                'hargaSewa'    => $penyewa->kamar ? ('Rp ' . number_format($penyewa->kamar->harga_bulan ?? 0, 0, ',', '.') . '/bulan') : null,
                'urlDashboard' => rtrim(config('app.url'), '/') . '/penyewa/dashboard',
            ];
            try {
                $this->kirimDanLog(
                    $penyewa,
                    null,
                    'email',
                    'transisi_aktif',
                    'Email selamat datang penyewa baru (transisi aktif).',
                    fn($msg) => Mail::to($email)->send(
                        new \App\Mail\NotificationMail('Selamat Datang di Asri Boarding House! 🏠', 'emails.welcome-penyewa', $viewData)
                    ),
                    $throwOnError
                );
            } catch (\Throwable $e) {
                Log::error('Gagal mengirim email welcome (transisi penyewa): ' . $e->getMessage(), [
                    'penyewa_id' => $penyewa->id,
                ]);
            }
        }
    }

    /**
     * Eksekusi callback pengiriman notifikasi dan simpan log audit.
     */
    protected function kirimDanLog(Penyewa $penyewa, ?Tagihan $tagihan, string $channel, string $event, string $pesan, callable $fn, bool $throwOnError = false): void
    {
        // Check if already sent successfully to prevent duplicate deliveries on job retries
        if ($tagihan) {
            $exists = LogNotifikasi::where('tagihan_id', $tagihan->id)
                ->where('channel', $channel)
                ->where('event', $event)
                ->where('status', 'sukses')
                ->exists();
            if ($exists) {
                Log::info("Notification already sent successfully, skipping to avoid duplicates", [
                    'tagihan_id' => $tagihan->id,
                    'channel' => $channel,
                    'event' => $event
                ]);
                return;
            }
        } elseif ($penyewa) {
            $exists = LogNotifikasi::where('penyewa_id', $penyewa->id)
                ->where('channel', $channel)
                ->where('event', $event)
                ->where('status', 'sukses')
                ->exists();
            if ($exists) {
                Log::info("Welcome/Penyewa notification already sent successfully, skipping to avoid duplicates", [
                    'penyewa_id' => $penyewa->id,
                    'channel' => $channel,
                    'event' => $event
                ]);
                return;
            }
        }

        $errorMsg = null;
        $failed = false;
        try {
            $result = $fn($pesan);
            if ($result === false) {
                $status = 'gagal';
                $errorMsg = 'Callback returned false';
                Log::error("Notif gagal: $event via $channel");
                $failed = true;
            } else {
                $status = 'sukses';
            }
        } catch (\Throwable $e) {
            $status = 'gagal';
            $errorMsg = $e->getMessage();
            Log::error("Notif gagal: $event via $channel. Error: " . $errorMsg);
            
            LogNotifikasi::create([
                'penyewa_id' => $penyewa->id,
                'tagihan_id' => $tagihan?->id,
                'channel'    => $channel,
                'event'      => $event,
                'status'     => $status,
                'pesan'      => $pesan,
                'error_msg'  => $errorMsg,
            ]);

            if ($throwOnError) {
                throw $e;
            }
            return;
        }

        LogNotifikasi::create([
            'penyewa_id' => $penyewa->id,
            'tagihan_id' => $tagihan?->id,
            'channel'    => $channel,
            'event'      => $event,
            'status'     => $status,
            'pesan'      => $pesan,
            'error_msg'  => $errorMsg,
        ]);

        if ($failed && $throwOnError) {
            throw new \Exception("Gagal mengirim notifikasi via $channel: $errorMsg");
        }
    }

    /**
     * Template WhatsApp: Tagihan Baru
     */
    public function templateTagihanBaru(Tagihan $tagihan, Penyewa $penyewa): string
    {
        return $this->templateBuilder->buildTagihanBaru($tagihan, $penyewa);
    }

    /**
     * Template WhatsApp: Pembayaran Berhasil
     */
    public function templatePembayaranBerhasil(Pembayaran $pembayaran): string
    {
        return $this->templateBuilder->buildPembayaranBerhasil($pembayaran);
    }

    /**
     * Template WhatsApp: Reminder Habis Kontrak H-14 & H-7
     */
    public function templateReminderHabisKontrak(Penyewa $penyewa, int $sisaHari): string
    {
        return $this->templateBuilder->buildReminderHabisKontrak($penyewa, $sisaHari);
    }

    public function templateReminderJatuhTempo(Tagihan $tagihan, Penyewa $penyewa): string
    {
        return $this->templateBuilder->buildReminderJatuhTempo($tagihan, $penyewa);
    }

    /**
     * Template WhatsApp: Notifikasi Wali
     */
    public function templateNotifikasiWali(Tagihan $tagihan, Penyewa $penyewa): string
    {
        return $this->templateBuilder->buildNotifikasiWali($tagihan, $penyewa);
    }

    /**
     * Template WhatsApp: Denda
     */
    public function templateDenda(Tagihan $tagihan, Penyewa $penyewa): string
    {
        return $this->templateBuilder->buildDenda($tagihan, $penyewa);
    }

    /**
     * Template WhatsApp: Welcome Email / Message
     */
    public function templateWelcomeEmail(Penyewa $penyewa): string
    {
        return "Selamat datang di Asri Boarding House! Akun penyewa Anda telah berhasil dibuat.";
    }

    /**
     * Template WhatsApp: Welcome Penyewa Baru
     */
    public function templateWelcomePenyewa(Penyewa $penyewa): string
    {
        return $this->templateBuilder->buildWelcomePenyewa($penyewa);
    }

    /**
     * Template WhatsApp: Reservasi
     */
    public function templateReservasi(Penyewa $penyewa): string
    {
        return "Terima kasih, reservasi Anda sedang diproses oleh admin Asri Boarding House.";
    }

    /**
     * Template WhatsApp: Reservasi Dibuat (BARU v2.1)
     */
    public function templateReservasiDibuat(Reservasi $reservasi): string
    {
        return $this->templateBuilder->buildReservasiDibuat($reservasi);
    }

    /**
     * Template WhatsApp: Reservasi Dibayar (BARU v2.1)
     */
    public function templateReservasiDibayar(Reservasi $reservasi): string
    {
        return $this->templateBuilder->buildReservasiDibayar($reservasi);
    }

    /**
     * Template WhatsApp: Reservasi Dikonfirmasi (BARU v2.1)
     */
    public function templateReservasiDikonfirmasi(Reservasi $reservasi): string
    {
        return $this->templateBuilder->buildReservasiDikonfirmasi($reservasi);
    }

    /**
     * Kirim notifikasi reservasi baru ke nomor HP Admin.
     */
    public function kirimNotifikasiReservasiBaru(Reservasi $reservasi): void
    {
        try {
            $reservasi->loadMissing(['user', 'kamar']);
            $adminWa = config('reservasi.admin_wa');
            if ($adminWa) {
                $message = $this->templateReservasiDibuat($reservasi);
                $this->fonnte->kirimPesan($adminWa, $message);
            }
        } catch (\Throwable $e) {
            Log::error("Gagal mengirim notifikasi reservasi baru ke admin via Fonnte: " . $e->getMessage(), [
                'reservasi_id' => $reservasi->id,
            ]);
        }
    }

    /**
     * Kirim notifikasi pembayaran reservasi sukses ke calon penyewa.
     */
    public function kirimNotifikasiPembayaranReservasi(Reservasi $reservasi): void
    {
        try {
            $user = $reservasi->user;
            if ($user && $user->no_hp) {
                $message = $this->templateReservasiDibayar($reservasi);
                $this->fonnte->kirimPesan($user->no_hp, $message);
            }
        } catch (\Throwable $e) {
            Log::error("Gagal mengirim notifikasi pembayaran reservasi ke penyewa via Fonnte: " . $e->getMessage(), [
                'reservasi_id' => $reservasi->id,
            ]);
        }
    }

    /**
     * Kirim notifikasi pembayaran sukses (WhatsApp & Email).
     */
    public function kirimNotifikasiPembayaran(Pembayaran $pembayaran, bool $throwOnError = false): void
    {
        $pembayaran->loadMissing(['tagihan.penyewa.user']);
        $penyewa = $pembayaran->tagihan?->penyewa;

        if (!$penyewa || !$penyewa->user) {
            Log::warning('Gagal mengirim notifikasi pembayaran: Data penyewa atau user tidak lengkap.', [
                'pembayaran_id' => $pembayaran->id,
            ]);
            return;
        }

        $message = $this->templatePembayaranBerhasil($pembayaran);
        $failures = [];

        // Kirim WhatsApp
        try {
            $this->kirimDanLog(
                $penyewa,
                $pembayaran->tagihan,
                'whatsapp',
                'pembayaran_sukses',
                $message,
                fn($msg) => $this->fonnte->kirimPesan($penyewa->user->no_hp, $msg),
                true
            );
        } catch (\Throwable $e) {
            $failures['whatsapp'] = $e->getMessage();
        }

        // Kirim Email
        $viewData = [
            'namaPenyewa' => $penyewa->user->nama ?? 'Penyewa',
            'periodeSewa' => $pembayaran->tagihan ? $pembayaran->tagihan->periode_bulan . '/' . $pembayaran->tagihan->periode_tahun : '-/-',
            'metodePembayaran' => $pembayaran->payment_type ?? '-',
            'nominalBayarFormatted' => 'Rp ' . number_format($pembayaran->nominal ?? 0, 0, ',', '.'),
            'urlNota' => rtrim(config('app.url'), '/') . '/penyewa/nota/' . $pembayaran->id . '/download',
        ];

        try {
            $this->kirimDanLog(
                $penyewa,
                $pembayaran->tagihan,
                'email',
                'pembayaran_sukses',
                $message,
                fn($msg) => Mail::to($penyewa->user->email)->send(
                    new NotificationMail('Konfirmasi Pembayaran Berhasil - Asri Boarding House', 'emails.pembayaran-sukses', $viewData)
                ),
                true
            );
        } catch (\Throwable $e) {
            $failures['email'] = $e->getMessage();
        }

        if ($throwOnError && !empty($failures)) {
            throw new \Exception('Gagal mengirim notifikasi pembayaran: ' . json_encode($failures));
        }
    }

    /**
     * Kirim reminder jatuh tempo / denda ke penyewa via WhatsApp.
     */
    public function kirimReminderJatuhTempo(Tagihan $tagihan, bool $throwOnError = false): void
    {
        $tagihan->loadMissing(['penyewa.user', 'penyewa.kamar']);
        $penyewa = $tagihan->penyewa;

        if (!$penyewa || !$penyewa->user) {
            Log::warning('Gagal mengirim reminder: Data penyewa atau user tidak lengkap.', [
                'tagihan_id' => $tagihan->id,
            ]);
            return;
        }

        // Tentukan template pesan berdasarkan status denda (nominal_denda > 0)
        if ($tagihan->nominal_denda > 0) {
            $message = $this->templateDenda($tagihan, $penyewa);
            $event = 'denda_dikenakan';
        } else {
            $message = $this->templateReminderJatuhTempo($tagihan, $penyewa);
            $event = 'reminder_penyewa';
        }

        $failures = [];

        // Kirim WhatsApp
        try {
            $this->kirimDanLog(
                $penyewa,
                $tagihan,
                'whatsapp',
                $event,
                $message,
                fn($msg) => $this->fonnte->kirimPesan($penyewa->user->no_hp, $msg),
                true
            );
        } catch (\Throwable $e) {
            $failures['whatsapp'] = $e->getMessage();
        }

        // Eskalasi Wali: Jika kolom bulan_keterlambatan > 1, kirim juga ke nomor wali
        if ($tagihan->bulan_keterlambatan > 1 && !empty($penyewa->no_wali)) {
            try {
                // Check if already sent under 'notifikasi_wali' or 'notifikasi_wali_eskalasi' to avoid duplicates
                $alreadySentWali = LogNotifikasi::where('tagihan_id', $tagihan->id)
                    ->where('channel', 'whatsapp')
                    ->whereIn('event', ['notifikasi_wali', 'notifikasi_wali_eskalasi'])
                    ->where('status', 'sukses')
                    ->exists();

                if (!$alreadySentWali) {
                    $pesanWali = $this->templateNotifikasiWali($tagihan, $penyewa);
                    $this->kirimDanLog(
                        $penyewa,
                        $tagihan,
                        'whatsapp',
                        'notifikasi_wali_eskalasi',
                        $pesanWali,
                        fn($msg) => $this->fonnte->kirimPesan($penyewa->no_wali, $msg),
                        false
                    );
                } else {
                    Log::info("Eskalasi wali skipped karena notifikasi wali sudah pernah berhasil dikirim untuk tagihan ini.", [
                        'tagihan_id' => $tagihan->id
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error("Gagal mengirim eskalasi wali via Fonnte: " . $e->getMessage());
            }
        }

        $nomorKamar = $penyewa->kamar?->nomor_kamar ?? '-';
        $nominalPokok = $tagihan->nominal_pokok ?? 0;
        $nominalDenda = $tagihan->nominal_denda ?? 0;
        $nominalTotal = $tagihan->nominal_total ?? 0;

        // Kirim Email
        $viewData = [
            'type' => $event === 'denda_dikenakan' ? 'denda' : 'reminder',
            'namaPenyewa' => $penyewa->user->nama ?? 'Penyewa',
            'nomorKamar' => $nomorKamar,
            'periodeSewa' => $tagihan->periode_bulan . '/' . $tagihan->periode_tahun,
            'nominalPokokFormatted' => 'Rp ' . number_format($nominalPokok, 0, ',', '.'),
            'nominalDendaFormatted' => 'Rp ' . number_format($nominalDenda, 0, ',', '.'),
            'nominalTotalFormatted' => 'Rp ' . number_format($nominalTotal, 0, ',', '.'),
            'urlTagihan' => rtrim(config('app.url'), '/') . '/penyewa/tagihan',
        ];

        $subject = $event === 'denda_dikenakan' 
            ? "[ALERTI] Denda Keterlambatan Diberlakukan - Tagihan Kost Kamar {$nomorKamar}" 
            : "[PENTING] Pengingat Batas Jatuh Tempo Tagihan Kost Kamar {$nomorKamar}";

        try {
            $this->kirimDanLog(
                $penyewa,
                $tagihan,
                'email',
                $event,
                $message,
                fn($msg) => Mail::to($penyewa->user->email)->send(
                    new NotificationMail($subject, 'emails.tagihan-reminder', $viewData)
                ),
                true
            );
        } catch (\Throwable $e) {
            $failures['email'] = $e->getMessage();
        }

        if ($throwOnError && !empty($failures)) {
            throw new \Exception('Gagal mengirim reminder jatuh tempo: ' . json_encode($failures));
        }
    }

    /**
     * Kirim notifikasi wali (WhatsApp).
     */
    public function kirimNotifikasiWali(Tagihan $tagihan, bool $throwOnError = false): void
    {
        $tagihan->loadMissing(['penyewa.user', 'penyewa.kamar']);
        $penyewa = $tagihan->penyewa;

        if (!$penyewa || empty($penyewa->no_wali)) {
            Log::warning('Gagal mengirim notifikasi wali: Data wali tidak lengkap.', [
                'tagihan_id' => $tagihan->id,
            ]);
            return;
        }

        $message = $this->templateNotifikasiWali($tagihan, $penyewa);

        $this->kirimDanLog(
            $penyewa,
            $tagihan,
            'whatsapp',
            'notifikasi_wali',
            $message,
            fn($msg) => $this->fonnte->kirimPesan($penyewa->no_wali, $msg),
            $throwOnError
        );
    }

    /**
     * Kirim notifikasi status reservasi ke calon penyewa.
     */
    public function kirimNotifikasiUserReservasi(Reservasi $reservasi, string $type): void
    {
        try {
            $user = $reservasi->user;
            if (!$user || !$user->no_hp) {
                return;
            }

            $message = $type === 'dikonfirmasi' 
                ? $this->templateReservasiDikonfirmasi($reservasi) 
                : $this->templateReservasiDibayar($reservasi);

            $this->fonnte->kirimPesan($user->no_hp, $message);
        } catch (\Throwable $e) {
            Log::error("Gagal mengirim notifikasi status reservasi ke penyewa via Fonnte: " . $e->getMessage(), [
                'reservasi_id' => $reservasi->id,
                'type' => $type,
            ]);
        }
    }

    /**
     * Kirim notifikasi kustom/manual (WhatsApp & Email) dan throw exception jika gagal (untuk queue job).
     */
    public function kirimNotifikasiKustomDirect(Penyewa $penyewa, string $channel, string $pesan, string $subject = 'Pengumuman Asri Boarding House'): void
    {
        $penyewa->loadMissing('user');
        if (!$penyewa->user) {
            throw new \Exception('Data user untuk penyewa tidak lengkap.');
        }

        if ($channel === 'whatsapp') {
            $this->kirimDanLog(
                $penyewa,
                null,
                'whatsapp',
                'broadcast_admin',
                $pesan,
                fn($msg) => $this->fonnte->kirimPesan($penyewa->user->no_hp, $msg),
                true // throwOnError
            );
        } elseif ($channel === 'email') {
            $viewData = [
                'namaPenyewa' => $penyewa->user->nama ?? 'Penyewa',
                'pesan' => $pesan,
                'subject' => $subject,
                'urlDashboard' => rtrim(config('app.url'), '/') . '/login',
            ];
            $this->kirimDanLog(
                $penyewa,
                null,
                'email',
                'broadcast_admin',
                $pesan,
                fn($msg) => Mail::to($penyewa->user->email)->send(
                    new NotificationMail($subject, 'emails.broadcast-kustom', $viewData)
                ),
                true // throwOnError
            );
        } else {
            throw new \Exception("Channel notifikasi '{$channel}' tidak valid.");
        }
    }

    /**
     * Kirim notifikasi kustom/manual (WhatsApp & Email) dari admin panel.
     */
    public function kirimNotifikasiKustom(Penyewa $penyewa, string $channel, string $pesan, string $subject = 'Pengumuman Asri Boarding House'): bool
    {
        $penyewa->loadMissing('user');
        if (!$penyewa->user) {
            return false;
        }

        if ($channel === 'whatsapp') {
            return $this->kirimDanLogKustom($penyewa, 'whatsapp', 'broadcast_admin', $pesan, function() use ($penyewa, $pesan) {
                return $this->fonnte->kirimPesan($penyewa->user->no_hp, $pesan);
            });
        }

        if ($channel === 'email') {
            $viewData = [
                'namaPenyewa' => $penyewa->user->nama ?? 'Penyewa',
                'pesan' => $pesan,
                'subject' => $subject,
                'urlDashboard' => rtrim(config('app.url'), '/') . '/login',
            ];
            return $this->kirimDanLogKustom($penyewa, 'email', 'broadcast_admin', $pesan, function() use ($penyewa, $subject, $viewData) {
                return Mail::to($penyewa->user->email)->send(
                    new NotificationMail($subject, 'emails.broadcast-kustom', $viewData)
                );
            });
        }

        return false;
    }

    /**
     * Helper untuk eksekusi callback pengiriman kustom dan rekam log audit.
     */
    protected function kirimDanLogKustom(Penyewa $penyewa, string $channel, string $event, string $pesan, callable $fn): bool
    {
        $errorMsg = null;
        $status = 'sukses';
        try {
            $result = $fn();
            if ($result === false) {
                $status = 'gagal';
                $errorMsg = 'Callback returned false';
            }
        } catch (\Throwable $e) {
            $status = 'gagal';
            $errorMsg = $e->getMessage();
        }

        LogNotifikasi::create([
            'penyewa_id' => $penyewa->id,
            'tagihan_id' => null,
            'channel'    => $channel,
            'event'      => $event,
            'status'     => $status,
            'pesan'      => $pesan,
            'error_msg'  => $errorMsg,
        ]);

        return $status === 'sukses';
    }

    /**
     * Kirim ulang notifikasi yang gagal dan perbarui status log aslinya.
     */
    public function kirimUlangNotifikasi(LogNotifikasi $log, string $pesan): bool
    {
        $penyewa = $log->penyewa;
        if (!$penyewa || !$penyewa->user) {
            return false;
        }

        $errorMsg = null;
        $status = 'sukses';
        try {
            if ($log->channel === 'whatsapp') {
                $result = $this->fonnte->kirimPesan($penyewa->user->no_hp, $pesan);
                if ($result === false) {
                    $status = 'gagal';
                    $errorMsg = 'Callback returned false';
                }
            } elseif ($log->channel === 'email') {
                $subject = $log->event === 'pembayaran_sukses'
                    ? 'Konfirmasi Pembayaran Berhasil - Asri Boarding House'
                    : ($log->event === 'denda_dikenakan'
                        ? 'Peringatan Denda Keterlambatan Tagihan - Asri Boarding House'
                        : ($log->event === 'reminder_penyewa'
                            ? 'Peringatan Jatuh Tempo Tagihan - Asri Boarding House'
                            : 'Pengumuman Asri Boarding House'));

                $viewData = [
                    'namaPenyewa' => $penyewa->user->nama ?? 'Penyewa',
                    'pesan' => $pesan,
                    'subject' => $subject,
                    'urlDashboard' => rtrim(config('app.url'), '/') . '/login',
                ];

                Mail::to($penyewa->user->email)->send(
                    new NotificationMail($subject, 'emails.broadcast-kustom', $viewData)
                );
            } else {
                $status = 'gagal';
                $errorMsg = 'Unknown channel';
            }
        } catch (\Throwable $e) {
            $status = 'gagal';
            $errorMsg = $e->getMessage();
        }

        $log->update([
            'status' => $status,
            'pesan' => $pesan,
            'error_msg' => $errorMsg,
        ]);

        return $status === 'sukses';
    }

    /**
     * Eksekusi pencarian penyewa yang kontraknya berakhir dalam 7 atau 14 hari
     * dan kirim notifikasi WhatsApp serta Email (Idempotent).
     */
    public function prosesReminderHabisKontrak(): void
    {
        $targetDays = [14, 7];

        foreach ($targetDays as $days) {
            $targetDate = \Illuminate\Support\Carbon::today()->addDays($days)->toDateString();

            Penyewa::where('status', 'aktif')
                ->whereNotNull('tanggal_keluar_seharusnya')
                ->whereDate('tanggal_keluar_seharusnya', $targetDate)
                ->with(['user', 'kamar'])
                ->chunkById(100, function ($penyewaList) use ($days) {
                    foreach ($penyewaList as $penyewa) {
                        try {
                            $event = "reminder_kontrak_{$days}";

                            // Cek jika sudah pernah dikirim agar tidak spam (Idempotency)
                            $sudahKirim = LogNotifikasi::where('penyewa_id', $penyewa->id)
                                ->where('event', $event)
                                ->where('status', 'sukses')
                                ->exists();

                            if ($sudahKirim) {
                                continue;
                            }

                            $pesan = $this->templateReminderHabisKontrak($penyewa, $days);

                            // 1. Kirim WA
                            if ($penyewa->user && $penyewa->user->no_hp) {
                                $this->kirimDanLog(
                                    $penyewa,
                                    null,
                                    'whatsapp',
                                    $event,
                                    $pesan,
                                    fn($msg) => $this->fonnte->kirimPesan($penyewa->user->no_hp, $msg),
                                    false
                                );
                            }

                            // 2. Kirim Email
                            if ($penyewa->user && $penyewa->user->email) {
                                $subject = "[PENGINGAT] Masa Kontrak Kost Kamar " . ($penyewa->kamar?->nomor_kamar ?? '-') . " Berakhir {$days} Hari Lagi";
                                $viewData = [
                                    'namaPenyewa' => $penyewa->user->nama ?? 'Penyewa',
                                    'pesan' => "Masa kontrak kost Anda akan berakhir dalam {$days} hari lagi pada " . ($penyewa->tanggal_keluar_seharusnya ? $penyewa->tanggal_keluar_seharusnya->format('d-m-Y') : '-') . ". Harap lakukan konfirmasi perpanjangan atau persiapan checkout.",
                                    'subject' => $subject,
                                    'urlDashboard' => rtrim(config('app.url'), '/') . '/login',
                                ];

                                $this->kirimDanLog(
                                    $penyewa,
                                    null,
                                    'email',
                                    $event,
                                    $pesan,
                                    fn($msg) => \Illuminate\Support\Facades\Mail::to($penyewa->user->email)->send(
                                        new \App\Mail\NotificationMail($subject, 'emails.broadcast-kustom', $viewData)
                                    ),
                                    false
                                );
                            }
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error("Gagal mengirim reminder habis kontrak H-{$days} untuk Penyewa ID {$penyewa->id}: " . $e->getMessage());
                        }
                    }
                });
        }
    }

    /**
     * Format pesan log notifikasi jika kolom pesan masih kosong.
     */
    public function formatLogMessage(LogNotifikasi $log, Penyewa $penyewa): string
    {
        $tagihan = $log->tagihan;
        if ($tagihan) {
            if ($log->event === 'tagihan_baru') {
                return $this->templateTagihanBaru($tagihan, $penyewa);
            } elseif ($log->event === 'denda_dikenakan') {
                return $this->templateDenda($tagihan, $penyewa);
            } elseif ($log->event === 'reminder_penyewa') {
                return $this->templateReminderJatuhTempo($tagihan, $penyewa);
            } elseif ($log->event === 'notifikasi_wali') {
                return $this->templateNotifikasiWali($tagihan, $penyewa);
            } elseif ($log->event === 'pembayaran_sukses') {
                $pembayaran = $tagihan->pembayaran->sortByDesc('created_at')->first();
                if ($pembayaran) {
                    return $this->templatePembayaranBerhasil($pembayaran);
                }
            }
        } elseif ($log->event === 'welcome_penyewa') {
            return $this->templateWelcomePenyewa($penyewa);
        }

        return $log->pesan ?? '';
    }
}


