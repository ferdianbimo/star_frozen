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
        if (!Schema::hasColumn('transactions', 'checkout_time')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->string('checkout_time')->nullable()->after('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('transactions', 'checkout_time')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropColumn('checkout_time');
            });
        }
    }
};
