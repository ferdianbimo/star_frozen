<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateManagerUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-manager {name} {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a manager user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $managerRole = Role::where('name', 'manager')->first();

        if (!$managerRole) {
            $this->error('Manager role not found. Please run migrations first.');
            return 1;
        }

        $user = User::create([
            'name' => $this->argument('name'),
            'email' => $this->argument('email'),
            'password' => Hash::make($this->argument('password')),
            'role_id' => $managerRole->id,
        ]);

        $this->info("Manager user created successfully: {$user->name} ({$user->email})");
        
        return 0;
    }
}
