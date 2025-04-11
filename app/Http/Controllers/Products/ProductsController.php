<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\ProductsRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function create(){
        return inertia('Products/Create',['categories'=>Category::all()]);
    }
    public function store(ProductsRequest $request){
        dd($request->all());
    }
}
