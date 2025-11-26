<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing stock out records (negative change) to 'sale' type
        DB::table('stock_logs')
            ->where('change', '<', 0)
            ->where(function($query) {
                $query->whereNull('transaction_type')
                      ->orWhere('transaction_type', '')
                      ->orWhere('transaction_type', 'manual');
            })
            ->update([
                'transaction_type' => 'sale',
                'updated_at' => now()
            ]);

        // Update unit_price and total_value for old records that don't have them
        $logs = DB::table('stock_logs')
            ->join('products', 'stock_logs.product_id', '=', 'products.id')
            ->where('stock_logs.transaction_type', 'sale')
            ->where(function($query) {
                $query->where('stock_logs.unit_price', 0)
                      ->orWhereNull('stock_logs.unit_price');
            })
            ->select('stock_logs.id', 'stock_logs.change', 'products.price')
            ->get();

        foreach ($logs as $log) {
            DB::table('stock_logs')
                ->where('id', $log->id)
                ->update([
                    'unit_price' => $log->price,
                    'total_value' => abs($log->change) * $log->price,
                    'updated_at' => now()
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally revert changes
        DB::table('stock_logs')
            ->where('transaction_type', 'sale')
            ->update(['transaction_type' => 'manual']);
    }
};
