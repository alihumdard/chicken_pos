<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RateFormula extends Model
{
    use HasFactory;

    protected $fillable = [
        'rate_key',
        'multiply',
        'divide',
        'plus',
        'minus',
    ];
    
    protected $casts = [
        'multiply' => 'float',
        'divide'   => 'float',
        'plus'     => 'float',
        'minus'    => 'float',
    ];
}