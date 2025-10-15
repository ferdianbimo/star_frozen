<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateCashierUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-cashier {name} {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a cashier user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cashierRole = Role::where('name', 'kasir')->first();

        if (!$cashierRole) {
            $this->error('Cashier role not found. Please run migrations first.');
            return 1;
        }

        $user = User::create([
            'name' => $this->argument('name'),
            'email' => $this->argument('email'),
            'password' => Hash::make($this->argument('password')),
            'role_id' => $cashierRole->id,
        ]);

        $this->info("Cashier user created successfully: {$user->name} ({$user->email})");
        
        return 0;
    }
}
