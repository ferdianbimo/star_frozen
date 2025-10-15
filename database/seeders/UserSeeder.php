<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID dari role
        $managerRole = Role::where('name', 'manager')->first();
        $cashierRole = Role::where('name', 'kasir')->first();

        // Buat user manager
        User::create([
            'name' => 'Manager',
            'email' => 'manager@starfrozen.com',
            'password' => Hash::make('manager123'),
            'role_id' => $managerRole->id,
            'email_verified_at' => now(),
        ]);

        // Buat user kasir
        User::create([
            'name' => 'Kasir',
            'email' => 'kasir@starfrozen.com',
            'password' => Hash::make('kasir123'),
            'role_id' => $cashierRole->id,
            'email_verified_at' => now(),
        ]);
    }
}
