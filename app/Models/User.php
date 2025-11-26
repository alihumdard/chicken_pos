<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // <-- Import this

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'role',
        'user_pic',
        'com_name', 
        'com_pic',
        'country',  
        'zip_code',
        'city',     
        'state',    
        'otp',
        'reset_pswd_time',
        'reset_pswd_attempt',
        'staff_id',
        'sadmin_id',
        'status',
        'created_by',
        'updated_by',
        'subscribed_to_newsletter',     
        'terms_accepted_at',            
        'privacy_policy_accepted_at',   
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'subscribed_to_newsletter' => 'boolean',            
        'terms_accepted_at' => 'datetime',                   
        'privacy_policy_accepted_at' => 'datetime',          
    ];

    /**
     * The services that belong to the user.
     * Defines the many-to-many relationship.
     */
    public function services(): BelongsToMany   
    {
        return $this->belongsToMany(Service::class, 'service_user', 'user_id', 'service_id');
    }
}