<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $primaryKey = 'slider_id';
    protected $fillable = [
        'slider_name',
        'image'
    ];
}
