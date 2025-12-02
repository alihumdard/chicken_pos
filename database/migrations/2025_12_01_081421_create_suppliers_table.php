<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Added for DB::table seeding

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique(); // Use 100 length and ensure unique name
            $table->string('contact_person')->nullable(); // Contact person
            $table->string('phone')->nullable(); // Phone number
            
            // This is for storing the running balance, using 15, 2 for precision
            $table->decimal('current_balance', 15, 2)->default(0); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};