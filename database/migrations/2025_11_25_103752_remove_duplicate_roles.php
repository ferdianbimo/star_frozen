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
        // Keep only the first occurrence of each role and delete duplicates
        $roles = DB::table('roles')->get();
        $seen = [];
        
        foreach ($roles as $role) {
            $key = strtolower($role->name);
            
            if (isset($seen[$key])) {
                // This is a duplicate, delete it
                DB::table('roles')->where('id', $role->id)->delete();
            } else {
                // First occurrence, keep it
                $seen[$key] = $role->id;
            }
        }
        
        // Add unique constraint to prevent future duplicates
        DB::statement('ALTER TABLE roles ADD UNIQUE KEY unique_role_name (name)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE roles DROP INDEX unique_role_name');
    }
};
