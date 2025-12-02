<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Added for seeding

return new class extends Migration
{
    /**
     * Run the migrations.
     * Note: Assumes 'suppliers' table already exists.
     */
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            // 1. Truck Details
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('driver_no')->nullable();
            
            // 2. Weight Details (Kilograms and Pieces)
            $table->decimal('gross_weight', 10, 2);
            $table->unsignedInteger('dead_qty')->default(0);
            $table->decimal('dead_weight', 10, 2)->default(0);
            $table->decimal('shrink_loss', 10, 2)->default(0);
            $table->decimal('net_live_weight', 10, 2); // Calculated value
            
            // 3. Financial Details
            $table->decimal('buying_rate', 10, 2);
            $table->decimal('total_payable', 10, 2); // Calculated value
            $table->decimal('effective_cost', 10, 2); // Calculated value (for reference)

            // 4. Date and Timestamps
            $table->date('purchase_date')->default(now()); // Explicitly adding a purchase date for filtering
            $table->timestamps();
        });

     
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};