<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StockLog;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class SyncStockLogsWithPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:sync-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all stock logs with proper prices and transaction values';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting sync process...');
        
        // Update all stock out records (sales)
        $salesLogs = StockLog::where('change', '<', 0)
            ->where(function($query) {
                $query->whereNull('transaction_type')
                      ->orWhere('transaction_type', '')
                      ->orWhere('transaction_type', 'manual');
            })
            ->get();
        
        $updated = 0;
        foreach ($salesLogs as $log) {
            $log->update(['transaction_type' => 'sale']);
            $updated++;
        }
        
        $this->info("Updated {$updated} records to 'sale' type");
        
        // Update prices and values for all sale records
        $saleLogs = StockLog::where('transaction_type', 'sale')
            ->with('product')
            ->get();
        
        $priceUpdated = 0;
        foreach ($saleLogs as $log) {
            if ($log->product) {
                $unitPrice = $log->product->price;
                $totalValue = abs($log->change) * $unitPrice;
                
                $log->update([
                    'unit_price' => $unitPrice,
                    'total_value' => $totalValue
                ]);
                
                $priceUpdated++;
                $this->line("Updated: {$log->product->name} - {$log->change} pack x Rp" . number_format($unitPrice, 0) . " = Rp" . number_format($totalValue, 0));
            }
        }
        
        $this->info("Updated prices for {$priceUpdated} sale records");
        $this->info('Sync completed successfully!');
        
        return 0;
    }
}
