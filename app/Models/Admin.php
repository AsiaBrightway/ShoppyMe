<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $primaryKey = 'admin_id';

    protected $fillable = [
        'user_name',
        'password',
        'store_id',
        'is_active',
    ];

    protected $hidden = [
        'password', // Hide password in API responses
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the store associated with the admin.
     */
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'store_id');
    }
}
