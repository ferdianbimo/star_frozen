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
        Schema::create('receipt_settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name')->default('Star Frozen');
            $table->text('store_address')->nullable();
            $table->string('store_phone')->nullable();
            $table->string('store_email')->nullable();
            $table->string('logo')->nullable();
            $table->text('header_text')->nullable();
            $table->text('footer_text')->nullable();
            $table->boolean('show_logo')->default(true);
            $table->boolean('show_address')->default(true);
            $table->boolean('show_phone')->default(true);
            $table->boolean('show_cashier_name')->default(true);
            $table->boolean('show_thank_you')->default(true);
            $table->string('thank_you_text')->default('TERIMAKASIH TELAH BERBELANJA');
            $table->string('receipt_width')->default('320px');
            $table->timestamps();
        });

        // Insert default settings
        DB::table('receipt_settings')->insert([
            'store_name' => 'Star Frozen',
            'store_address' => 'Jl. Abdul Fatah Barat, RT.02/RW.02, Dusun Bungur, Bungur, Kec. Karangrejo, Kabupaten Tulungagung, Jawa Timur 66253',
            'store_phone' => '0812-3456-7890',
            'footer_text' => 'Printed by Star Frozen POS',
            'thank_you_text' => 'TERIMAKASIH TELAH BERBELANJA',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_settings');
    }
};
