<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Mengisi tabel settings dengan nilai default resmi sesuai Blueprint & PRD.
     */
    public function run(): void
    {
        $settings = [
            'logo_text' => 'Asri Boarding House',
            'logo_icon' => '🏡',
            'hero_tagline' => 'DIGITAL BOARDING HOUSE',
            'hero_title' => 'HUNIAN KOST EXCLUSIVE & MODERN',
            'hero_description' => 'Solusi kost eksklusif bebas ribet untuk mahasiswa dan pekerja di Semarang. Satu harga sudah mencakup semua kenyamanan: Internet Super Cepat tanpa kuota, Listrik Gratis, serta lingkungan yang Aman & Tenteram. Tersedia 32 unit kamar eksklusif yang siap menjadi tempat terbaikmu untuk produktif sekaligus beristirahat setelah seharian beraktivitas.',
            'hero_image' => 'images/hero-dummy.png',

            'contact_address' => 'Jl. Maera Sari No.1/no.12, Tembalang, Kec. Tembalang, Kota Semarang, Jawa Tengah 50275',
            'contact_whatsapp' => '62895330031313',
            'contact_email' => 'info@asriboardinghouse.com',
            'google_maps_embed' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.6052329388147!2d110.4357388!3d-7.0555541!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e708c3ea3f605a1%3A0x6338b7e289bf6560!2sJl.%20Maera%20Sari%2C%20Tembalang%2C%20Kec.%20Tembalang%2C%20Kota%20Semarang%2C%20Jawa%20Tengah!5e0!3m2!1sid!2sid!4v1717460000000!5m2!1sid!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="w-full h-full"></iframe>',

            'bank_name' => 'Bank Mandiri',
            'bank_account_number' => '123-456-7890',
            'bank_account_owner' => 'Asri Boarding House',

            'durasi_sewa_default' => '12',

            'promo_section_title' => 'Paket Promo Spesial',
            'promo_section_subtitle' => 'Pilih paket sewa terbaik untuk hemat lebih banyak!',
            'promo_pkg1_name' => 'Paket Fleksibel Bulanan',
            'promo_pkg1_type' => 'bulanan',
            'promo_pkg1_duration' => '1',
            'promo_pkg1_discount' => '0',
            'promo_pkg1_desc' => 'Sewa bulanan standar tanpa komitmen panjang, bayar per bulan sesuai kebutuhan.',

            'promo_pkg2_name' => 'Paket Hemat Triwulan',
            'promo_pkg2_type' => 'bulanan',
            'promo_pkg2_duration' => '3',
            'promo_pkg2_discount' => '5',
            'promo_pkg2_desc' => 'Pilihan cerdas sewa 3 bulan sekaligus dengan harga promo lebih terjangkau.',

            'promo_pkg3_name' => 'Paket Tahunan Super Promo',
            'promo_pkg3_type' => 'bulanan',
            'promo_pkg3_duration' => '12',
            'promo_pkg3_discount' => '8.33',
            'promo_pkg3_desc' => 'Hemat maksimal! Sewa 12 bulan penuh, diskon spesial setara GRATIS 1 bulan sewa.',

            'about_title' => 'Tentang Kami',
            'about_description' => 'Kost Asri Boarding House adalah hunian eksklusif modern yang didesain secara khusus untuk memenuhi kebutuhan tempat tinggal mahasiswa dan profesional muda di kawasan Semarang. Berkomitmen menghadirkan keseimbangan antara kenyamanan, fungsionalitas, keamanan, serta kemudahan transaksi digital.',
            'about_visi' => 'Menjadi pelopor hunian kost modern terintegrasi yang paling tepercaya dengan mengedepankan kualitas layanan, kelengkapan fasilitas, serta integrasi teknologi untuk memberikan pengalaman tinggal terbaik bagi mahasiswa dan pekerja.',
            'about_misi_1' => 'Konsisten memelihara kebersihan, ketertiban, dan keindahan lingkungan hunian.',
            'about_misi_2' => 'Menyediakan fasilitas premium modern demi kemudahan belajar dan produktivitas bekerja.',
            'about_misi_3' => 'Menerapkan sistem administrasi reservasi dan pembayaran digital yang aman, transparan, dan realtime.',
            'about_misi_4' => 'Membangun komunikasi dan hubungan kekeluargaan yang baik dengan seluruh penghuni.',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
