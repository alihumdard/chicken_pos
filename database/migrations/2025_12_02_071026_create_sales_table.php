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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            // Foreign key linking to the customers table
            $table->foreignId('customer_id')->constrained('customers');
            $table->decimal('total_amount', 15, 2)->default(0.00);
            $table->string('payment_status', 20)->default('credit'); // e.g., 'credit', 'paid', 'partial'
            $table->timestamps();
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            // Foreign key linking to the sales table
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            $table->string('product_category', 50); // e.g., 'Whole', 'Chest', 'Thigh'
            $table->decimal('weight_kg', 8, 3); // Weight up to 99999.999 kg
            $table->decimal('rate_pkr', 10, 2);
            $table->decimal('line_total', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
    }
};