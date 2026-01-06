<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $fillable = ['name', 'type','is_default','location'];
    
    public function getCurrentStockAttribute() {
        $purchased = Purchase::where('shop_id', $this->id)->sum('net_live_weight') ?? 0;
        
        $transferredIn = StockTransfer::where('to_shop_id', $this->id)->sum('weight') ?? 0;
        $transferredOut = StockTransfer::where('from_shop_id', $this->id)->sum('weight') ?? 0;

        $sold = SaleItem::join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->where('sales.shop_id', $this->id)
            ->whereNull('sales.stock_transfer_id') 
            ->sum('sale_items.weight_kg') ?? 0;

        return ($purchased + $transferredIn) - ($sold + $transferredOut);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}