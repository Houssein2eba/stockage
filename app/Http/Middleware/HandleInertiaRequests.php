<?php

namespace App\Http\Middleware;

use App\Http\Resources\PermissionsResource;
use App\Http\Resources\RolesResource;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {

    $user = auth()->user() ?? null;
    $userPermissions = $user ? $user->getPermissionsViaRoles()->pluck('name')->toArray() : [];
    $userRole= $user ? $user->roles->first() : null;



    return array_merge(parent::share($request), [

        'auth' => [
            'user' => $user ?? null,
            'permissions' =>$user ? $userPermissions : [],
            'roles' => $user ? $userRole : null,
            'notifications' => fn () => $request->user()
            ? $request->user()->unreadNotifications
            : null,
        'notificationCount' => fn () => $request->user()
            ? $request->user()->unreadNotifications->count()
            : 0,
        ],

    ]);
    }
}
