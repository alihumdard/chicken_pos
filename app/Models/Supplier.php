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
        // Add other columns like 'phone', 'address', etc., if they exist on your table
    ];
    
    /**
     * Get the purchases for the supplier.
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }
}