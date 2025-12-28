<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'customer_id',
        'total_amount',
        'payment_status', // e.g., 'credit', 'paid', 'partial'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    /**
     * A Sale belongs to a Customer.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * A Sale has many Sale Items (products).
     */
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}