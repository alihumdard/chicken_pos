<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'base_effective_cost', // The cost from the selected purchase that day
        'wholesale_rate',
        'permanent_rate',
        'retail_mix_rate',
        'retail_chest_rate',
        'retail_thigh_rate',
        'retail_piece_rate',
        'is_active', // Flag to mark this as the currently active rate

        'manual_base_cost', 
        'live_chicken_rate',
        'wholesale_mix_rate',
        'wholesale_chest_rate',
        'wholesale_thigh_rate',
        'wholesale_customer_piece_rate',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}