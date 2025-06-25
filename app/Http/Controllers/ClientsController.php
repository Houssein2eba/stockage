<?php

namespace App\Http\Controllers;

use App\Exports\ClientExport;
use App\Exports\ClientsExport;
use App\Http\Resources\ClientResource;
use App\Http\Requests\ClientRequest;
use App\Http\Resources\OrderResource;
use App\Models\Client;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Log;
use Maatwebsite\Excel\Excel;

class ClientsController extends Controller
{
    public function deleteJson($id){
        $client=Client::findOrFail( $id );
        $client->delete();
        return response()->json(['message'=>'Client deleted']);

    }
    public function index(Request $request)
    {

        $request->validate([
            'search' => 'nullable|string',
            'sort' => 'nullable|string|in:name,number,orders_count,depts_amount,created_at',
            'direction' => 'nullable|string|in:asc,desc',
            'page' => 'nullable|integer|min:1',

        ]);

      if($request->wantsJson()){
        Log::info($request->wantsJson());

        $clients=Client::with('orders')->
        when($request->search, function ($query) use ($request) {
            $query->where('name', 'like', '%'. $request->search. '%')
                ->orWhere('number', 'like', '%'. $request->search. '%');
        })->
        paginate(6)->withQueryString();
        return response()->json([
            'clients' => ClientResource::collection($clients),
            'users_count' => Client::count(),
            'meta'=>[
                'current_page'=>$clients->currentPage(),
                'last_page'=>$clients->lastPage(),
            ]
        ]);
      }


        $clients = Client::with(['orders' => function ($q) {
            $q->with('products'); // if you need product info
        }])
        ->withCount('orders') // to allow sorting by number of orders
        ->when($request->search, function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('number', 'like', '%' . $request->search . '%');
        })
        ->when($request->sort && $request->direction, function ($query) use ($request) {
            if ($request->sort === 'orders_count') {
                $query->orderBy('orders_count', $request->direction);
            } elseif ($request->sort === 'total_order_amount') {
                $query->orderBy('total_order_amount', $request->direction);
            } elseif ($request->sort === 'depts_amount') {
                $query->orderBy('depts_amount', $request->direction);
            } else {
                $query->orderBy($request->sort, $request->direction);
            }
        }, function ($query) {
            $query->latest();
        })
        ->paginate(PAGINATION)
        ->withQueryString();




        return Inertia::render('Clients/Index', [
            'clients' => ClientResource::collection($clients),
            'filters' => $request->only(['search', 'sort', 'direction', 'page']),
            'clients_count' => DB::table('clients')->count(),
        ]);

    }
    public function show(Request $request,$id)
{

    if($request->wantsJson()){
        $orders=Order::with(['products','client'])
        ->where('client_id', $id)

        ->cursorPaginate(3, ['*'], 'cursor', $request->cursor ?? null)
        ->withQueryString();
        return response()->json([
            'orders' => OrderResource::collection($orders),
            'meta'=>[
                'next_cursor' => $orders->nextCursor()?->encode(),
                'per_page' => $orders->perPage(),
                'has_more' => $orders->hasMorePages(),
            ]
        ]);
    }
    $client = Client::with('orders')->findOrFail($id);

     $orders = $client->orders()
    ->with(['products','payment','client'])
    ->latest()
    ->paginate(PAGINATION)
    ->withQueryString();







    return inertia('Clients/Show', [
        'client' => new ClientResource($client),
        'orders' => OrderResource::collection($orders),
    ]);
}


    public function create()
    {
        return inertia('Clients/Create');
    }
    public function store(ClientRequest $request)
    {

        $client=DB::transaction(function () use ($request) {
            $client = Client::create([
                'name' => $request->name,
                'number' => $request->number,
            ]);



                return $client;
        });

        if($request->wantsJson()){
            return response()->json([
                'client' => new ClientResource($client),
                'message' => 'Client Created',
                'status' => 200
            ]);
        }else{
            return back()->with('success', 'Client Created');
        }


    }

    public function edit($id)
    {
        $client = Client::findOrFail($id);
        return inertia('Clients/Edit', [
            'client' => new ClientResource($client)
        ]);
    }

    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'name' => 'required|string',
            'number' => 'required|string|unique:clients,number,' . $id,
        ],
        [
            'name.required' => 'Le nom est obligatoire',
            'number.required' => 'Le numéro est obligatoire',
            'number.unique' => 'Le numéro est déjà utilisé',
        ]
    );
        $client=DB::transaction(function () use ($validated, $id) {
            $client = Client::findOrFail($id);
            $old = $client->toArray();
            $client->update([
                'name' => $validated['name'],
                'number' => $validated['number'],
            ]);




                return $client;
        });
        if($request->wantsJson()){
            return response()->json([
                'client' => new ClientResource($client),
                'message' => 'Client Updated',
                'status' => 200
            ]);
        }

        return back();
    }

    public function destroy($id)
    {

        $client = Client::findOrFail($id);
        DB::transaction(function () use ($client) {

            $client->delete();

        });

        return back();
    }

    public function export(){

        return \Maatwebsite\Excel\Facades\Excel::download(new ClientsExport(), 'clients.xlsx');
    }
    public function exportclient($id){
        $client = Client::findOrFail($id);

        return \Maatwebsite\Excel\Facades\Excel::download(new ClientExport($client), 'client.xlsx');
    }
}
