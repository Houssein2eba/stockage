<?php

use App\Http\Controllers\ClientsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Notifications\NotificationController;
use App\Http\Controllers\Pdf\FactureController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\Sales\SalesController;
use App\Http\Controllers\Users\UsersController;
use App\Http\Resources\RolesResource;
use App\Http\Resources\UserApiResource;
use App\Http\Resources\UserResource;
use App\Models\Client;
use App\Models\Fcm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes (no authentication required)

    Route::middleware(['throttle:5,1'])->post('/login', function (Request $request) {

        $request->validate([
            'credential' => 'required|string',
            'password' => 'required|string',
            'token'=>'required|string',
        ]);

        $login=$request->only('credential','password');
        if(filter_var($login['credential'], FILTER_VALIDATE_EMAIL)){
            $field='email';
        } else {
            $field='number';
        }

        if(Auth::attempt([$field=> $login['credential'],'password'=>$login['password']])){
            $user=Auth::user()->load('roles');

        }

        if (!$user) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }else{

            Fcm::updateOrCreate(['token' => $request->token]);
          Fcm::where('token', '!=', $request->token)->delete();
            $token = $user->createToken('auth_token')->plainTextToken;




        return response()->json([
            'token' => $token,
            'user' => new UserApiResource($user),
            'status' => 'success',
        ]);
        }


    })->name('login');


// Protected routes (require authentication)
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // Notification routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', function (Request $request) {


            return response()->json([
                'notifications' => $request->user()
                    ->notifications()
                    ->latest()
                    ->take(5)
                    ->get(),
                'unreadCount' => $request->user()
                    ->unreadNotifications()
                    ->count(),
            ]);
        });

        Route::post('/{id}/mark-as-read', function (Request $request, $id) {
            $notification = $request->user()->notifications()->find($id);

            if (!$notification) {
                return response()->json([
                    'message' => 'Notification not found'
                ], 404);
            }

            $notification->markAsRead();

            return response()->json([
                'message' => 'Notification marked as read',
                'notification' => $notification,
            ]);
        })->name('notifications.markAsRead');
        Route::delete('/{id}',[NotificationController::class,'destroy'])->name('notifications.destroy');
    });

    // Client routes
Route::prefix('clients')->group(function () {
        Route::get('/', [ClientsController::class,'index'])->name('clients.index');

        Route::post('/', [ClientsController::class, 'store'])
            ->name('clients.store');


        Route::put('/{client}', [ClientsController::class, 'update'])
            ->name('clients.update');

        Route::delete('/{client}', [ClientsController::class, 'deleteJson'])
            ->name('clients.destroy');


        Route::prefix('orders')->name('orders.')->group(function () {
              Route::get('/{client}', [ClientsController::class, 'show'])
            ->name('clients.orders');
        Route::put('/mark-paid/{id}', [SalesController::class, 'markAsPaid'])
        ->name('markAsPaid');
        Route::get('/export-pdf/{id}', [FactureController::class, 'generatePdf'])->name('invoice');


        });


    });
   Route::prefix('roles')->group(function () {
       Route::get('/',[RolesController::class,'index'])
       ->name('roles.index');
       Route::delete('/{id}',[RolesController::class,'destroy']);
   });

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/',[UsersController::class,'index'])->name('index');
        Route::get('/getUser/{id}',[UsersController::class,'getUser'])->name('show');
        Route::post('/', function(Request $request){
          $validated=$request->validate([
             'name' => 'required|string',
             'email' => 'required|email|unique:users,email',
             'number'=>['required','regex:/^([2-4][0-9]{7})$/',Rule::unique('users','number')],
             'password' => 'required|string',
             'role_id'=>'exists:roles,id',
          ]);

         $user= DB::transaction(function () use ($validated){
          $user=User::create($validated);
          $role=Role::find($validated['role_id']);
          $user->assignRole($role);
          return $user;
          });

          return response()->json([
              'user'=>$user
          ]);
        })->name('store');
        Route::put('/{id}',[UsersController::class,'updateJson'])->name('update');
        Route::delete('/{id}',function($id){
            if(Auth::user()->id==$id){
                return response()->json([
                    'message' => 'You cannot delete yourself'
                ], 401);
            }
            $user=User::find($id);
            $user->delete();
            return response()->json([
                'message' => 'User deleted successfully'
            ]);
        })->name('delete');

    });
    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');

    //Stock routes
    Route::prefix('stocks')->name('stocks.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Stock\StockController::class, 'index'])->name('index');
        });


    // Logout
    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    })->name('logout');
});


