<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RateFormula extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 
        'rate_key', 
        'icon_url', 
        'channel',
        'multiply', 
        'divide', 
        'plus', 
        'minus', 
        'status'
    ];
    
    protected $casts = [
        'multiply' => 'float',
        'divide'   => 'float',
        'plus'     => 'float',
        'minus'    => 'float',
    ];
}