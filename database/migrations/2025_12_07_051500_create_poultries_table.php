<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('poultries', function (Blueprint $table) {
            $table->id();
            $table->date('entry_date');
            $table->string('batch_no')->nullable(); // e.g., Batch-001
            $table->integer('quantity'); // Number of birds
            $table->decimal('total_weight', 10, 2); // Total weight in KG
            $table->decimal('cost_price', 10, 2); // Total cost
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poultries');
    }
};