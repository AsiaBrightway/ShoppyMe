<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $primaryKey = 'store_id';
    protected $fillable = [
        'store_name',
        'owner_name',
        'address',
        'phone_number',
        'email',
    ];

    /**
     * Get the admins associated with the store.
     */
    public function admins()
    {
        return $this->hasMany(Admin::class, 'store_id', 'store_id');
    }
}