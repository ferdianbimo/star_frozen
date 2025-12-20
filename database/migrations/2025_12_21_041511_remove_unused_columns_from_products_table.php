<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Remove unused columns:
     * - date_in: Not used, batches have their own date_received
     * - expiration_date: Not used, batches have their own expiration_date
     * - renteng_per_box: Replaced by box_contains_unit/qty
     * - box_per_karton: Replaced by karton_contains_unit/qty
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'date_in',
                'expiration_date',
                'renteng_per_box',
                'box_per_karton',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->date('date_in')->nullable();
            $table->date('expiration_date')->nullable();
            $table->integer('renteng_per_box')->nullable();
            $table->integer('box_per_karton')->nullable();
        });
    }
};
