<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Http\Resources\productResource;
use App\Http\Resources\StockResource;
use App\Models\Stock;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StockController extends Controller
{
    public function index(Request $request){
        $stocks=Stock::with('products')->when($request->search, function ($query, $search) {
            $query->where('name', 'LIKE', "%{$search}%");
        })->paginate(PAGINATION)->withQueryString();
        return Inertia::render('Stock/Index',[
            'stocks'=>StockResource::collection($stocks),
            'search'=>$request->search
        ]);
    }

    public function show(Request $request,$id){
        $stock=Stock::with('products')->findOrFail($id);
        $products=$stock->products()->with('categories')
        ->when($request->search, function ($query, $search) {
            $query->where('name', 'LIKE', "%{$search}%");
        })
        ->paginate(PAGINATION)->withQueryString();

        return Inertia::render('Stock/Show',[
            'stock'=>new StockResource($stock),
            'products'=>productResource::collection($products)
        ]);
    }
}
