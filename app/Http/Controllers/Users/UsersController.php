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
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
class UsersController extends Controller
{
    public function export()
    {
        return Excel::download(new UsersExport(), 'users.xlsx');
    }
    public function index(Request $request): Response
    {
        $users = User::with('roles')
            ->when($request->search, fn($query) =>
                $query->where('name', 'like', "%{$request->search}%")
            )
            ->paginate(PAGINATION)
            ->withQueryString();

        return Inertia::render('Users/Index', [
            'users' => UserResource::collection($users),
            'filters' => $request->only('search'),
        ]);
    }

    public function create(): Response
    {
        $roles = RolesResource::collection(Role::all());

        return Inertia::render('Users/Create', [
            'roles' => $roles,
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'number' => $request->number,
                'password' => bcrypt($request->password),
            ]);

            $user->assignRole($request->role);

            activity()
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->withProperties([
                    'new' => $request->validated(),
                    'old' => [],
                ])
                ->log('Created user');
        });

        return to_route('users.index');
    }

    public function edit($id): Response|RedirectResponse
    {
        $user = User::with('roles')->find($id);

        if (!$user) {
            return redirect()->route('users.index')->with('error', 'User not found.');
        }

        return Inertia::render('Users/Edit', [
            'user' => new UserResource($user),
            'roles' => RolesResource::collection(Role::all()),
        ]);
    }

    public function update(UserRequest $request, $id): RedirectResponse
    {
        DB::transaction(function () use ($request, $id) {
            $user = User::findOrFail($id);

            $oldData = $user->only(['name', 'email', 'number']);

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'number' => $request->number,
            ]);

            $user->syncRoles([$request->input('role.name')]);

            activity()
                ->causedBy(auth()->user())
                ->performedOn(new User)
                ->withProperties([
                    'new' => $request->validated(),
                    'old' => $oldData,
                ])
                ->log('Updated user');
        });

        return redirect()->route('users.index');
    }

    public function delete($id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $userName = $user->name;

        $user->delete();

        activity()
            ->causedBy(auth()->user())
            ->performedOn(new User)
            ->withProperties([
                'old' => ['name' => $userName],
                'new' => [],
            ])
            ->log('Deleted user');

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
