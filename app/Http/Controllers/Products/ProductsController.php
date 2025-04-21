<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\ProductsRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\productResource;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
class ProductsController extends Controller
{
    public function index(Request $request){

        $products = Product::query()
            ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            })
            ->with('categories')
            ->latest()
            ->paginate(PAGINATION)
            ->withQueryString();


        return inertia('Products/Index',[
            'categories'=>CategoryResource::collection(Category::all()),
             'products'=>$products,
            'filters'=>$request->only(['search']),

        ]);
    }
    public function store(ProductsRequest $request){



        $request->validated();

        DB::transaction(function () use ($request) {
        $url = $request->file('image')->store('products', 'public');

            $product = Product::create([
                'name'=>$request->name,
                'description'=>$request->description,
                'price'=>$request->price,
                'quantity'=>$request->quantity,
                'image'=>$url,
                'min_quantity'=>$request->min_quantity,
            ]);
            $product->categories()->attach(collect($request->category)->pluck('id'));
        });


        return back();
    }
    public function edit($id){
        $product = Product::with('categories')->findOrFail($id);
        return inertia('Products/Edit',[
            'categories'=>CategoryResource::collection(Category::all()),
            'product'=>new productResource($product),
        ]);
    }

    public function update(ProductsRequest $request,$id){
        $request->validated();
        $product = Product::findOrFail($id);
        DB::transaction(function () use ($request, $product) {
            if ($request->hasFile('image')) {
                $url = $request->file('image')->store('products', 'public');
                $product->update([
                    'name'=>$request->name,
                    'description'=>$request->description,
                    'price'=>$request->price,
                    'quantity'=>$request->quantity,
                    'image'=>$url,
                    'min_quantity'=>$request->min_quantity,
                ]);
            }else{
                $product->update([
                    'name'=>$request->name,
                    'description'=>$request->description,
                    'price'=>$request->price,
                    'quantity'=>$request->quantity,
                    'min_quantity'=>$request->min_quantity,
                ]);
            }
            $product->categories()->sync(collect($request->category)->pluck('id'));
        });

        return to_route('products.index');
    }

    public function destroy($id){
        $product = Product::findOrFail($id);
        $product->categories()->detach();
        $product->delete();
        return back();
    }
}
