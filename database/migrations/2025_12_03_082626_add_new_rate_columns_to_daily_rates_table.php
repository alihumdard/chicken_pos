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
        // 🟢 Adding all the new columns required by the RateController
        Schema::table('daily_rates', function (Blueprint $table) {
            $table->decimal('live_chicken_rate', 10, 2)->default(0.00)->after('permanent_rate');
            $table->decimal('wholesale_hotel_mix_rate', 10, 2)->default(0.00)->after('live_chicken_rate');
            $table->decimal('wholesale_hotel_chest_rate', 10, 2)->default(0.00)->after('wholesale_hotel_mix_rate');
            $table->decimal('wholesale_hotel_thigh_rate', 10, 2)->default(0.00)->after('wholesale_hotel_chest_rate');
            $table->decimal('wholesale_customer_piece_rate', 10, 2)->default(0.00)->after('wholesale_hotel_thigh_rate');
            // Note: retail_mix_rate, retail_chest_rate, retail_thigh_rate, retail_piece_rate should already exist from the initial migration, but if they are new, they should be added here as well. Assuming they exist based on the column order.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_rates', function (Blueprint $table) {
            $table->dropColumn([
                'live_chicken_rate',
                'wholesale_hotel_mix_rate',
                'wholesale_hotel_chest_rate',
                'wholesale_hotel_thigh_rate',
                'wholesale_customer_piece_rate',
            ]);
        });
    }
};