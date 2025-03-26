<?php

namespace App\Http\Middleware;

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
        $auth=currentAuth();

        $user = $auth['user'] ?? null;
        $guard = $auth['guard'] ?? null;

    return array_merge(parent::share($request), [
        'auth' => [
            'user' => $auth ?? null,
            'guard' => $guard ?? null,
            'permissions' => $guard==='web' ? $user->getAllPermissions() : [],
            'roles' => $guard==='web' ? $user->roles : [],
        ],
        'flash' => [
            'success' => $request->session()->get('success'),
            'error' => $request->session()->get('error'),
        ],
    ]);
    }
}
