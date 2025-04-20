<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Http\Resources\productResource;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index(){
        $clients = ClientResource::collection(Client::all());
        $products=productResource::collection(Product::all());
        return inertia('Sales/Index', [
            'clients' => $clients,
        ]);
    }

    public function store(Request $request){
        dd($request->all());

        // Logic to handle the sale
        // ...

        return back()->with('success', 'Sale recorded successfully.');
    }
}
