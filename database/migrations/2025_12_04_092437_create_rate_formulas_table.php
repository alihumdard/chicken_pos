<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('rate_formulas', function (Blueprint $table) {
        $table->id();
        $table->string('title');       // Friendly Name: "Mix (No. 34)"
        $table->string('rate_key');    // Machine Key: "wholesale_mix_rate"
        $table->string('icon_url')->nullable(); 
        $table->enum('channel', ['wholesale', 'retail']); //
        $table->decimal('multiply', 10, 4)->default(1.0000); //
        $table->decimal('divide', 10, 4)->default(1.0000);
        $table->decimal('plus', 10, 2)->default(0.00);
        $table->decimal('minus', 10, 2)->default(0.00);
        $table->boolean('status')->default(true); // Active/Inactive
        $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rate_formulas');
    }
};
