<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Payment extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'logo'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
