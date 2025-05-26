<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $primaryKey = 'city_id';
    protected $fillable = [
        'name'
    ];
    public function townships()
    {
        return $this->hasMany(Township::class, 'city_id', 'city_id');
    }
}
