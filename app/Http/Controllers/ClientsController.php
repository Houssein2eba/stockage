<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientsController extends Controller
{
    public function index()
    {
        return inertia('Clients/Index', [
            'clients' => ClientResource::collection(Client::with(['user'])->paginate(PAGINATION)),
        ]);
    }
}
