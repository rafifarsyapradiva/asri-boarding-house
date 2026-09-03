<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kamar;
use App\Models\Fasilitas;

class KamarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hubungkan Fasilitas secara dinamis berdasarkan nama fasilitas (Bebas dari pergeseran Auto Increment ID)
        $vipFasilitas = Fasilitas::whereIn('nama', [
            'Air Conditioner (AC)',
            'Wi-Fi High Speed',
            'Free Listrik',
            'Kamar Mandi Dalam',
            'Lemari Pakaian',
            'Meja Belajar',
            'Kasur Springbed Queen'
        ])->pluck('id')->toArray();

        $deluxeFasilitas = Fasilitas::whereIn('nama', [
            'Wi-Fi High Speed',
            'Free Listrik',
            'Kamar Mandi Dalam',
            'Lemari Pakaian',
            'Meja Belajar',
            'Kasur Springbed Queen'
        ])->pluck('id')->toArray();

        $standarFasilitas = Fasilitas::whereIn('nama', [
            'Wi-Fi High Speed',
            'Free Listrik',
            'Kamar Mandi Luar (Bersama)',
            'Lemari Pakaian',
            'Meja Belajar',
            'Kasur Springbed Queen'
        ])->pluck('id')->toArray();

        // Lantai 1 (16 Kamar: 101 - 116)
        // 3 VIP, 1 Deluxe, 12 Standar
        for ($i = 1; $i <= 16; $i++) {
            $nomorKamar = '1' . str_pad($i, 2, '0', STR_PAD_LEFT);
            if ($i <= 3) {
                $tipe = 'vip';
                $harga = 1400000;
                $luas = 24.00;
                $fasilitasIds = $vipFasilitas;
                $deskripsi = 'Kamar VIP mewah di lantai 1 dengan fasilitas terlengkap dan AC.';
            } elseif ($i == 4) {
                $tipe = 'deluxe';
                $harga = 950000;
                $luas = 18.00;
                $fasilitasIds = $deluxeFasilitas;
                $deskripsi = 'Kamar Deluxe nyaman di lantai 1 dengan kamar mandi dalam.';
            } else {
                $tipe = 'standar';
                $harga = 750000;
                $luas = 12.00;
                $fasilitasIds = $standarFasilitas;
                $deskripsi = 'Kamar Standar hemat di lantai 1 dengan fasilitas lengkap dan kamar mandi luar.';
            }

            $kamar = Kamar::updateOrCreate(
                ['nomor_kamar' => $nomorKamar],
                [
                    'lantai' => 1,
                    'tipe' => $tipe,
                    'luas_m2' => $luas,
                    'harga_bulan' => $harga,
                    'deskripsi' => $deskripsi,
                    'status' => 'tersedia',
                ]
            );

            // Sync fasilitas untuk idempotensi seeder
            $kamar->fasilitas()->sync($fasilitasIds);
        }

        // Lantai 2 (16 Kamar: 201 - 216)
        // 3 VIP, 2 Deluxe, 11 Standar
        for ($i = 1; $i <= 16; $i++) {
            $nomorKamar = '2' . str_pad($i, 2, '0', STR_PAD_LEFT);
            if ($i <= 3) {
                $tipe = 'vip';
                $harga = 1400000;
                $luas = 24.00;
                $fasilitasIds = $vipFasilitas;
                $deskripsi = 'Kamar VIP mewah di lantai 2 dengan pemandangan luar dan fasilitas terlengkap.';
            } elseif ($i == 4 || $i == 5) {
                $tipe = 'deluxe';
                $harga = 950000;
                $luas = 18.00;
                $fasilitasIds = $deluxeFasilitas;
                $deskripsi = 'Kamar Deluxe nyaman di lantai 2 dengan kamar mandi dalam.';
            } else {
                $tipe = 'standar';
                $harga = 750000;
                $luas = 12.00;
                $fasilitasIds = $standarFasilitas;
                $deskripsi = 'Kamar Standar hemat di lantai 2 dengan fasilitas lengkap dan kamar mandi luar.';
            }

            $kamar = Kamar::updateOrCreate(
                ['nomor_kamar' => $nomorKamar],
                [
                    'lantai' => 2,
                    'tipe' => $tipe,
                    'luas_m2' => $luas,
                    'harga_bulan' => $harga,
                    'deskripsi' => $deskripsi,
                    'status' => 'tersedia',
                ]
            );

            // Sync fasilitas untuk idempotensi seeder
            $kamar->fasilitas()->sync($fasilitasIds);
        }
    }
}
