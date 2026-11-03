<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Untuk membuat akun admin, jalankan:
     *   ADMIN_PASSWORD=rahasia123 php artisan db:seed --class=AdminSeeder
     *
     * Atau set variabel di .env lalu jalankan seeder.
     */
    public function run(): void
    {
        $this->call([
            FireAwareDemoSeeder::class,
            GuideSeeder::class,
            EducationArticleSeeder::class,
        ]);
    }
}
