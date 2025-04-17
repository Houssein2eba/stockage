<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Models\CategoryProduct;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
class Product extends Model
{
    use HasUuids;
    public $timestamps = false;
    protected $guarded = ['id','created_at','updated_at'];

    public function categories(){
        return $this->
        belongsToMany(Category::class)
        ->using(CategoryProduct::class)
        ->withTimestamps();
    }
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($product) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
        });
    }

}
