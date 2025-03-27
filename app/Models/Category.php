<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasUuids;

    protected $guarded = ['id'];

    public function products(){
        return $this->belongsToMany(Product::class, 'category_product', 'category_id', 'product_id');
    }
}
