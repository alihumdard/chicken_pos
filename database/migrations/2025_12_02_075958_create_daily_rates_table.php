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
        Schema::create('daily_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('base_effective_cost', 10, 2); // Base cost reference
            $table->decimal('wholesale_rate', 10, 2);
            $table->decimal('permanent_rate', 10, 2);
            $table->decimal('retail_mix_rate', 10, 2);
            $table->decimal('retail_chest_rate', 10, 2);
            $table->decimal('retail_thigh_rate', 10, 2);
            $table->decimal('retail_piece_rate', 10, 2);
            $table->boolean('is_active')->default(false); // Only one should be true at any time
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_rates');
    }
};