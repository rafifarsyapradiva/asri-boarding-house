<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            UserPenyewaSeeder::class,
            SettingSeeder::class,
            FasilitasSeeder::class,
            KamarSeeder::class,
            CustomerReviewSeeder::class,
            FaqSeeder::class,
            PeraturanSeeder::class,
            GallerySeeder::class,
        ]);
    }
}
