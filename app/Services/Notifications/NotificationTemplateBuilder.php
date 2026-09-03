<?php

namespace App\Services\Notifications;

use App\Models\Tagihan;
use App\Models\Penyewa;
use App\Models\Pembayaran;
use App\Models\Reservasi;
use App\Models\Keluhan;

class NotificationTemplateBuilder
{
    /**
     * Template WhatsApp: Tagihan Baru
     */
    public function buildTagihanBaru(Tagihan $tagihan, Penyewa $penyewa): string
    {
        $nama = $penyewa->user?->nama ?? 'Penyewa';
        $periode = $tagihan->periode_bulan . '/' . $tagihan->periode_tahun;
        $nomorKamar = $penyewa->kamar?->nomor_kamar ?? '-';
        $nominalTotal = number_format($tagihan->nominal_total ?? 0, 0, ',', '.');

        $tanggalJatuhTempo = '-';
        if ($tagihan->tanggal_jatuh_tempo instanceof \Carbon\Carbon) {
            $tanggalJatuhTempo = $tagihan->tanggal_jatuh_tempo->format('Y-m-d');
        } elseif (is_string($tagihan->tanggal_jatuh_tempo)) {
            $tanggalJatuhTempo = date('Y-m-d', strtotime($tagihan->tanggal_jatuh_tempo));
        }

        $appUrl = rtrim(config('app.url'), '/');

        return "🔔 *TAGIHAN BARU DITERBITKAN* 🔔\n\n" .
               "Halo *{$nama}*,\n\n" .
               "Tagihan sewa Anda untuk periode bulan *{$periode}* telah diterbitkan. Berikut rinciannya:\n\n" .
               "🏠 *Kamar:* Kamar {$nomorKamar}\n" .
               "💰 *Total Tagihan:* Rp {$nominalTotal}\n" .
               "📅 *Jatuh Tempo:* {$tanggalJatuhTempo}\n\n" .
               "Silakan lakukan pembayaran sebelum jatuh tempo melalui portal penyewa berikut:\n" .
               "🔗 {$appUrl}/penyewa/tagihan\n\n" .
               "Terima kasih atas kerja samanya.\n" .
               "— *Manajemen Asri Boarding House*";
    }

    /**
     * Template WhatsApp: Pembayaran Berhasil
     */
    public function buildPembayaranBerhasil(Pembayaran $pembayaran): string
    {
        $pembayaran->loadMissing(['tagihan.penyewa.user']);
        $periode = $pembayaran->tagihan ? $pembayaran->tagihan->periode_bulan . '/' . $pembayaran->tagihan->periode_tahun : '-/-';
        $paymentType = $pembayaran->payment_type ?? '-';
        $nominal = number_format($pembayaran->nominal ?? 0, 0, ',', '.');
        $linkNota = rtrim(config('app.url'), '/') . '/penyewa/nota/' . $pembayaran->id . '/download';

        return "✅ *PEMBAYARAN DIKONFIRMASI* ✅\n\n" .
               "Halo, pembayaran sewa Anda untuk periode *{$periode}* telah diterima dan dikonfirmasi.\n\n" .
               "💰 *Jumlah:* Rp {$nominal}\n" .
               "💳 *Metode:* {$paymentType}\n\n" .
               "Nota resmi (PDF) dapat diunduh pada tautan berikut:\n" .
               "🔗 {$linkNota}\n\n" .
               "Terima kasih atas kepercayaan Anda.\n" .
               "— *Manajemen Asri Boarding House*";
    }

    /**
     * Template WhatsApp: Reminder Habis Kontrak
     */
    public function buildReminderHabisKontrak(Penyewa $penyewa, int $sisaHari): string
    {
        $nama = $penyewa->user?->nama ?? 'Penyewa';
        $nomorKamar = $penyewa->kamar?->nomor_kamar ?? '-';
        $tanggalKeluar = $penyewa->tanggal_keluar_seharusnya ? $penyewa->tanggal_keluar_seharusnya->format('d-m-Y') : '-';
        $appUrl = rtrim(config('app.url'), '/');

        return "⚠️ *PENGINGAT MASA KONTRAK SEGERA BERAKHIR* ⚠️\n\n" .
               "Halo *{$nama}*,\n\n" .
               "Kami menginformasikan bahwa masa sewa Anda untuk *Kamar {$nomorKamar}* akan berakhir dalam waktu *{$sisaHari} hari* lagi, tepatnya pada tanggal *{$tanggalKeluar}*.\n\n" .
               "Mohon segera lakukan konfirmasi ke admin apakah Anda ingin:\n" .
               "1. *Memperpanjang sewa kost* untuk bulan berikutnya.\n" .
               "2. *Mengakhiri sewa kost* (Checkout) sesuai tanggal berakhir sewa.\n\n" .
               "Silakan hubungi WhatsApp Admin Kost atau akses portal penyewa untuk informasi lebih lanjut:\n" .
               "🔗 {$appUrl}/penyewa/dashboard\n\n" .
               "Terima kasih atas perhatian Anda.\n" .
               "— *Manajemen Asri Boarding House*";
    }

    /**
     * Template WhatsApp: Reminder Jatuh Tempo
     */
    public function buildReminderJatuhTempo(Tagihan $tagihan, Penyewa $penyewa): string
    {
        $nama_penyewa = $penyewa->user?->nama ?? 'Penyewa';
        $nomor_kamar = $penyewa->kamar?->nomor_kamar ?? '-';
        $periode_bulan = $tagihan->periode_bulan;
        $periode_tahun = $tagihan->periode_tahun;
        $nominal_pokok = number_format($tagihan->nominal_pokok, 0, ',', '.');
        $link_pembayaran = rtrim(config('app.url'), '/') . '/penyewa/tagihan';

        return "Halo *{$nama_penyewa}*,\n\n" .
               "Mengingatkan bahwa tagihan kost Anda untuk Kamar *{$nomor_kamar}* periode *{$periode_bulan}/{$periode_tahun}* telah melewati batas jatuh tempo (Tanggal 10).\n\n" .
               "Saat ini Anda berada dalam *Masa Keringanan (Toleransi)*. Anda masih dapat melakukan pembayaran sebesar *Rp {$nominal_pokok}* tanpa dikenakan denda sama sekali hingga akhir bulan ini.\n\n" .
               "⚠️ *PENTING:* Jika pembayaran belum diselesaikan hingga berganti bulan, sistem akan otomatis memberlakukan denda keterlambatan 5% pada tagihan ini.\n\n" .
               "Tautan bayar resmi: {$link_pembayaran}";
    }

    /**
     * Template WhatsApp: Notifikasi Wali
     */
    public function buildNotifikasiWali(Tagihan $tagihan, Penyewa $penyewa): string
    {
        $nama_wali = $penyewa->nama_wali ?? 'Wali';
        $nama_penyewa = $penyewa->user?->nama ?? 'Penyewa';
        $nomor_kamar = $penyewa->kamar?->nomor_kamar ?? '-';
        $periode_bulan = $tagihan->periode_bulan;
        $periode_tahun = $tagihan->periode_tahun;
        $nominal_pokok = number_format($tagihan->nominal_pokok, 0, ',', '.');
        $nominal_denda = number_format($tagihan->nominal_denda, 0, ',', '.');
        $nominal_total = number_format($tagihan->nominal_total, 0, ',', '.');
        $link_pembayaran = rtrim(config('app.url'), '/') . '/penyewa/tagihan';

        return "Yth. Bapak/Ibu *{$nama_wali}*,\n" .
               "Wali dari *{$nama_penyewa}*,\n\n" .
               "Kami dari Manajemen Asri Boarding House menginformasikan bahwa tagihan sewa kost Putra/Putri Anda untuk Kamar *{$nomor_kamar}* periode *{$periode_bulan}/{$periode_tahun}* belum diselesaikan hingga melewati batas akhir bulan kalender berjalan.\n\n" .
               "Masa keringanan telah berakhir dan sistem otomatis memberlakukan denda keterlambatan 5%.\n\n" .
               "Rincian tunggakan wajib bayar:\n" .
               "- Pokok Kost: Rp {$nominal_pokok}\n" .
               "- Denda: Rp {$nominal_denda}\n" .
               "- *Total Akhir: Rp {$nominal_total}*\n\n" .
               "Mohon bantuan Bapak/Ibu untuk mengingatkan atau membantu Putra/Putri Anda agar dapat segera melakukan pelunasan via tautan resmi Midtrans berikut:\n" .
               "🔗 {$link_pembayaran}";
    }

    /**
     * Template WhatsApp: Denda
     */
    public function buildDenda(Tagihan $tagihan, Penyewa $penyewa): string
    {
        $nama_penyewa = $penyewa->user?->nama ?? 'Penyewa';
        $nomor_kamar = $penyewa->kamar?->nomor_kamar ?? '-';
        $periode_bulan = $tagihan->periode_bulan;
        $periode_tahun = $tagihan->periode_tahun;
        $nominal_pokok = number_format($tagihan->nominal_pokok, 0, ',', '.');
        $nominal_denda = number_format($tagihan->nominal_denda, 0, ',', '.');
        $nominal_total = number_format($tagihan->nominal_total, 0, ',', '.');
        $link_pembayaran = rtrim(config('app.url'), '/') . '/penyewa/tagihan';

        return "⚠️ *PEMBERITAHUAN DENDA KETERLAMBATAN* ⚠️\n\n" .
               "Halo *{$nama_penyewa}*,\n\n" .
               "Kami menginformasikan bahwa tagihan kost Kamar *{$nomor_kamar}* untuk periode *{$periode_bulan}/{$periode_tahun}* belum diselesaikan hingga melewati batas akhir bulan berjalan. Masa keringanan Anda telah berakhir dan sistem memberlakukan *Denda Keterlambatan 5%*.\n\n" .
               "Rincian tunggakan:\n" .
               "- Pokok: Rp {$nominal_pokok}\n" .
               "- Denda: Rp {$nominal_denda}\n" .
               "- *Total Wajib Bayar: Rp {$nominal_total}*\n\n" .
               "Mohon segera melunasi via tautan resmi berikut untuk menghindari eskalasi pemberitahuan ke wali:\n" .
               "🔗 {$link_pembayaran}";
    }

    /**
     * Template WhatsApp: Welcome Penyewa Baru
     */
    public function buildWelcomePenyewa(Penyewa $penyewa): string
    {
        $nomorKamar = $penyewa->kamar?->nomor_kamar ?? '-';
        $tipe = $penyewa->kamar?->tipe ?? '-';
        $hargaBulan = number_format($penyewa->harga_sewa ?? ($penyewa->kamar?->harga_bulan ?? 0), 0, ',', '.');
        $appUrl = rtrim(config('app.url'), '/');
        $email = $penyewa->user?->email ?? '-';
        $noHp = $penyewa->user?->no_hp ?? '-';

        return "🏠 *SELAMAT DATANG DI ASRI BOARDING HOUSE* 🏠\n\n" .
               "Halo, selamat bergabung! Berikut rincian kamar dan kredensial akun portal Anda:\n\n" .
               "🚪 *Kamar:* Kamar {$nomorKamar} ({$tipe})\n" .
               "💵 *Sewa:* Rp {$hargaBulan}/bulan\n\n" .
               "Silakan masuk ke Portal Penyewa untuk memantau tagihan dan fasilitas:\n" .
               "🔗 *Login:* {$appUrl}/penyewa/login\n" .
               "📧 *Email:* {$email}\n" .
               "🔑 *Password:* {$noHp} (segera ganti demi keamanan)\n\n" .
               "Selamat beristirahat!\n" .
               "— *Manajemen Asri Boarding House*";
    }

    /**
     * Template WhatsApp: Reservasi Dibuat
     */
    public function buildReservasiDibuat(Reservasi $reservasi): string
    {
        $namaUser = $reservasi->user?->nama ?? '-';
        $nomorKamar = $reservasi->kamar?->nomor_kamar ?? '-';
        $tipeSewa = $reservasi->tipe_sewa;
        $durasi = $reservasi->durasi;
        $tanggalMulai = $reservasi->tanggal_mulai;
        $totalHarga = number_format($reservasi->total_harga ?? 0, 0, ',', '.');
        $appUrl = rtrim(config('app.url'), '/');

        return "🔔 *RESERVASI BARU MASUK* 🔔\n\n" .
               "Halo Admin, terdapat reservasi baru melalui website:\n\n" .
               "👤 *Nama:* {$namaUser}\n" .
               "🏠 *Kamar:* Kamar {$nomorKamar}\n" .
               "⏱️ *Durasi:* {$durasi} ({$tipeSewa})\n" .
               "📅 *Tanggal Masuk:* {$tanggalMulai}\n" .
               "💰 *Total:* Rp {$totalHarga}\n\n" .
               "Harap periksa dan verifikasi di admin panel:\n" .
               "🔗 {$appUrl}/admin/reservasi/{$reservasi->id}\n\n" .
               "— *Sistem Asri Boarding House*";
    }

    /**
     * Template WhatsApp: Reservasi Dibayar
     */
    public function buildReservasiDibayar(Reservasi $reservasi): string
    {
        $nama = $reservasi->user?->nama ?? '-';
        $nomorKamar = $reservasi->kamar?->nomor_kamar ?? '-';
        $nominal = $reservasi->is_dp ? ($reservasi->nominal_dp ?? 0) : ($reservasi->total_harga ?? 0);
        $nominalStr = number_format($nominal, 0, ',', '.');

        return "✅ Pembayaran Reservasi DITERIMA!\n\n" .
               "Halo *{$nama}*, pembayaran booking untuk *Kamar {$nomorKamar}* telah dikonfirmasi oleh sistem.\n\n" .
               "💰 *Jumlah Dibayar:* Rp {$nominalStr}\n\n" .
               "Tim admin kami akan segera menghubungi Anda untuk langkah selanjutnya.\n" .
               "— *Manajemen Asri Boarding House*";
    }

    /**
     * Template WhatsApp: Reservasi Dikonfirmasi
     */
    public function buildReservasiDikonfirmasi(Reservasi $reservasi): string
    {
        $nama = $reservasi->user?->nama ?? '-';
        $nomorKamar = $reservasi->kamar?->nomor_kamar ?? '-';
        $tanggalMulai = $reservasi->tanggal_mulai;
        $appUrl = rtrim(config('app.url'), '/');

        return "🏠 *RESERVASI DIKONFIRMASI* 🏠\n\n" .
               "Selamat Datang di Asri Boarding House!\n\n" .
               "Halo *{$nama}*, reservasi Anda untuk *Kamar {$nomorKamar}* telah disetujui.\n" .
               "📅 *Mulai Masuk:* {$tanggalMulai}\n\n" .
               "Silakan masuk ke portal penyewa menggunakan akun Anda:\n" .
               "🔗 {$appUrl}/penyewa/dashboard\n\n" .
               "Sampai jumpa di kost!\n" .
               "— *Manajemen Asri Boarding House*";
    }
}
