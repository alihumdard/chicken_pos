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
    'customer_id', // Ensure ye column DB mein isi naam se hai
    'supplier_id', 
    'date',
    'type', 
    'description',
    'debit',
    'credit',
    'balance',
];

public function customer()
{
    // Agar DB mein column ka naam 'customer_id' hai toh:
    return $this->belongsTo(Customer::class, 'customer_id');
}

public function sale()
{
    // Description se ID nikalne ke bajaye, hum controller mein filter karenge
    return $this->belongsTo(Sale::class, 'id'); 
}

}
