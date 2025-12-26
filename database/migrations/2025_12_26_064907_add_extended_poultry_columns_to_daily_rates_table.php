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
   public function up(): void
{
    Schema::table('daily_rates', function (Blueprint $table) {
        $table->decimal('wholesale_chest_and_leg_pieces', 10, 2)->default(0.00);
        $table->decimal('wholesale_drum_sticks', 10, 2)->default(0.00);
        $table->decimal('wholesale_chest_boneless', 10, 2)->default(0.00);
        $table->decimal('wholesale_thigh_boneless', 10, 2)->default(0.00);
        $table->decimal('wholesale_kalagi_pot_gardan', 10, 2)->default(0.00);
        
        // Also adding retail versions for consistency
        $table->decimal('retail_chest_and_leg_pieces', 10, 2)->default(0.00);
        $table->decimal('retail_drum_sticks', 10, 2)->default(0.00);
        $table->decimal('retail_chest_boneless', 10, 2)->default(0.00);
        $table->decimal('retail_thigh_boneless', 10, 2)->default(0.00);
        $table->decimal('retail_kalagi_pot_gardan', 10, 2)->default(0.00);
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_rates', function (Blueprint $table) {
            //
        });
    }
};
