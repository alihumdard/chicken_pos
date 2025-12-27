<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Added for explicit relation

class Supplier extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'type',
        'current_balance', 
        'phone',
        'address',
        'current_balance',
    ];
    
    /**
     * Get the purchases for the supplier.
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }
}