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
        Schema::table('stock_logs', function (Blueprint $table) {
            $table->decimal('unit_price', 15, 2)->default(0)->after('change')->comment('Harga per unit saat transaksi');
            $table->decimal('total_value', 15, 2)->default(0)->after('unit_price')->comment('Total nilai transaksi (quantity * price)');
            $table->string('transaction_type', 20)->default('manual')->after('total_value')->comment('Type: sale, purchase, adjustment, manual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_logs', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'total_value', 'transaction_type']);
        });
    }
};
