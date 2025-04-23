<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use App\Http\Requests\ClientRequest;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientsController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::query()
            ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('number', 'like', '%' . $request->search . '%');
            })
            ->with('orders')
            ->latest()
            ->paginate(PAGINATION)
            ->withQueryString();


        return inertia('Clients/Index', [
             'clients' => ClientResource::collection($clients),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {


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
}
