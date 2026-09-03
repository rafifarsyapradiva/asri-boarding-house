<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserPenyewaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Memasukkan data user penyewa portal tagihan.
     * Akun ini dibuat secara manual oleh admin.
     * Admin perlu mendaftarkan penyewa ini melalui panel admin (/admin/penyewa/create)
     * agar dapat mengakses portal penyewa (/penyewa/login).
     */
    public function run(): void
    {
        $email = 'rafif.arsya.pradiva@gmail.com';

        $existing = DB::table('users')
            ->where('email', $email)
            ->first();

        if ($existing) {
            // Update kredensial tanpa mengubah no_hp
            DB::table('users')
                ->where('email', $email)
                ->update([
                    'nama'                    => 'Rafif Arsya Pradiva',
                    'password'                => Hash::make('rapipap27'),
                    'role'                    => 'penyewa',
                    'is_active'               => 1,
                    'require_password_change' => false,
                    'updated_at'              => now(),
                ]);
        } else {
            // Insert baru
            DB::table('users')->insert([
                'nama'                    => 'Rafif Arsya Pradiva',
                'email'                   => $email,
                'password'                => Hash::make('rapipap27'),
                'no_hp'                   => '628000000001',
                'role'                    => 'penyewa',
                'is_active'               => 1,
                'require_password_change' => false,
                'created_at'              => now(),
                'updated_at'              => now(),
            ]);
        }
    }
}
