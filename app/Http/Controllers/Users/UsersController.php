<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UserApiResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
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
    public function index(Request $request)
    {
        $users = User::with('roles')
            ->when($request->search, fn($query) =>
                $query->where('name', 'like', "%{$request->search}%")
            )
            ->paginate(PAGINATION)
            ->withQueryString();

            if($request->wantsJson()){
                $usersApi=User::with('roles')->get();
                Log::info($usersApi);
                return response()->json([
                   'users'=>UserApiResource::collection($usersApi)
                ]);
            }

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
    public function getUser($id){
        $user=User::findOrFail($id);
        return response()->json([
            'user'=>new UserApiResource($user)
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
    public function updateJson(Request $request,$id){
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,'.$id,
            'number'=>['required','regex:/^([2-4][0-9]{7})$/',Rule::unique('users','number')->ignore($id)],
            'role_id'=>'exists:roles,id',
        ]);

        $user = User::findOrFail($id);
        $user->update($request->all());
        $role=Role::find($request->input('role_id'));
        $user->syncRoles([$role]);
        return response()->json([
            'user'=>new UserApiResource($user)
        ]);
    }

    public function delete(Request $request,$id): RedirectResponse
    {
        $user = User::findOrFail($id);

        Log::info($user);
        $userName = $user->name;

        $user->delete();
        if($request->wantsJson()){
            return response()->json([
                'message' => 'User deleted successfully.'
            ]);
        }

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
