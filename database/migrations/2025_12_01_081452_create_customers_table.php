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
        // This table stores customer details for the sales point.
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique(); // Business/Customer Name, enforced as unique
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            // Store the outstanding balance (can be positive or negative)
            // Using 15 total digits with 2 decimal places for high-precision monetary values
            $table->decimal('current_balance', 15, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};