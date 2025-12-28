<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model {
    protected $fillable = ['from_shop_id', 'to_shop_id', 'weight', 'date', 'description'];

    public function fromShop() { return $this->belongsTo(Shop::class, 'from_shop_id'); }
    public function toShop() { return $this->belongsTo(Shop::class, 'to_shop_id'); }
}