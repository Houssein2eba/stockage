<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasUuids;
    protected $guarded = ['id'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
}
