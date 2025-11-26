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
        Schema::table('transactions', function (Blueprint $table) {
            // Add missing columns
            $table->string('invoice_number')->nullable()->after('transaction_number');
            $table->decimal('subtotal', 15, 2)->default(0)->after('user_id');
            $table->decimal('tax', 15, 2)->default(0)->after('subtotal');
            $table->decimal('discount', 15, 2)->default(0)->after('tax');
            $table->decimal('total', 15, 2)->default(0)->after('discount');
            $table->decimal('profit', 15, 2)->default(0)->after('total');
            $table->string('status')->default('completed')->after('change_amount');
            $table->text('notes')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'invoice_number',
                'subtotal',
                'tax',
                'discount',
                'total',
                'profit',
                'status',
                'notes'
            ]);
        });
    }
};
