<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RateFormula;

class RateFormulaSeeder extends Seeder
{
    private const RATE_KEYS = [
        'wholesale_rate',
        'live_chicken_rate',
        'wholesale_mix_rate',
        'wholesale_chest_rate',
        'wholesale_thigh_rate',
        'wholesale_customer_piece_rate',
        'retail_mix_rate',
        'retail_chest_rate',
        'retail_thigh_rate',
        'retail_piece_rate',
        'purchase_effective_cost', 
        'permanent_rate' 
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::RATE_KEYS as $key) {
            // Create or update the formula entry with default values
            RateFormula::updateOrCreate(
                ['rate_key' => $key],
                [
                    'multiply' => 1.0000,
                    'divide'   => 1.0000,
                    'plus'     => 0.0000,
                    'minus'    => 0.0000,
                ]
            );
        }
    }
}