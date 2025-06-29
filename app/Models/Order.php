<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Order extends Model
{
    use HasUuids;

    protected $guarded = ['id','created_at','updated_at'];



    protected $appends = ['total_amount'];

    

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_details')
            ->using(OrderDetail::class)
            ->withPivot(['quantity','total_amount'])
            ->withTimestamps();
    }




    public function order_details()
{
    return $this->hasMany(OrderDetail::class);
}



    public function getTotalAmountAttribute()
    {
        return $this->products()->sum('order_details.total_amount');
    }

    public static function generateReference(): string
    {
        return (string) \Illuminate\Support\Str::uuid();
    }

    public function totalAmount():float
    {
        $totalAmount = DB::table('order_details')->where('order_id', $this->id)->sum('total_amount');
        return (float) $totalAmount;
    }

}
