<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\PermissionsResource;
use App\Http\Resources\RolesResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        if($request->wantsJson()){
            return response()->json([
                'roles'=>RolesResource::collection(Role::with('permissions')
                ->where('name', '!=', 'admin')->get()),
                'roles_count'=>Role::where('name', '!=', 'admin')->count(),
            ]);
        }
        $request->validate([
            'search' => 'nullable|string',
            'sort' => 'nullable|string|in:name,users_count',
            'direction' => 'nullable|string|in:asc,desc',
            'page' => 'nullable|integer|min:1',
        ]);
        $roles = Role::with(['permissions', 'users'])
        ->withCount('users')
            ->when($request->has('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->input('search') . '%');

            })
            ->when($request->has('sort'), function ($query) use ($request) {
                if($request->input('sort') === 'users_count'){
                    $query->orderBy('users_count', $request->input('direction', 'asc'));
                }else{
                    $query->orderBy($request->input('sort'), $request->input('direction', 'asc'));
                }
            })
            ->where('name', '!=', 'admin')
            ->paginate(PAGINATION)
            ->withQueryString();

        return Inertia::render('Roles/Index', [
            'roles' => RolesResource::collection($roles),
        ]);
    }

    public function create()
    {
        $permissions = Permission::all();

        return Inertia::render('Roles/Create', [
            'permissions' => PermissionsResource::collection($permissions),
        ]);
    }

    public function store(StoreRoleRequest $request)
    {
        DB::transaction(function () use ($request) {
            $role = Role::create([
                'name' => $request->name,
            ]);

            $permissions = collect($request->permissions)->pluck('name');
            $role->syncPermissions($permissions);


        });

        return to_route('roles.index');
    }

    public function edit($id)
{
    $role = Role::with('permissions')->findOrFail($id);

    return Inertia::render('Roles/Edit', [
        'role' => new RolesResource($role),
        'permissions' => PermissionsResource::collection(Permission::all()),
    ]);
}

    public function update(Request $request, $id)
    {


        $request->validate([

            'name' => ['required', 'string', 'max:255',Rule::unique('roles')->ignore($id)],
            'permissions' => 'required|array',
            'permissions.*.name'=>'string|exists:permissions,name',
            'permissions.*.id' => 'exists:permissions,id',
        ],[
            'name.required' => 'Le nom est obligatoire',
            'name.unique' => 'Le nom est déjà utilisé',
            'permissions.required' => 'Les permissions sont obligatoires',
            'permissions.*.name.exists' => 'La permission n\'existe pas',
            'permissions.*.id.exists' => 'La permission n\'existe pas',
        ]);

        $role = Role::findOrFail($id);

        DB::transaction(function () use ($request, $role) {
            $role->update([
                'name' => $request->name,
            ]);

            $permissions = collect($request->permissions)->pluck('name');
            $role->syncPermissions($permissions);

            $role->refresh();

        });

        return to_route('roles.index');
    }

    public function destroy(Request $request,$id)
    {
        $role = Role::findOrFail($id);
        $old = $role->toArray();
        $role->delete();

         if($request->wantsJson()){
            return response()->json(['message' => 'Role deleted successfully.']);
         }
        return to_route('roles.index');
    }
}
