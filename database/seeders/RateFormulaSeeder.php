<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RateFormula;
use Illuminate\Support\Str;

class RateFormulaSeeder extends Seeder
{
    /**
     * Complete list of Keys => Titles.
     * You can edit titles here individually.
     */
    private const RATE_MAP = [
        // --- WHOLESALE ITEMS ---
        'wholesale_live_chicken_rate'   => 'Wholesale Live Chicken',
        'wholesale_mix_34_rate'         => 'Mix (No. 34)',
        'wholesale_mix_35_rate'         => 'Mix (No. 35)',
        'wholesale_mix_36_rate'         => 'Mix (No. 36)',
        'wholesale_mix_37_rate'         => 'Mix (No. 37)',
        'wholesale_chest_leg_38_rate'   => 'Chest Leg (No. 38)',
        'wholesale_drum_sticks_rate'    => 'Drum Sticks',
        'wholesale_chest_boneless_rate' => 'Chest Boneless',
        'wholesale_thigh_boneless_rate' => 'Thigh Boneless',
        'wholesale_kalagi_pot_rate'     => 'Kalagi Pot',
        'wholesale_chick_paw_rate'      => 'Chick Paws',

        // --- RETAIL ITEMS ---
        'retail_live_chicken_rate'      => 'Retail Live Chicken',
        'retail_mix_34_rate'            => 'Mix (No. 34)',
        'retail_mix_35_rate'            => 'Mix (No. 35)',
        'retail_mix_36_rate'            => 'Mix (No. 36)',
        'retail_mix_37_rate'            => 'Mix (No. 37)',
        'retail_chest_leg_38_rate'      => 'Chest Leg (No. 38)',
        'retail_drum_sticks_rate'       => 'Drum Sticks',
        'retail_chest_boneless_rate'    => 'Chest Boneless',
        'retail_thigh_boneless_rate'    => 'Thigh Boneless',
        'retail_kalagi_pot_rate'        => 'Kalagi Pot',
        'retail_chick_paw_rate'         => 'Chick Paws',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::RATE_MAP as $key => $title) {
            $channel = Str::startsWith($key, 'wholesale') ? 'wholesale' : 'retail';

            RateFormula::updateOrCreate(
                ['rate_key' => $key], 
                [
                    'title'    => $title,
                    'channel'  => $channel,
                    'multiply' => 1.0000,
                    'divide'   => 1.0000,
                    'plus'     => 0.00,
                    'minus'    => 0.00,
                    'status'   => true,
                ]
            );
        }
    }
}