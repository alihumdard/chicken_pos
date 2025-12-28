<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyRate extends Model
{
    use HasFactory;

    // 🟢 Only these columns exist in the DB now
    protected $fillable = [
        'supplier_id',
        'base_effective_cost',
        'manual_base_cost', 
        'rate_values', // This stores: ['wholesale_mix_rate' => 350, 'retail_chest_rate' => 500, ...]
        'is_active',
    ];

    // 🟢 Automatically cast JSON to Array
    protected $casts = [
        'is_active'   => 'boolean',
        'rate_values' => 'array', 
        'base_effective_cost' => 'decimal:2',
        'manual_base_cost'    => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}