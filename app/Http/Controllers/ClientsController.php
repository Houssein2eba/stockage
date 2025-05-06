<?php

namespace App\Http\Controllers;

use App\Exports\ClientExport;
use App\Exports\ClientsExport;
use App\Http\Resources\ClientResource;
use App\Http\Requests\ClientRequest;
use App\Http\Resources\OrderResource;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Maatwebsite\Excel\Excel;

class ClientsController extends Controller
{
    public function index(Request $request)
    {

        $request->validate([
            'search' => 'nullable|string',
            'sort' => 'nullable|string|in:name,number,orders_count,depts_amount,created_at',
            'direction' => 'nullable|string|in:asc,desc',
            'page' => 'nullable|integer|min:1',

        ]);
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
    public function show($id)
{
    $client = Client::with('orders')->findOrFail($id);

$orders = $client->orders()
    ->with(['products', 'payment']) // Eager load relationships per order
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
        DB::transaction(function () use ($request) {
            $client = Client::create([
                'name' => $request->name,
                'number' => $request->number,
            ]);
            $attributes = $client->toArray();
            unset($attributes['id'], $attributes['created_at'], $attributes['updated_at']);
            activity()
                ->performedOn($client)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => $attributes])
                ->log('Client Created');
        });

        return back();
    }

    public function edit($id)
    {
        $client = Client::findOrFail($id);
        return inertia('Clients/Edit', [
            'client' => new ClientResource($client)
        ]);
    }

    public function update(ClientRequest $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $client = Client::findOrFail($id);
            $old = $client->toArray();
            $client->update([
                'name' => $request->name,
                'number' => $request->number,
            ]);
            $attributes = $client->toArray();
            unset($old['id'], $old['created_at'], $old['updated_at']);
            unset($attributes['id'], $attributes['created_at'], $attributes['updated_at']);
            activity()
                ->performedOn($client)
                ->causedBy(auth()->user())
                ->withProperties(['old' => $old, 'attributes' => $attributes])
                ->log('Client Updated');
        });

        return back();
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        DB::transaction(function () use ($client) {
            $old = $client->toArray();
            $client->delete();
            unset($old['id'], $old['created_at'], $old['updated_at']);
            activity()
                ->performedOn($client)
                ->causedBy(auth()->user())
                ->withProperties(['old' => $old])
                ->log('Client Deleted');
        });

        return back();
    }

    public function export(){
        activity()->causedBy(auth()->user())->log('Clients Exported')

        ;
        return \Maatwebsite\Excel\Facades\Excel::download(new ClientsExport(), 'clients.xlsx');
    }
    public function exportclient($id){
        $client = Client::findOrFail($id);
        activity()->causedBy(auth()->user())->log('Client Exported')
        ;
        return \Maatwebsite\Excel\Facades\Excel::download(new ClientExport($client), 'client.xlsx');
    }
}
