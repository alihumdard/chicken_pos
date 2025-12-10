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
            $table->decimal('wholesale_mix_rate', 10, 2)->default(0.00)->after('live_chicken_rate');
            $table->decimal('wholesale_chest_rate', 10, 2)->default(0.00)->after('wholesale_mix_rate');
            $table->decimal('wholesale_thigh_rate', 10, 2)->default(0.00)->after('wholesale_chest_rate');
            $table->decimal('wholesale_customer_piece_rate', 10, 2)->default(0.00)->after('wholesale_thigh_rate');
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
                'wholesale_mix_rate',
                'wholesale_chest_rate',
                'wholesale_thigh_rate',
                'wholesale_customer_piece_rate',
            ]);
        });
    }
};