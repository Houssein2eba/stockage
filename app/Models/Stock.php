<?php

namespace App\Models;

use Carbon\Carbon;
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
        ->withPivot(['stock_date','type','products_quantity'])

        ->withTimestamps();
    }



}
