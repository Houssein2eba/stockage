<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasUuids;
    
    protected $guarded = ['id','created_at','updated_at'];

    protected $fillable = [
        'reference',
        'client_id',
        'payment_id',
        'total_amount',
        'status'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class,'order_details')
            ->using(OrderDetail::class)
            ->withPivot(['quantity','total_amount'])
            ->withTimestamps();
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function calculateTotalAmount()
    {
        return $this->products()->sum('total_amount');
    }
}
