<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Display the settings form.
     */
    public function edit(): View
    {
        $settings = [];
        foreach (array_keys(Setting::$defaults) as $key) {
            $settings[$key] = Setting::get($key);
        }

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request): RedirectResponse
    {
        if ($request->has('contact_whatsapp') && is_string($request->input('contact_whatsapp'))) {
            $cleanWa = Setting::formatWhatsapp($request->input('contact_whatsapp'));
            $request->merge(['contact_whatsapp' => $cleanWa]);
        }

        if ($request->has('google_maps_embed') && is_string($request->input('google_maps_embed'))) {
            $request->merge(['google_maps_embed' => trim($request->input('google_maps_embed'))]);
        }

        $rules = [
            'logo_text' => ['required', 'string', 'max:100'],
            'logo_icon' => ['required', 'string', 'max:50'],
            'hero_tagline' => ['required', 'string', 'max:200'],
            'hero_title' => ['required', 'string', 'max:250'],
            'hero_description' => ['required', 'string', 'max:1000'],
            'hero_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'contact_address' => ['required', 'string', 'max:500'],
            'contact_whatsapp' => ['required', 'string', 'regex:/^(\+)?(0|62)8[0-9]{8,13}$/'],
            'contact_email' => ['required', 'string', 'email', 'max:150'],
            'google_maps_embed' => [
                'required',
                'string',
                'max:2000',
                new \App\Rules\ValidGoogleMapsEmbed(),
            ],
            'bank_name' => ['required', 'string', 'max:100'],
            'bank_account_number' => ['required', 'string', 'max:50'],
            'bank_account_owner' => ['required', 'string', 'max:150'],

            'promo_section_title' => ['sometimes', 'required', 'string', 'max:100'],
            'promo_section_subtitle' => ['sometimes', 'required', 'string', 'max:200'],

            'about_title' => ['required', 'string', 'max:150'],
            'about_description' => ['required', 'string', 'max:1500'],
            'about_visi' => ['required', 'string', 'max:1000'],
            'about_misi_1' => ['required', 'string', 'max:500'],
            'about_misi_2' => ['required', 'string', 'max:500'],
            'about_misi_3' => ['required', 'string', 'max:500'],
            'about_misi_4' => ['required', 'string', 'max:500'],
        ];

        $messages = [
            'logo_text.required' => 'Nama brand/logo wajib diisi.',
            'logo_icon.required' => 'Ikon logo wajib diisi.',
            'hero_tagline.required' => 'Tagline hero wajib diisi.',
            'hero_title.required' => 'Judul utama hero wajib diisi.',
            'hero_description.required' => 'Deskripsi hero wajib diisi.',
            'hero_image.image' => 'Hero image harus berupa file gambar.',
            'hero_image.mimes' => 'Format hero image harus jpeg, jpg, atau png.',
            'hero_image.max' => 'Ukuran hero image maksimal adalah 2MB.',
            'contact_address.required' => 'Alamat kontak wajib diisi.',
            'contact_whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'contact_whatsapp.regex' => 'Format nomor WhatsApp tidak valid (contoh: 0812xxx atau 62812xxx).',
            'contact_email.required' => 'Email kontak wajib diisi.',
            'contact_email.email' => 'Format email tidak valid.',
            'google_maps_embed.required' => 'Tautan / Embed Google Maps wajib diisi.',
            'bank_name.required' => 'Nama bank wajib diisi.',
            'bank_account_number.required' => 'Nomor rekening wajib diisi.',
            'bank_account_owner.required' => 'Nama pemilik rekening wajib diisi.',
            'promo_section_title.required' => 'Judul bagian promo wajib diisi.',
            'promo_section_subtitle.required' => 'Subjudul bagian promo wajib diisi.',
            'about_title.required' => 'Judul Tentang Kami wajib diisi.',
            'about_description.required' => 'Deskripsi Tentang Kami wajib diisi.',
            'about_visi.required' => 'Visi Tentang Kami wajib diisi.',
            'about_misi_1.required' => 'Misi poin 1 wajib diisi.',
            'about_misi_2.required' => 'Misi poin 2 wajib diisi.',
            'about_misi_3.required' => 'Misi poin 3 wajib diisi.',
            'about_misi_4.required' => 'Misi poin 4 wajib diisi.',
        ];

        for ($i = 1; $i <= 3; $i++) {
            $rules["promo_pkg{$i}_name"] = ['sometimes', 'required', 'string', 'max:100'];
            $rules["promo_pkg{$i}_type"] = ['sometimes', 'required', 'string', 'in:harian,mingguan,bulanan'];
            $rules["promo_pkg{$i}_duration"] = [
                'sometimes', 'required', 'integer', 'min:1', 'max:30',
                function ($attribute, $value, $fail) use ($request, $i) {
                    $typeKey = "promo_pkg{$i}_type";
                    $type = $request->input($typeKey);
                    if ($type) {
                        $min = config("reservasi.min_durasi.{$type}", 1);
                        $max = config("reservasi.max_durasi.{$type}", 30);
                        if ($value < $min || $value > $max) {
                            $typeLabel = $type === 'harian' ? 'Harian' : ($type === 'mingguan' ? 'Mingguan' : 'Bulanan');
                            $fail("Durasi Paket {$i} harus antara {$min} dan {$max} untuk tipe sewa {$typeLabel}.");
                        }
                    }
                }
            ];
            $rules["promo_pkg{$i}_discount"] = ['sometimes', 'required', 'numeric', 'min:0', 'max:100'];
            $rules["promo_pkg{$i}_desc"] = ['sometimes', 'required', 'string', 'max:500'];

            $messages["promo_pkg{$i}_name.required"] = "Nama Paket {$i} wajib diisi.";
            $messages["promo_pkg{$i}_type.required"] = "Tipe sewa Paket {$i} wajib diisi.";
            $messages["promo_pkg{$i}_type.in"] = "Tipe sewa Paket {$i} harus berupa harian, mingguan, atau bulanan.";
            $messages["promo_pkg{$i}_duration.required"] = "Durasi Paket {$i} wajib diisi.";
            $messages["promo_pkg{$i}_duration.integer"] = "Durasi Paket {$i} harus berupa angka bulat.";
            $messages["promo_pkg{$i}_duration.min"] = "Durasi Paket {$i} minimal 1.";
            $messages["promo_pkg{$i}_duration.max"] = "Durasi Paket {$i} maksimal 30.";
            $messages["promo_pkg{$i}_discount.required"] = "Diskon Paket {$i} wajib diisi.";
            $messages["promo_pkg{$i}_discount.numeric"] = "Diskon Paket {$i} harus berupa angka.";
            $messages["promo_pkg{$i}_discount.min"] = "Diskon Paket {$i} minimal 0.";
            $messages["promo_pkg{$i}_discount.max"] = "Diskon Paket {$i} maksimal 100.";
            $messages["promo_pkg{$i}_desc.required"] = "Deskripsi Paket {$i} wajib diisi.";
        }

        $request->validate($rules, $messages);

        if ($request->has('google_maps_embed') && is_string($request->input('google_maps_embed'))) {
            $request->merge([
                'google_maps_embed' => Setting::sanitizeGoogleMapsEmbed($request->input('google_maps_embed'))
            ]);
        }

        if ($request->hasFile('hero_image')) {
            $file = $request->file('hero_image');
            
            // Hapus file lama jika ada dan bukan URL eksternal
            $oldPath = Setting::get('hero_image');
            if ($oldPath && !str_starts_with($oldPath, 'http')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
            }
            
            $path = $file->store('settings', 'public');
            Setting::updateOrCreate(
                ['key' => 'hero_image'],
                ['value' => $path]
            );
        }

        foreach (array_keys(Setting::$defaults) as $key) {
            if ($key === 'hero_image') {
                continue;
            }
            if ($request->has($key)) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $request->input($key)]
                );
            }
        }

        return redirect()->route('admin.settings.edit')->with('success', 'Pengaturan konten landing page berhasil diperbarui!');
    }
}
