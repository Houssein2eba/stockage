<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\ProductsRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(){
        return inertia('Products/Index',['categories'=>CategoryResource::collection(Category::all())]);
    }
    public function store(ProductsRequest $request){
        dd($request->all());
    }
}
