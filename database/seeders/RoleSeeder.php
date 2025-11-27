<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat role manager dan kasir
        Role::firstOrCreate(['name' => 'manager']);
        Role::firstOrCreate(['name' => 'kasir']);
    }
}
