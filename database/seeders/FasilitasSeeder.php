<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fasilitas;

class FasilitasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fasilitas = [
            [
                'nama' => 'Air Conditioner (AC)',
                'ikon' => 'snowflake',
                'is_active' => true,
                'deskripsi' => 'Pendingin ruangan untuk kenyamanan maksimal sepanjang hari.',
            ],
            [
                'nama' => 'Wi-Fi High Speed',
                'ikon' => 'wifi',
                'is_active' => true,
                'deskripsi' => 'Koneksi internet nirkabel cepat dan stabil di seluruh area kamar.',
            ],
            [
                'nama' => 'Free Listrik',
                'ikon' => 'bolt',
                'is_active' => true,
                'deskripsi' => 'Biaya sewa sudah termasuk penggunaan listrik standar.',
            ],
            [
                'nama' => 'Kamar Mandi Dalam',
                'ikon' => 'bath',
                'is_active' => true,
                'deskripsi' => 'Kamar mandi pribadi di dalam kamar dilengkapi fasilitas saniter modern.',
            ],
            [
                'nama' => 'Kamar Mandi Luar (Bersama)',
                'ikon' => 'shower',
                'is_active' => true,
                'deskripsi' => 'Kamar mandi luar bersih untuk digunakan bersama.',
            ],
            [
                'nama' => 'Lemari Pakaian',
                'ikon' => 'door-closed',
                'is_active' => true,
                'deskripsi' => 'Lemari pakaian luas untuk penyimpanan pakaian.',
            ],
            [
                'nama' => 'Meja Belajar',
                'ikon' => 'desktop',
                'is_active' => true,
                'deskripsi' => 'Meja belajar/kerja minimalis dengan kursi nyaman.',
            ],
            [
                'nama' => 'Kasur Springbed Queen',
                'ikon' => 'bed',
                'is_active' => true,
                'deskripsi' => 'Kasur berkualitas tinggi untuk istirahat tidur yang nyaman.',
            ],
        ];

        foreach ($fasilitas as $item) {
            Fasilitas::updateOrCreate(
                ['nama' => $item['nama']],
                [
                    'ikon' => $item['ikon'],
                    'deskripsi' => $item['deskripsi'],
                    'is_active' => $item['is_active'],
                ]
            );
        }
    }
}
