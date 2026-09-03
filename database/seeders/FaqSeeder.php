<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kosongkan tabel terlebih dahulu untuk menghindari duplikasi data
        Faq::truncate();

        $faqs = [
            [
                'pertanyaan' => 'Bagaimana cara memesan kamar di Asri Boarding House? Apakah bisa online?',
                'jawaban' => 'Sangat mudah dan praktis! Anda bisa memesan kamar impian 100% online secara aman. Cukup pilih kamar yang Anda suka di halaman Katalog Kamar, klik "Pesan Unit", isi formulir data diri singkat, lalu selesaikan pembayaran Uang Muka (DP minimal 30%) atau pelunasan langsung secara online melalui virtual account, QRIS/e-wallet, atau kartu kredit via Midtrans.',
                'urutan' => 1,
                'is_active' => 1,
            ],
            [
                'pertanyaan' => 'Apakah harga sewa bulanan sudah termasuk biaya listrik dan air?',
                'jawaban' => 'Benar sekali! Seluruh biaya sewa kamar di Asri Boarding House sudah bersifat All-In, yaitu sudah mencakup gratis listrik harian dan pemakaian air bersih. Anda tidak perlu lagi khawatir dengan tagihan tambahan atau pusing memikirkan token listrik bulanan!',
                'urutan' => 2,
                'is_active' => 1,
            ],
            [
                'pertanyaan' => 'Fasilitas eksklusif apa saja yang sudah siap saya gunakan di dalam kamar?',
                'jawaban' => 'Kamar kami siap huni (ready-to-use) dengan fasilitas premium: AC dingin, kamar mandi dalam bersih & modern, kasur spring bed premium yang empuk, meja & kursi belajar nyaman, lemari pakaian fungsional, sirkulasi udara optimal dengan jendela luar, serta koneksi Wi-Fi super cepat 24 jam.',
                'urutan' => 3,
                'is_active' => 1,
            ],
            [
                'pertanyaan' => 'Bagaimana dengan sistem akses gerbang? Apakah ada jam malam?',
                'jawaban' => 'Demi menunjang produktivitas Anda, kami memberikan akses bebas penuh 24 jam. Pintu masuk area kost menggunakan sistem keamanan mandiri (smart access gate & digital lock), sehingga Anda bebas pulang-pergi dengan aman sesuai kesibukan kuliah/kerja tanpa perlu cemas terkunci di luar.',
                'urutan' => 4,
                'is_active' => 1,
            ],
            [
                'pertanyaan' => 'Bagaimana cara membayar tagihan sewa untuk bulan-bulan berikutnya?',
                'jawaban' => 'Pembayaran sangat transparan dan otomatis. Anda cukup masuk ke Portal Penyewa, buka menu "Tagihan Saya", dan selesaikan pembayaran secara instan menggunakan metode online (Virtual Account bank pilihan, ShopeePay/GoPay/OVO/Dana, QRIS, atau gerai ritel). Sistem kami akan langsung mencatat pembayaran Anda dalam hitungan detik secara otomatis.',
                'urutan' => 5,
                'is_active' => 1,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}
