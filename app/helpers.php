<?php
if (!function_exists('currentAuth')) {
    function currentAuth()
    {
        if (auth()->guard('admin')->check()) {
            return [
                'user' => auth()->guard('admin')->user(),
                'guard' => 'admin',
                'type' => 'admin'
            ];
        }
        
        if (auth()->guard('web')->check()) {
            return [
                'user' => auth()->user(),
                'guard' => 'web',
                'type' => 'user'
            ];
        }
        
        return null;
    }
}
?>