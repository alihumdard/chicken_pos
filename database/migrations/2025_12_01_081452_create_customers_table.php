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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            // 🟢 UPDATED: Name is NOT unique anymore (allows duplicates)
            $table->string('name', 100); 
            
            $table->string('contact_person')->nullable();
            
            // 🟢 UPDATED: Phone is unique (no duplicate numbers) but nullable (can be empty)
            $table->string('phone')->nullable()->unique();
            
            $table->text('address')->nullable();
            
            // 15 digits total, 2 decimal places (e.g., 1234567890123.00)
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