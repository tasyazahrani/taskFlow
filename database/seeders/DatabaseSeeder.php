<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        // Panggil seeder dalam urutan yang benar
        // User biasanya dipanggil pertama karena task mungkin membutuhkan user_id
        $this->call([
            UserSeeder::class,
            TaskSeeder::class,
        ]);
    }
}