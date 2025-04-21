<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Http\Resources\productResource;
use App\Models\Client;
use App\Models\Product;
use App\Models\SaleRegister;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index(){
        $clients = ClientResource::collection(Client::all());
        $products=productResource::collection(Product::all());
        return inertia('Sales/Index', [
            'clients' => $clients,
            'products'=>$products
        ]);
    }

    public function store(Request $request){
        dd($request->all());

        $product = Product::find($request->product_id);
        $client = Client::find($request->client_id);

        $client->sales()->create([
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'total_amount' => $product->price * $request->quantity,
            'payment_method' => $request->payment_method,
            'status' => $request->payment_status,
        ]);
        $product->decrement('quantity', $request->quantity);



        return back()->with('success', 'Sale recorded successfully.');
    }
}
