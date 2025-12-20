<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Unit hierarchy (largest to smallest):
     * - karton: Contains multiple box
     * - box: Contains multiple pack OR renteng
     * - pack: Contains multiple pcs
     * - renteng: Contains multiple pcs
     * - pcs: Base unit (smallest unit)
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Add karton and pack units
            $table->boolean('sell_karton')->default(false)->after('sell_box');
            $table->boolean('sell_pack')->default(false)->after('sell_karton');
            
            // Quantity conversions for new units
            $table->integer('box_per_karton')->nullable()->after('renteng_per_box'); // How many box in 1 karton
            $table->integer('pcs_per_pack')->nullable()->after('box_per_karton'); // How many pcs in 1 pack
            
            // Selling prices for new units
            $table->decimal('price_karton', 12, 2)->nullable()->after('price_box');
            $table->decimal('price_pack', 12, 2)->nullable()->after('price_karton');
            
            // Purchase prices for ALL units (harga beli)
            $table->decimal('purchase_price_pcs', 12, 2)->nullable()->after('purchase_price');
            $table->decimal('purchase_price_renteng', 12, 2)->nullable()->after('purchase_price_pcs');
            $table->decimal('purchase_price_box', 12, 2)->nullable()->after('purchase_price_renteng');
            $table->decimal('purchase_price_karton', 12, 2)->nullable()->after('purchase_price_box');
            $table->decimal('purchase_price_pack', 12, 2)->nullable()->after('purchase_price_karton');
        });
        
        // Update stock_logs to track converted quantity
        Schema::table('stock_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_logs', 'quantity_in_base_unit')) {
                $table->integer('quantity_in_base_unit')->nullable()->after('unit_type');
            }
        });
        
        // Update product_batches to track unit type for incoming stock
        Schema::table('product_batches', function (Blueprint $table) {
            if (!Schema::hasColumn('product_batches', 'incoming_unit')) {
                $table->string('incoming_unit')->default('pcs')->after('quantity');
            }
            if (!Schema::hasColumn('product_batches', 'incoming_quantity')) {
                $table->integer('incoming_quantity')->nullable()->after('incoming_unit');
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
                'sell_karton',
                'sell_pack',
                'box_per_karton',
                'pcs_per_pack',
                'price_karton',
                'price_pack',
                'purchase_price_pcs',
                'purchase_price_renteng',
                'purchase_price_box',
                'purchase_price_karton',
                'purchase_price_pack',
            ]);
        });
        
        Schema::table('stock_logs', function (Blueprint $table) {
            if (Schema::hasColumn('stock_logs', 'quantity_in_base_unit')) {
                $table->dropColumn('quantity_in_base_unit');
            }
        });
        
        Schema::table('product_batches', function (Blueprint $table) {
            if (Schema::hasColumn('product_batches', 'incoming_unit')) {
                $table->dropColumn('incoming_unit');
            }
            if (Schema::hasColumn('product_batches', 'incoming_quantity')) {
                $table->dropColumn('incoming_quantity');
            }
        });
    }
};
