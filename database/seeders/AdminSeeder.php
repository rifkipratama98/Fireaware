<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Membuat akun admin pertama.
 *
 * Jalankan SEKALI di awal: php artisan db:seed --class=AdminSeeder
 *
 * Password diambil dari variabel lingkungan ADMIN_PASSWORD.
 * Jika tidak diset, seeder berhenti dan melempar exception agar
 * password tidak pernah terpasang secara default di kode produksi.
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('ADMIN_PASSWORD');

        if (empty($password)) {
            throw new \RuntimeException(
                'ADMIN_PASSWORD belum diset di .env. '
                . 'Tambahkan ADMIN_PASSWORD=<password-aman> sebelum menjalankan seeder ini.'
            );
        }

        $email = env('ADMIN_EMAIL', 'admin@fireaware.id');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name'     => env('ADMIN_NAME', 'Administrator'),
                'password' => Hash::make($password),
            ]
        );

        $this->command->info("Akun admin berhasil dibuat: {$email}");
    }
}
