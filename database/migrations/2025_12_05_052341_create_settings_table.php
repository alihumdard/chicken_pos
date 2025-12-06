<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Required for manual DB insert

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This table will store all general configuration fields
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('shop_name')->nullable();
            $table->string('address')->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('logo_url')->nullable()->comment('Path to the uploaded logo image.');
            $table->timestamps();
        });
        
        // CRITICAL: Insert the single row for configuration settings immediately
        // The application will always update this row (ID 1).
        DB::table('settings')->insert([
            'id' => 1,
            'shop_name' => 'RANA POS', // Default placeholder name
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};