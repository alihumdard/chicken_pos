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
        Schema::table('daily_rates', function (Blueprint $table) {
            // 1. Add the new flexible JSON column
            // We place it after 'manual_base_cost' so it sits nicely in the middle
            $table->json('rate_values')->nullable()->after('manual_base_cost');

            // 2. Drop ALL the specific static rate columns
            // We keep: id, supplier_id, base_effective_cost, manual_base_cost, is_active, timestamps
            $table->dropColumn([
                // Wholesale Columns
                'wholesale_rate',
                'wholesale_mix_rate',
                'wholesale_chest_rate',
                'wholesale_thigh_rate',
                'wholesale_customer_piece_rate',
                'wholesale_chest_and_leg_pieces',
                'wholesale_drum_sticks',
                'wholesale_chest_boneless',
                'wholesale_thigh_boneless',
                'wholesale_kalagi_pot_gardan',

                // Retail Columns
                'live_chicken_rate',
                'retail_mix_rate',
                'retail_chest_rate',
                'retail_thigh_rate',
                'retail_piece_rate',
                'retail_chest_and_leg_pieces',
                'retail_drum_sticks',
                'retail_chest_boneless',
                'retail_thigh_boneless',
                'retail_kalagi_pot_gardan',
                
                // Misc
                'permanent_rate',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_rates', function (Blueprint $table) {
            // Drop JSON column
            $table->dropColumn('rate_values');

            // Re-add the columns if we rollback (all nullable/default 0 to be safe)
            $table->decimal('wholesale_rate', 10, 2)->default(0);
            $table->decimal('permanent_rate', 10, 2)->default(0);
            $table->decimal('live_chicken_rate', 10, 2)->default(0);
            
            // ... (You would list all other columns here if you really need rollback safety)
        });
    }
};