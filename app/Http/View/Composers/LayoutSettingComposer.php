<?php

namespace App\Http\View\Composers;

use App\Models\Setting;
use Illuminate\View\View;

class LayoutSettingComposer
{
    /**
     * Cache internal untuk memastikan query Setting hanya berjalan 1x per request lifecycle.
     */
    private static ?array $cachedSettings = null;

    /**
     * Inject data settings ke view layout.
     */
    public function compose(View $view): void
    {
        if (self::$cachedSettings === null) {
            $rawWa = Setting::get('contact_whatsapp') 
                ?? config('reservasi.admin_wa') 
                ?? config('settings.defaults.contact_whatsapp');

            self::$cachedSettings = [
                'waNumber' => Setting::formatWhatsapp((string) $rawWa),
                'logoText' => Setting::get('logo_text') ?: config('settings.defaults.logo_text', 'Asri Boarding House'),
                'logoIcon' => Setting::get('logo_icon') ?: config('settings.defaults.logo_icon', '🏡'),
                'heroTagline' => Setting::get('hero_tagline') ?: config('settings.defaults.hero_tagline', 'DIGITAL BOARDING HOUSE'),
                'heroTitle' => Setting::get('hero_title') ?: config('settings.defaults.hero_title', 'HUNIAN KOST EXCLUSIVE & MODERN'),
                'heroDescription' => Setting::get('hero_description') ?: config('settings.defaults.hero_description', 'Nikmati fasilitas sewa kamar kost eksklusif dengan sistem reservasi digital 24 jam.'),
                'heroImage' => Setting::get('hero_image') ?: config('settings.defaults.hero_image'),
                'contactAddress' => Setting::get('contact_address') ?: config('settings.defaults.contact_address', 'Jl. Maera Sari No.1/no.12, Tembalang, Kec. Tembalang, Kota Semarang, Jawa Tengah 50275'),
                'googleMapsEmbed' => Setting::get('google_maps_embed') ?: config('settings.defaults.google_maps_embed'),
                'aboutTitle' => Setting::get('about_title') ?: config('settings.defaults.about_title', 'Tentang Kami'),
                'aboutDescription' => Setting::get('about_description') ?: config('settings.defaults.about_description', 'Kost Asri Boarding House adalah hunian eksklusif modern yang didesain secara khusus untuk memenuhi kebutuhan tempat tinggal mahasiswa dan profesional muda di kawasan Semarang.'),
                'aboutVisi' => Setting::get('about_visi') ?: config('settings.defaults.about_visi', 'Menjadi pelopor hunian kost modern terintegrasi yang paling tepercaya.'),
                'aboutMisi1' => Setting::get('about_misi_1') ?: config('settings.defaults.about_misi_1', 'Menyediakan fasilitas kamar yang bersih, rapi, dan modern.'),
                'aboutMisi2' => Setting::get('about_misi_2') ?: config('settings.defaults.about_misi_2', 'Memberikan pelayanan reservasi digital yang cepat, transparan, dan aman.'),
                'aboutMisi3' => Setting::get('about_misi_3') ?: config('settings.defaults.about_misi_3', 'Menjaga keamanan dan ketertiban lingkungan tempat tinggal 24/7.'),
                'aboutMisi4' => Setting::get('about_misi_4') ?: config('settings.defaults.about_misi_4', 'Membangun suasana kekeluargaan yang hangat antapenyewa.'),
                'promoSectionTitle' => Setting::get('promo_section_title') ?: config('settings.defaults.promo_section_title', 'Paket Promo Spesial'),
                'promoSectionSubtitle' => Setting::get('promo_section_subtitle') ?: config('settings.defaults.promo_section_subtitle', 'Pilih paket sewa terbaik untuk hemat lebih banyak!'),
            ];
        }

        $view->with(self::$cachedSettings);
    }

    /**
     * Reset cache internal (berguna saat Unit Testing).
     */
    public static function resetCache(): void
    {
        self::$cachedSettings = null;
    }
}
