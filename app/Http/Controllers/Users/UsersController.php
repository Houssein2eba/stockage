<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\User;
use Illuminate\Http\Request;
class UsersController extends Controller
{
    use AuthorizesRequests;

    public function index(){

        $auth=currentAuth();
    // or with Spatie permissions directly
        dd($auth);
    if (!auth()->user()->can('view_users' || auth()->guard('admin')->check())) {
        abort(403);
    }

        $users=User::with('roles')->get()->toArray();
        return inertia('Users/Index', ['users'=>$users]);
    }
}
