<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['role' => 'admin'],
            [
                'nama' => 'Admin Asep',
                'email' => 'asri-asep@gmail.com',
                'password' => Hash::make('asriasep48'),
                'no_hp' => '6281234567890',
                'require_password_change' => false,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
