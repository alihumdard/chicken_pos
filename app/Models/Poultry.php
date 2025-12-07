<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poultry extends Model
{
    use HasFactory;

    protected $fillable = [
        'entry_date',
        'batch_no',
        'quantity',
        'total_weight',
        'cost_price',
        'description',
    ];
}