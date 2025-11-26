<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title',
        'body',
        'url',
        'status',
        'client_id',
        'name',
        'email',
        'email_forward',
        'message',
        'type',
        'display_date',
        'deleted_at',
    ];
}
