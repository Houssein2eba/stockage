<?php

use App\Models\Admin;
use App\Models\User;
if (!function_exists('currentAuth')) {
    function currentAuth()
    {
        if (auth()->guard('admin')->check()) {

            $admin=Admin::findOrFail(auth()->guard('admin')->user()->id);
            return [
                'user' => $admin,
                'guard' => 'admin',
                'type' => 'admin'
            ];
        }
        
        if (auth()->guard('web')->check()) {
            $user=User::with('roles')->findOrFail(auth()->guard('web')->user()->id);
            return [
                'user' => $user,
                'guard' => 'web',
                'type' => 'user'
            ];
        }
        
        return null;
    }
}
?>