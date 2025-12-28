<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Shop;

class ShopSeeder extends Seeder
{
    public function run()
    {
        Shop::create(['name' => 'Wholesale (Live Hub)','type' => 'wholesale', 'location' => 'Kot momin', 'is_default' => true]);
        Shop::create(['name' => 'Retail (Meat Shop)','type' => 'retail', 'location' => 'Kot momin', 'is_default' => false]);
    }
}