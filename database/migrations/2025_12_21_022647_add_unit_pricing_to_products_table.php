<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds unit-based pricing support:
     * - box: Contains multiple renteng, has its own price
     * - renteng: Contains multiple pcs, has its own price  
     * - pcs: Base unit (smallest unit)
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Unit configuration
            $table->string('base_unit')->default('pcs')->after('unit'); // pcs, renteng, box
            
            // Quantity conversions
            $table->integer('pcs_per_renteng')->nullable()->after('base_unit'); // How many pcs in 1 renteng
            $table->integer('renteng_per_box')->nullable()->after('pcs_per_renteng'); // How many renteng in 1 box
            
            // Prices for each unit type
            $table->decimal('price_pcs', 12, 2)->nullable()->after('price'); // Price per pcs
            $table->decimal('price_renteng', 12, 2)->nullable()->after('price_pcs'); // Price per renteng
            $table->decimal('price_box', 12, 2)->nullable()->after('price_renteng'); // Price per box
            
            // Track which units are available for sale
            $table->boolean('sell_pcs')->default(true)->after('price_box');
            $table->boolean('sell_renteng')->default(false)->after('sell_pcs');
            $table->boolean('sell_box')->default(false)->after('sell_renteng');
        });
        
        // Add unit sold info to transaction_items
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->string('unit_sold')->default('pcs')->after('quantity'); // Unit type sold (pcs/renteng/box)
            $table->integer('quantity_in_base_unit')->nullable()->after('unit_sold'); // Converted quantity in base unit (pcs)
        });
        
        // Add unit info to stock_logs
        Schema::table('stock_logs', function (Blueprint $table) {
            $table->string('unit_type')->default('pcs')->after('change'); // Unit type for the stock change
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'base_unit',
                'pcs_per_renteng',
                'renteng_per_box',
                'price_pcs',
                'price_renteng', 
                'price_box',
                'sell_pcs',
                'sell_renteng',
                'sell_box'
            ]);
        });
        
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->dropColumn(['unit_sold', 'quantity_in_base_unit']);
        });
        
        Schema::table('stock_logs', function (Blueprint $table) {
            $table->dropColumn('unit_type');
        });
    }
};
