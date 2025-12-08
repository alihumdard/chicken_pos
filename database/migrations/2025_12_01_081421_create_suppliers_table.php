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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            // 🟢 UPDATED: Name is NOT unique anymore
            $table->string('name', 100); 
            
            $table->string('contact_person')->nullable();
            
            // 🟢 UPDATED: Phone is unique but nullable
            $table->string('phone')->nullable()->unique();
            
            $table->decimal('current_balance', 15, 2)->default(0.00); 
            
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