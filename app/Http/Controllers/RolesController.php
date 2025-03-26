<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
     public function index()
    {
       

        return Inertia::render('Roles/Index', [
            'roles' => Role::with('permissions')->get(),
            
        ]);
    }
   
    public function create(){

        return inertia('Roles/Create',['permissions'=>Permission::all()]);
    }

    public function store(StoreRoleRequest $request)
    {
        
        $role = Role::create([
            'name' => $request->input('name'),
            'guard_name' => 'web',
        ]);
        $role->syncPermissions($request->permissions);
        
        return redirect()->route('roles.index');
    }
     public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update($request->only('name'));
        $role->syncPermissions($request->permissions);
        
        return redirect()->back()->with('success', 'Role updated successfully');
    }
    public function destroy($role)
    {
        
          
        Role::find($role)->delete();
        return redirect()->back();
    }
}
