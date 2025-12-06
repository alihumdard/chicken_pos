<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // Table ka naam (Optional agar table ka naam 'transactions' hi hai)
    protected $table = 'transactions';

    // Mass assignment allow karne ke liye fields define karein
    protected $fillable = [
        'contact_id',
        'contact_type', // 'customer' or 'supplier'
        'date',
        'type',         // e.g., 'sale', 'payment', 'opening_balance'
        'description',
        'debit',
        'credit',
        'balance'
    ];

}