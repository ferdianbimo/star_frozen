<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * New unit structure - each unit specifies what it contains:
     * - Karton: contains X of (box/renteng/pack/pcs)
     * - Box: contains X of (renteng/pack/pcs)
     * - Renteng: contains X pcs
     * - Pack: contains X pcs
     * - Pcs: base unit
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Karton contains what unit
            if (!Schema::hasColumn('products', 'karton_contains_unit')) {
                $table->string('karton_contains_unit')->nullable()->after('box_per_karton'); // box, renteng, pack, pcs
            }
            if (!Schema::hasColumn('products', 'karton_contains_qty')) {
                $table->integer('karton_contains_qty')->nullable()->after('karton_contains_unit');
            }
            
            // Box contains what unit
            if (!Schema::hasColumn('products', 'box_contains_unit')) {
                $table->string('box_contains_unit')->nullable()->after('karton_contains_qty'); // renteng, pack, pcs
            }
            if (!Schema::hasColumn('products', 'box_contains_qty')) {
                $table->integer('box_contains_qty')->nullable()->after('box_contains_unit');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'karton_contains_unit',
                'karton_contains_qty',
                'box_contains_unit',
                'box_contains_qty',
            ]);
        });
    }
};
