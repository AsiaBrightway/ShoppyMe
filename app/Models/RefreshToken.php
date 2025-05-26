<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RefreshToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'token',
        'expires_at',
    ];

    protected $dates = ['expires_at'];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
