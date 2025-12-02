<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_category', // e.g., 'Whole', 'Chest', 'Thigh'
        'weight_kg',
        'rate_pkr',
        'line_total',
    ];

    protected $casts = [
        'weight_kg' => 'decimal:3',
        'rate_pkr' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    /**
     * A Sale Item belongs to a Sale.
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}