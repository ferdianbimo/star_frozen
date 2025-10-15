<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Jalankan RoleSeeder dulu, karena UserSeeder membutuhkan role
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
