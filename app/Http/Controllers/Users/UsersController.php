<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function index(){
        
        $users=User::with('roles')->get()->toArray();
        return inertia('Users/Index', ['users'=>$users]);
    }

      public function create(){
        $roles=Role::get()->toArray();
        

        return inertia('Users/Create',['roles'=>$roles]);
      }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required|string|same:password',
            'number' => 'required|string|regex:/^([2-4][0-9]{7})$/',
            'role' => 'required|integer|exists:roles,id',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'number' => $request->number,
            'password' => $request->password,
            
        ]);
        $user->roles()->attach($request->role);

        

        return redirect()->route('users.index');
    }

    public function edit($id){
        
        if (!$user = User::find($id)) {
            return redirect()->route('users.index');
        }
        //$user = User::findOrFail($id)->roles()->add($id); 5 stars
        $user=User::with('roles')->findOrFail($id);
        $roles=Role::get()->toArray();
        
        return inertia('Users/Edit', ['user'=>$user,'roles'=>$roles]);
    }
     public function update(Request $request,$id){

        $request->validate([
            'name' => 'required|string|min:3',
            'email' => 'required|email|unique:users,email,' . $id,
            'number' => 'required|string|regex:/^([2-4][0-9]{7})$/',
            'role' => 'required|string|exists:roles,name',
        ]);
        
        $user=User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'number' => $request->number,
        ]);
        $user->syncRoles([$request->role]);
        return redirect()->route('users.index');
     }

    public function delete($id){

        
        $user=User::findOrFail($id);
        $user->delete();
        return redirect()->route('users.index')->with('success','User deleted successfully');
    }
}
