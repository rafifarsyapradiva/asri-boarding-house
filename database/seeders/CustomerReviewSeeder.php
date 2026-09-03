<?php

namespace Database\Seeders;

use App\Models\CustomerReview;
use Illuminate\Database\Seeder;

class CustomerReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CustomerReview::create([
            'nama' => 'Rian Hidayat',
            'pekerjaan' => 'Mahasiswa Rantau (Bandung) - Universitas Diponegoro',
            'bintang' => 5,
            'ulasan' => 'Sebagai mahasiswa rantau dari Bandung, tinggal di Asri Boarding House bener-bener membantu banget. Kamarnya nyaman dan luas buat belajar, internetnya super kencang tanpa putus buat ngerjain tugas kuliah, dan deket banget ke kampus UNDIP. Suasana lingkungannya juga tenang dan aman!',
            'foto' => null,
        ]);

        CustomerReview::create([
            'nama' => 'Amelia Putri',
            'pekerjaan' => 'Pekerja Kantoran (Jakarta) - IT Consultant Semarang',
            'bintang' => 5,
            'ulasan' => 'Sangat cocok untuk pekerja rantau yang butuh ketenangan setelah seharian kerja. Fasilitas AC-nya dingin, kamar mandi bersih di dalam, dan yang paling penting listriknya sudah include (tidak perlu pusing beli token lagi). Akses ke pusat kota juga gampang sekali.',
            'foto' => null,
        ]);

        CustomerReview::create([
            'nama' => 'Bagas Saputra',
            'pekerjaan' => 'Mahasiswa Rantau (Surabaya) - Polines',
            'bintang' => 5,
            'ulasan' => 'Sudah hampir 2 tahun ngekost di sini. Kamarnya bersih, ada fasilitas cleaning service mingguan, dan pengelolanya sangat ramah serta responsif kalau ada kendala fasilitas. Dekat dengan warung makan dan minimarket, jadi gampang cari kebutuhan sehari-hari.',
            'foto' => null,
        ]);
    }
}
