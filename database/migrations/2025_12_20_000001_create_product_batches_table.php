<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('batch_code')->nullable(); // Optional batch code for tracking
            $table->integer('quantity')->default(0); // Available stock in this batch
            $table->decimal('purchase_price', 10, 2)->nullable(); // Purchase price for this batch
            $table->date('date_received'); // Date when batch was received
            $table->date('expiration_date')->nullable(); // Expiration date for this batch
            $table->text('notes')->nullable(); // Additional notes
            $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_active')->default(true); // Active/inactive status
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['product_id', 'expiration_date']);
            $table->index(['product_id', 'is_active']);
        });
        
        // Add batch_id to stock_logs for tracking batch-specific changes
        Schema::table('stock_logs', function (Blueprint $table) {
            $table->foreignId('batch_id')->nullable()->after('product_id')->constrained('product_batches')->onDelete('set null');
        });
        
        // Add batch_id to transaction_items for tracking which batch was sold
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->foreignId('batch_id')->nullable()->after('product_id')->constrained('product_batches')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropColumn('batch_id');
        });
        
        Schema::table('stock_logs', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropColumn('batch_id');
        });
        
        Schema::dropIfExists('product_batches');
    }
};
