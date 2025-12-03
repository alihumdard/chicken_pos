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
        Schema::table('daily_rates', function (Blueprint $table) {
            // Add the new column to store the user-specified override rate.
            $table->decimal('manual_base_cost', 8, 2)->default(0.00)->after('base_effective_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_rates', function (Blueprint $table) {
            $table->dropColumn('manual_base_cost');
        });
    }
};