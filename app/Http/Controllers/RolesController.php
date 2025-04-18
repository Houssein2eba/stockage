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
    public function index()
    {
        $roles = Role::with(['permissions', 'users'])->paginate(PAGINATION);

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

            activity()
                ->causedBy(auth()->user())
                ->performedOn($role)
                ->withProperties([
                    'new' => $request->validated(),
                    'old' => [],
                ])
                ->log('Created role');
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

        dd($request->all());
        $request->validate([
            'id' => 'required|integer|exists:roles,id',
            'name' => ['required', 'string', 'max:255',Rule::unique('roles')->ignore($id)],
            'permissions' => 'required|array',
            'permissions.*.name'=>'string|exists:permissions,name',
            'permissions.*.id' => 'integer|exists:permissions,id',
        ]);

        $role = Role::findOrFail($id);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $roleName = $role->name;
        $role->delete();

        activity()
            ->causedBy(auth()->user())
            ->performedOn(new Role)
            ->withProperties([
                'new' => [],
                'old' => ['role_name' => $roleName],
            ])
            ->log('Deleted role');

        return to_route('roles.index');
    }
}
