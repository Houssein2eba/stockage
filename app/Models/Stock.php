<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $guarded = ['id'];

    public function products(){
        return $this->hasMany(Product::class,'product_stocks')
        ->using(ProductStock::class)
        ->withPivot(['quantity','expiry_date'])
        ->withTimestamps();
    }

}
