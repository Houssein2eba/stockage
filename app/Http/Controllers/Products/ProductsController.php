<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\ProductsRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\productResource;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
class ProductsController extends Controller
{
    public function index(){
        $products = Product::with('categories')->get();
       
        
        
        return inertia('Products/Index',[
            'categories'=>CategoryResource::collection(Category::all()),
             'products'=>$products,
        
        ]);
    }
    public function store(ProductsRequest $request){



        $request->validated();
        
        $url = $request->file('image')->store('products', 'public');

        $product = Product::create([
            'name'=>$request->name,
            'description'=>$request->description,
            'price'=>$request->price,
            'quantity'=>$request->quantity,
            'min_quantity'=>$request->min_quantity,
            'image'=>$url
        ]);
        $product->categories()->attach(collect($request->category)->pluck('id'));
        return back();
    }

    public function destroy($id){
        $product = Product::findOrFail($id);
        $product->categories()->detach();
        $product->delete();
        return back();
    }
}
