<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function index(Request $request):Response{
        
       
        $users=User::with('roles')->when($request->search, function ($query) use ($request) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        })->paginate(PAGINATION)->withQueryString();
        
        return inertia('Users/Index', ['users'=>$users]);
    }

      public function create():Response{
        $roles=Role::get()->toArray();
        

        return inertia('Users/Create',['roles'=>$roles]);
      }

    public function store(UserRequest $request):RedirectResponse
    {
       
        $request->validated();
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'number' => $request->number,
            'password' => $request->password,
            
        ])->assignRole($request->role);

        

        return to_route('Users/Index');
    }

    public function edit($id){
        
        if (!$user = User::find($id)) {
            return redirect()->route('users.index');
        }
        //$user = User::findOrFail($id)->roles()->add($id); 5 stars
        // $user=User::with('roles')->findOrFail($id);
        $user=User::all()->withRelatio
        $roles=Role::get()->toArray();
        
        return inertia('Users/Edit', ['user'=>$user,'roles'=>$roles]);
    }
     public function update(UserRequest $request,$id){

        $request->validated();
       
        
        $user=User::with('roles')->findOrFail($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'number' => $request->number,
        ]);
        $user->syncRoles([$request->role]);

       activity()
       ->causedBy(auth()->user())
       ->performedOn(new User())
       ->withProperties(['new'=>$request->all(),'old'=>$user->getOriginale()])
       ->log('updated user');

        return redirect()->route('users.index');
     }

    public function delete($id){

        
        $user=User::findOrFail($id);
        $user->delete();
        return redirect()->route('users.index')->with('success','User deleted successfully');
    }
}
