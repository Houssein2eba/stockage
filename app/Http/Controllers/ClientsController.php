<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use App\Http\Resources\productResource;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientsController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::query()
            ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            })
            ->when($request->sort && $request->direction, function ($query) use ($request) {
                $query->orderBy($request->sort, $request->direction);
            }, function ($query) {
                $query->latest();
            })
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
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'number' => 'required|string|regex:/^[2-4][0-9]{7}/',
        ]);

        DB::transaction(function () use ($request) {
            Client::create([
                'name' => $request->name,
                'number' => $request->number,
            ]);
            activity()
                ->performedOn(new Client)
                ->causedBy(auth()->user())
                ->log('Client Created');
        });

        return back();
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        DB::transaction(function () use ($client) {
            $client->delete();
            activity()
                ->performedOn(new Client)
                ->causedBy(auth()->user())
                ->log('Client Deleted');
        });

        return back();
    }
}
