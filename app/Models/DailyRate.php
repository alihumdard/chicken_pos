<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'base_effective_cost',
        'manual_base_cost', 
        'is_active',

        // Wholesale Rates
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

        // Retail Rates
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
        
        'permanent_rate',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}