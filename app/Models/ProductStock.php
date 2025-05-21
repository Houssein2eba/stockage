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
}
