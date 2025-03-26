<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    
     public function index()
    {

        // $roleCounts = DB::table('model_has_roles')
        //     ->select('role_id', DB::raw('count(*) as user_count'))
        //     ->groupBy('role_id')
        //     ->get();

        // foreach ($roleCounts as $roleCount) {
        //     dd($roleCount);
        // }
        
       

// $managerRole = Role::findByName('manager','web');
// $cashierRole = Role::findByName('cachier','web');

// // Check if any users are assigned
// dd([
//     'manager_users' => $managerRole->users()->count(),
//     'cashier_users' => $cashierRole->users()->count()
// ]);
   
  $roles=Role::with(['permissions']) // This adds a 'users_count' property
                ->get();
    
     $rolesData = [];
     foreach ($roles as $role) {
         $rolesData[] = [
            'id' => $role->id,
             'name' => $role->name,
             'users_count' => $role->users()->count(),
             'permissions' => $role->permissions
         ];
     }
    
           
        return Inertia::render('Roles/Index', [
            'roles' => $rolesData,
            
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
        $role = Role::findOrFail($id);
        $role->update($request->only('name'));
        $role->syncPermissions($request->permissions);
        
        return redirect()->route('roles.index');
    }
    public function destroy($role)
    {
        
          
        Role::find($role)->delete();
        return redirect()->back();
    }
}
