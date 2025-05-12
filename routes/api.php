<?php

use App\Http\Controllers\ClientsController;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/notifications', function (Request $request) {
        return response()->json([
            'notifications' => $request->user()->notifications()->latest()->take(5)->get(),
            'unreadCount' => $request->user()->unreadNotifications()->count(),
        ]);
    });
    Route::post('/notifications/{id}', function (Request $request, $id) {
        $notification = $request->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json([
            'message' => 'Notification marked as read',
            'notification' => $notification,
        ]);
    })->name('notifications.markAsRead');
    Route::get('/clients',function(){
    $clients=Client::with('orders')->get()->toResourceCollection();

    return response()->json($clients);

})->name('clients');
Route::delete('/clients/{id}',[ClientsController::class,'deleteJson']);
Route::post('/clients',[ClientsController::class,'store']);

//logout
Route::post('/logout',function(Request $request){
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message'=>'Logged out successfully']);
})->name('logout');
});



// Auth routes
Route::post('/login',function(Request $request){

    $request->validate([
        'email'=>'required|email',
        'password'=>'required'
    ]);
    $user=User::where('email',$request->email)->first();

    if(!$user || !Hash::check($request->password,$user->password)){
        return response()->json(['message'=>'Invalid credentials'],401);
    }
    $token=$user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'token'=>$token,
        'user'=>$user,
        'status'=>'success',
    ]);
})->name('login');

