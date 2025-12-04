<?php
// database/migrations/YYYY_MM_DD_HHMMSS_create_rate_formulas_table.php

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
        Schema::create('rate_formulas', function (Blueprint $table) {
            $table->id();
            // The rate_key corresponds to the keys in RateController::RATE_MARGINS
            $table->string('rate_key')->unique()->comment('The key for the rate being modified (e.g., wholesale_rate)');
            $table->decimal('multiply', 8, 4)->default(1.0000);
            $table->decimal('divide', 8, 4)->default(1.0000);
            $table->decimal('plus', 8, 4)->default(0.0000);
            $table->decimal('minus', 8, 4)->default(0.0000);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_formulas');
    }
};