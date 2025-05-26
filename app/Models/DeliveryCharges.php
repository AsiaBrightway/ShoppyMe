<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryCharges extends Model
{
    protected $primaryKey = 'delivery_charges_id';
    protected $fillable = [
        'township_id',
        'delivery_fee'
    ];

    public function township()
    {
        return $this->belongsTo(Township::class, 'township_id', 'township_id');
    }
}
