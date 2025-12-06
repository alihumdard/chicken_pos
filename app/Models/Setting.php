<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', // Necessary if you want to allow mass assignment of ID for the first record
        'shop_name',
        'address',
        'phone_number',
        'logo_url',
    ];

    /**
     * Fetches the single global settings record (ID 1), creating it if it doesn't exist.
     *
     * @return \App\Models\Setting
     */
    public static function getGlobalSettings()
    {
        // Find the record with ID 1, or create it if it doesn't exist.
        // The store method expects to find or create this single record.
        return self::firstOrCreate(['id' => 1]);
    }
}