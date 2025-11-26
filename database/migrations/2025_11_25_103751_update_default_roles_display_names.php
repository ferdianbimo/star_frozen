<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('roles')->where('name', 'manager')->update([
            'display_name' => 'Manager',
            'description' => 'Full access to manage inventory, finance, and users'
        ]);

        DB::table('roles')->where('name', 'kasir')->update([
            'display_name' => 'Kasir',
            'description' => 'Handle POS transactions and basic inventory management'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('roles')->whereIn('name', ['manager', 'kasir'])->update([
            'display_name' => null,
            'description' => null
        ]);
    }
};
