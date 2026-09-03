<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Peraturan;

class PeraturanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kosongkan tabel terlebih dahulu untuk menghindari duplikasi data
        Peraturan::truncate();

        $rules = [
            [
                'judul' => 'Kebersihan & Kerapian',
                'deskripsi' => 'Selalu menjaga kebersihan, kerapian, dan ketertiban di lingkungan bersama Asri Boarding House.',
                'ikon' => 'sparkles',
                'urutan' => 1,
            ],
            [
                'judul' => 'Penggunaan Energi',
                'deskripsi' => 'Apabila meninggalkan kamar kost, wajib mematikan AC, lampu, serta seluruh peralatan elektronik lainnya.',
                'ikon' => 'bolt',
                'urutan' => 2,
            ],
            [
                'judul' => 'Elektronik Tambahan',
                'deskripsi' => 'Wajib melaporkan dengan jujur kepada pengelola kost apabila membawa dan menggunakan barang elektronik tambahan di luar fasilitas standar.',
                'ikon' => 'computer-desktop',
                'urutan' => 3,
            ],
            [
                'judul' => 'Siklus Pembayaran',
                'deskripsi' => 'Pembayaran uang sewa kost wajib dilakukan tepat waktu di awal bulan, paling lambat sebelum tanggal 10. Jika terjadi keterlambatan, penyewa wajib segera melakukan konfirmasi kepada pengelola.',
                'ikon' => 'credit-card',
                'urutan' => 4,
            ],
            [
                'judul' => 'Disiplin Saluran Pembuangan',
                'deskripsi' => 'Dilarang keras membuang pembalut wanita, tisu, sisa makanan, atau benda apa pun ke dalam kloset dan saluran pembuangan kamar mandi.',
                'ikon' => 'exclamation-triangle',
                'urutan' => 5,
            ],
            [
                'judul' => 'Ketentuan Tamu Menginap',
                'deskripsi' => 'Tamu yang menginap lebih dari 24 jam wajib melapor kepada pengelola kost. Apabila tamu menginap lebih dari 3 malam, akan dikenakan biaya tambahan sesuai ketentuan.',
                'ikon' => 'user-group',
                'urutan' => 6,
            ],
            [
                'judul' => 'Aturan Tamu Pria',
                'deskripsi' => 'Tamu pria dilarang keras masuk ke dalam kamar maupun area dalam kost putri. Menerima tamu pria hanya diizinkan di ruang tamu utama yang telah disediakan, kecuali atas izin khusus pengelola.',
                'ikon' => 'shield-alert',
                'urutan' => 7,
            ],
            [
                'judul' => 'Larangan Merokok',
                'deskripsi' => 'Dilarang keras merokok di dalam seluruh area lantai 1, lantai 2, maupun lingkungan dalam kawasan kost putri.',
                'ikon' => 'no-symbol',
                'urutan' => 8,
            ],
            [
                'judul' => 'Jam Malam & Akses Gerbang',
                'deskripsi' => 'Batas jam malam bertamu maksimal pukul 22:00 WIB. Bagi penghuni yang pulang ke kost di atas pukul 22:00 WIB, wajib memberikan laporan atau izin terlebih dahulu kepada pengelola/penjaga kost.',
                'ikon' => 'moon',
                'urutan' => 9,
            ],
        ];

        foreach ($rules as $rule) {
            Peraturan::create($rule);
        }
    }
}
