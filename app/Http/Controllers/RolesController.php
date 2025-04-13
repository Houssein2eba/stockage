<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\PermissionsResource;
use App\Http\Resources\RolesResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    
     public function index()
    {
        return Inertia::render('Roles/Index', [
            'roles' => RolesResource::collection(
                Role::paginate(PAGINATION)
            
            ),
        ]);
    }
   
    public function create(){

        return inertia('Roles/Create',['permissions'=>PermissionsResource::collection(
            Permission::all()
        )]);
    }

    public function store(StoreRoleRequest $request)
    {
       
        
        DB::transaction(function () use ($request) {
            $role = Role::create([
                'name' => $request->input('name'),
                'guard_name' => 'web',
            ]);
            $role->permissions()->sync(
                Permission::whereIn('name', $request->input('permissions.*.name'))->get()
            );
            activity()
        ->causedBy(auth()->user())
        ->performedOn($role)
        ->withProperties([
            'new' => $request->validated(),
            'old' => [],
        ])
        ->log('create role');
        });
        

        
        return to_route('roles.index');
    }
    public function edit($id){
        
        $role=Role::with('permissions')->find($id);
        
        $permissions=Permission::all();
        
        return inertia('Roles/Edit', ['role'=>$role,'permissions'=>$permissions]);
    }
     public function update(Request $request,$id)
    {
        

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);
        $role = Role::with('permissions')->find($id);
        
        $oldRoleData = [
        'name' => $role->name,
        'permissions' => $role->permissions->pluck('name')->toArray()
    ];

        $role->update($request->only('name'));
        $role->syncPermissions($request->permissions);
        activity()
        ->causedBy(auth()->user())
        ->performedOn(new Role())
        ->withProperties(['new' => $request->all(), 'old' => $oldRoleData])
        ->log('updated role');

        return to_route('roles.index');
    }
    public function destroy($id)
    {
        
          
        $role=Role::find($id);
        $role->delete();

        activity()
        ->causedBy(auth()->user())
        ->performedOn(new Role())
        ->withProperties(['new'=>[],'old'=>['role name'=>$role->name]])
        ->log('delete role')
        ;

        return to_route('roles.index');
    }
}
