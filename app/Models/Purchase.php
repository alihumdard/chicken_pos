<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Added for explicit relation

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'driver_no',
        'gross_weight',
        'dead_qty',
        'dead_weight',
        'shrink_loss',
        'net_live_weight',
        'buying_rate',
        'total_payable',
        'effective_cost',
        'total_kharch',
        'shop_id'
    ];
    
    // Define relationship to get supplier name
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}