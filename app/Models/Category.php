<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\Product;
use App\Models\CategoryProduct;

class Category extends Model
{
    use HasUuids;

    protected $guarded = ['id','created_at','updated_at'];

public $timestamps = false;


    public function products(){
        return $this->belongsToMany(Product::class)
        ->using(CategoryProduct::class)
        ->withTimestamps()
        ;
    }
}
