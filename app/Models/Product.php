<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasUuids;
    
    protected $guarded = ['id'];

    public function categories(){
        return $this->belongsToMany(Category::class, 'category_product', 'product_id', 'category_id');
    }

}
