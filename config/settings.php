<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Setting Values
    |--------------------------------------------------------------------------
    |
    | Default configurations for the landing page, contact information,
    | marketing promo packages, and about us section.
    |
    */

    'defaults' => [
        'logo_text' => env('SETTING_LOGO_TEXT', 'Asri Boarding House'),
        'logo_icon' => env('SETTING_LOGO_ICON', '🏡'),
        'hero_tagline' => env('SETTING_HERO_TAGLINE', '✨ Hunian Terpopuler & Modern: Asri Boarding House'),
        'hero_title' => env('SETTING_HERO_TITLE', 'Kost Eksklusif Bebas Ribet di Semarang'),
        'hero_description' => env(
            'SETTING_HERO_DESC',
            'Solusi kost eksklusif bebas ribet untuk mahasiswa dan pekerja di Semarang. Satu harga sudah mencakup semua kenyamanan: Internet Super Cepat tanpa kuota, Listrik Gratis, serta lingkungan yang Aman & Tenteram. Tersedia 32 unit kamar eksklusif yang siap menjadi tempat terbaikmu untuk produktif sekaligus beristirahat setelah seharian beraktivitas.'
        ),
        'hero_image' => env('SETTING_HERO_IMAGE', 'images/hero-dummy.png'),

        'contact_address' => env('SETTING_CONTACT_ADDRESS', 'Jl. Maera Sari No.1/no.12, Tembalang, Kec. Tembalang, Kota Semarang, Jawa Tengah 50275'),
        'contact_whatsapp' => env('ADMIN_WA_NUMBER', '0895330031313'),
        'contact_email' => env('SETTING_CONTACT_EMAIL', 'info@asriboardinghouse.com'),
        'google_maps_url' => env('SETTING_MAPS_URL', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.6052329388147!2d110.4357388!3d-7.0555541!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e708c3ea3f605a1%3A0x6338b7e289bf6560!2sJl.%20Maera%20Sari%2C%20Tembalang%2C%20Kec.%20Tembalang%2C%20Kota%20Semarang%2C%20Jawa%20Tengah!5e0!3m2!1sid!2sid!4v1717460000000!5m2!1sid!2sid'),
        'google_maps_embed' => '<iframe src="' . env('SETTING_MAPS_URL', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.6052329388147!2d110.4357388!3d-7.0555541!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e708c3ea3f605a1%3A0x6338b7e289bf6560!2sJl.%20Maera%20Sari%2C%20Tembalang%2C%20Kec.%20Tembalang%2C%20Kota%20Semarang%2C%20Jawa%20Tengah!5e0!3m2!1sid!2sid!4v1717460000000!5m2!1sid!2sid') . '" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',

        'bank_name' => env('SETTING_BANK_NAME', 'Bank Mandiri'),
        'bank_account_number' => env('SETTING_BANK_ACCOUNT', '123-456-7890'),
        'bank_account_owner' => env('SETTING_BANK_OWNER', 'Asri Boarding House'),

        // System Settings
        'durasi_sewa_default' => (string) env('SETTING_DURASI_SEWA_DEFAULT', '12'),

        // Backward-compatible Flat Promo Keys
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

        'promo_section_title' => 'Paket Promo Spesial',
        'promo_section_subtitle' => 'Pilih paket sewa terbaik untuk hemat lebih banyak!',

        // Structured Dynamic Marketing Packages
        'promo_section' => [
            'title' => env('SETTING_PROMO_TITLE', 'Paket Promo Spesial'),
            'subtitle' => env('SETTING_PROMO_SUBTITLE', 'Pilih paket sewa terbaik untuk hemat lebih banyak!'),
            'packages' => [
                [
                    'id' => 'pkg1',
                    'name' => 'Paket Fleksibel Bulanan',
                    'type' => 'bulanan',
                    'duration' => 1,
                    'discount_percentage' => 0.0,
                    'description' => 'Sewa bulanan standar tanpa komitmen panjang, bayar per bulan sesuai kebutuhan.',
                ],
                [
                    'id' => 'pkg2',
                    'name' => 'Paket Hemat Triwulan',
                    'type' => 'bulanan',
                    'duration' => 3,
                    'discount_percentage' => 5.0,
                    'description' => 'Pilihan cerdas sewa 3 bulan sekaligus dengan harga promo lebih terjangkau.',
                ],
                [
                    'id' => 'pkg3',
                    'name' => 'Paket Tahunan Super Promo',
                    'type' => 'bulanan',
                    'duration' => 12,
                    'discount_percentage' => 8.33,
                    'description' => 'Hemat maksimal! Sewa 12 bulan penuh, diskon spesial setara GRATIS 1 bulan sewa.',
                ],
            ],
        ],

        // Tentang Kami Settings
        'about_title' => 'Tentang Kami',
        'about_description' => 'Kost Asri Boarding House adalah hunian eksklusif modern yang didesain secara khusus untuk memenuhi kebutuhan tempat tinggal mahasiswa dan profesional muda di kawasan Semarang. Berkomitmen menghadirkan keseimbangan antara kenyamanan, fungsionalitas, keamanan, serta kemudahan transaksi digital.',
        'about_visi' => 'Menjadi pelopor hunian kost modern terintegrasi yang paling tepercaya dengan mengedepankan kualitas layanan, kelengkapan fasilitas, serta integrasi teknologi untuk memberikan pengalaman tinggal terbaik bagi mahasiswa dan pekerja.',
        'about_misi_1' => 'Konsisten memelihara kebersihan, ketertiban, dan keindahan lingkungan hunian.',
        'about_misi_2' => 'Menyediakan fasilitas premium modern demi kemudahan belajar dan produktivitas bekerja.',
        'about_misi_3' => 'Menerapkan sistem administrasi reservasi dan pembayaran digital yang aman, transparan, dan realtime.',
        'about_misi_4' => 'Membangun komunikasi dan hubungan kekeluargaan yang baik dengan seluruh penghuni.',

        // Structured About Us Section
        'about' => [
            'title' => env('SETTING_ABOUT_TITLE', 'Tentang Kami'),
            'description' => env('SETTING_ABOUT_DESC', 'Kost Asri Boarding House adalah hunian eksklusif modern yang didesain secara khusus untuk memenuhi kebutuhan tempat tinggal mahasiswa dan profesional muda di kawasan Semarang. Berkomitmen menghadirkan keseimbangan antara kenyamanan, fungsionalitas, keamanan, serta kemudahan transaksi digital.'),
            'visi' => env('SETTING_ABOUT_VISI', 'Menjadi pelopor hunian kost modern terintegrasi yang paling tepercaya dengan mengedepankan kualitas layanan, kelengkapan fasilitas, serta integrasi teknologi untuk memberikan pengalaman tinggal terbaik bagi mahasiswa dan pekerja.'),
            'misi' => [
                'Konsisten memelihara kebersihan, ketertiban, dan keindahan lingkungan hunian.',
                'Menyediakan fasilitas premium modern demi kemudahan belajar dan produktivitas bekerja.',
                'Menerapkan sistem administrasi reservasi dan pembayaran digital yang aman, transparan, dan realtime.',
                'Membangun komunikasi dan hubungan kekeluargaan yang baik dengan seluruh penghuni.',
            ],
        ],
    ],

];
