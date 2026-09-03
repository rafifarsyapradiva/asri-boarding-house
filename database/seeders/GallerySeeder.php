<?php

namespace Database\Seeders;

use App\Models\Gallery;
use Illuminate\Database\Seeder;

class GallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $galleries = [
            [
                'judul' => 'Kamar VIP Eksklusif',
                'deskripsi' => 'Desain interior mewah dengan tempat tidur queen size, AC, smart TV, kamar mandi dalam, dan meja kerja modern.',
                'foto' => 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=800&q=80',
                'urutan' => 1,
                'is_active' => true,
            ],
            [
                'judul' => 'Kamar Deluxe Nyaman',
                'deskripsi' => 'Kamar luas dengan pencahayaan alami yang baik, dilengkapi dengan AC, kamar mandi dalam, lemari pakaian, dan meja belajar.',
                'foto' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80',
                'urutan' => 2,
                'is_active' => true,
            ],
            [
                'judul' => 'Kamar Standar Fungsional',
                'deskripsi' => 'Hunian kost yang praktis dan efisien dengan kasur single, AC, meja belajar, lemari, sirkulasi udara optimal, dan kamar mandi dalam.',
                'foto' => 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=800&q=80',
                'urutan' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($galleries as $gallery) {
            Gallery::create($gallery);
        }
    }
}
