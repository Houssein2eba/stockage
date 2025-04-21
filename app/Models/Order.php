<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    
    protected $guarded = ['id','created_at','updated_at'];
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->using(OrderDetail::class)
            ->withPivot(['quantity','total_amount'])
            ->withTimestamps();
    }
}
