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
                'guard_name' => 'web',
            ]);

            $permissions = Permission::whereIn('name', $request->input('permissions.*.name'))->get();
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
        $permissions = Permission::all();

        return Inertia::render('Roles/Edit', [
            'role' => $role,
            'permissions' => PermissionsResource::collection($permissions),
        ]);
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        $oldData = [
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('name')->toArray(),
        ];

        $role->update($request->only('name'));
        $role->syncPermissions($request->permissions);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($role)
            ->withProperties([
                'new' => $request->validated(),
                'old' => $oldData,
            ])
            ->log('Updated role');

        return to_route('roles.index');
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
