<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;

use App\Http\Requests\UserRequest;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function index(Request $request):Response{
        
        // $users=User::with('roles')->when($request->search, function ($query) use ($request) {
        //     $query->where('name', 'LIKE', "%{$request->search}%");
        // })->paginate(PAGINATION)->withQueryString();
       
        $users=UserResource::collection(
            User::with('roles')->when($request->search, function ($query) use ($request) {
                $query->where('name', 'LIKE', "%{$request->search}%");
            })->paginate(PAGINATION)->withQueryString()
        );
        
        return inertia('Users/Index', ['users'=>$users]);
    }

      public function create():Response{
        $roles=RolesResource::collection(
            Role::all()
        );
        

        return inertia('Users/Create',['roles'=>$roles]);
      }

    public function store(UserRequest $request):RedirectResponse
    {
       
        $request->validated();
        
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'number' => $request->input('number'),
                'password' => bcrypt($request->input('password')),
            ]);
            $user->assignRole($request->role);
            
            activity()
            ->causedBy(auth()->user())
            ->performedOn(new User())
            ->withProperties(['new'=>$request->all(),'old'=>[]])
            ->log('created user');
        });

        

        return to_route('users.index');
    }

    public function edit($id){
        

        if (!$user = User::find($id)) {
            return redirect()->route('users.index');
        }
        
        $user = new UserResource(User::with('roles')->find($id));
        $roles=RolesResource::collection(
            Role::all()
        );
        
        return inertia('Users/Edit', ['user'=>$user,'roles'=>$roles]);
    }
     public function update(UserRequest $request,$id){

        $request->validated();
       
        
        DB::transaction(function () use ($request, $id) {
            $user = User::findOrFail($id);

            $oldData = $user->getOriginal();

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'number' => $request->number,
            ]);

            $user->syncRoles([$request->input('role.name')]);

            activity()
                ->causedBy(auth()->user())
                ->performedOn(new User)
                ->withProperties(['new' => $request->all(), 'old' => $oldData])
                ->log('updated user');
        });

        return redirect()->route('users.index');
     }

    public function delete($id){

        
        $user=User::findOrFail($id);
        $user->delete();
        return redirect()->route('users.index')->with('success','User deleted successfully');
    }
}
