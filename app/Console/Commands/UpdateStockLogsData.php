<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StockLog;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class UpdateStockLogsData extends Command
{
    protected $signature = 'stock:update-all';
    protected $description = 'Update all stock_logs with proper transaction_type, unit_price, and total_value';

    public function handle()
    {
        $this->info('Starting to update stock logs...');
        
        // Get all stock logs with negative change (stock out)
        $stockLogs = StockLog::where('change', '<', 0)->get();
        
        $updated = 0;
        $skipped = 0;
        
        foreach ($stockLogs as $log) {
            $product = Product::find($log->product_id);
            
            if (!$product) {
                $this->warn("Product not found for stock_log ID: {$log->id}");
                $skipped++;
                continue;
            }
            
            // Calculate unit price and total value
            $unitPrice = $product->price; // Use current selling price
            $totalValue = abs($log->change) * $unitPrice;
            
            // Update the record
            $log->update([
                'transaction_type' => 'sale',
                'unit_price' => $unitPrice,
                'total_value' => $totalValue
            ]);
            
            $this->line("Updated: {$product->name} | Qty: {$log->change} | Price: Rp " . number_format($unitPrice, 0, ',', '.') . " | Total: Rp " . number_format($totalValue, 0, ',', '.'));
            $updated++;
        }
        
        $this->newLine();
        $this->info("✅ Update completed!");
        $this->info("Updated: {$updated} records");
        $this->info("Skipped: {$skipped} records");
        
        // Show total
        $totalIncome = StockLog::where('transaction_type', 'sale')->sum('total_value');
        $this->info("Total Pemasukan: Rp " . number_format($totalIncome, 0, ',', '.'));
        
        return 0;
    }
}
