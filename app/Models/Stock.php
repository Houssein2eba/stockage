<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasUuids,HasFactory;
    protected $guarded = ['id'];

    public function products(){
        return $this->belongsToMany(Product::class,'product_stocks')
        ->using(ProductStock::class)
        ->withPivot(['quantity','expiry_date'])
        ->withTimestamps();
    }

}
