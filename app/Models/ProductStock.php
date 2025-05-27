<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductStock extends Pivot
{
    use HasUuids;
    protected $table = 'product_stocks';
    protected $guarded = ['id'];

    public function product(){
        return $this->belongsTo(Product::class);
    }
    public function stock(){
        return $this->belongsTo(Stock::class);
    }
    public function getbenefitsAttribute():float
    {
        return $this->product->price - $this->product->cost;
    }
}
