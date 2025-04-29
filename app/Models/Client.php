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
    public function products()
{
    return $this->hasManyThrough(Product::class, Order::class, 'client_id', 'id', 'id', 'order_id')
        ->join('order_details', 'products.id', '=', 'order_details.product_id')
        ->select('products.*');
}

}
