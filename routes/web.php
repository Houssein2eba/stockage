<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\Pdf\FactureController;
use App\Http\Controllers\Products\CategoriesController;
use App\Http\Controllers\Products\ProductsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\Sales\SalesController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Users\UsersController;
use App\Models\Order;
use Illuminate\Foundation\Application;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

define('PAGINATION', 2);

// Public Routes
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
    // Clients Routes (resourceful)
    Route::get('/clients', [ClientsController::class, 'index'])->name('clients.index');
    Route::get('/clients/create', [ClientsController::class, 'create'])->name('clients.create');
    Route::post('/clients', [ClientsController::class, 'store'])->name('clients.store');
    Route::get('client/show/{id}', [ClientsController::class, 'show'])->name('clients.show');
    Route::get('/clients/{id}', [ClientsController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/{id}', [ClientsController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{id}', [ClientsController::class, 'destroy'])->name('clients.destroy');
    //sells Routes
    Route::prefix('sales')->name('sales.')->group(function () {

        Route::get('/', [SalesController::class, 'index'])->name('index');
        Route::get('/sale/{id}', [SalesController::class, 'show'])->name('show');
        Route::get('/create', [SalesController::class, 'create'])->name('create');
        Route::post('/', [SalesController::class, 'store'])->name('store');
        Route::get('/{id}', [SalesController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SalesController::class, 'update'])->name('update');
        Route::delete('/{id}', [SalesController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/invoice', [FactureController::class, 'generatePdf'])->name('invoice');
    });


});

// Admin Routes
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    // Users Routes
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/export', [UsersController::class, 'export'])->name('export');
        Route::get('/', [UsersController::class, 'index'])->name('index');
        Route::get('/create', [UsersController::class, 'create'])->name('create');
        Route::post('/', [UsersController::class, 'store'])->name('store');
        Route::get('/{id}', [UsersController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UsersController::class, 'update'])->name('update');
        Route::delete('/{id}', [UsersController::class, 'delete'])->name('delete');
    });

    // Roles Routes
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RolesController::class, 'index'])->name('index');
        Route::get('/create', [RolesController::class, 'create'])->name('create');
        Route::post('/', [RolesController::class, 'store'])->name('store');
        Route::get('/{id}', [RolesController::class, 'edit'])->name('edit');
        Route::put('/{id}', [RolesController::class, 'update'])->name('update');
        Route::delete('/{id}', [RolesController::class, 'destroy'])->name('destroy');
    });

    // Activity Log Routes
    Route::prefix('activity')->name('activity.')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index'])->name('index');
        Route::get('/{activity}', [ActivityLogController::class, 'view'])->name('view');
    });

    // Categories Routes
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoriesController::class, 'index'])->name('index');
        Route::get('/create', [CategoriesController::class, 'create'])->name('create');
        Route::post('/', [CategoriesController::class, 'store'])->name('store');
        Route::put('/{id}', [CategoriesController::class, 'update'])->name('update');
        Route::delete('/{id}', [CategoriesController::class, 'destroy'])->name('destroy');
    });

    // Products Routes
    Route::prefix('products')->name('products.')->group(function () {

        Route::get('/', [ProductsController::class, 'index'])->name('index');
        Route::get('/create', [ProductsController::class, 'create'])->name('create');
        Route::post('/', [ProductsController::class, 'store'])->name('store');
        Route::get('/export', [ProductsController::class, 'export'])->name('export');
        Route::get('/{id}/show', [ProductsController::class, 'show'])->name('show');
        Route::get('/{id}', [ProductsController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ProductsController::class, 'update'])->name('update');
        Route::delete('/{id}', [ProductsController::class, 'destroy'])->name('destroy');
    });

    // Payment Methods Routes
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('/', [PaymentMethodController::class, 'index'])->name('index');
        Route::post('/', [PaymentMethodController::class, 'store'])->name('store');
        Route::delete('/{payment}', [PaymentMethodController::class, 'destroy'])->name('destroy');
    });
    //Notification routes
    Route::get('/notifications', [\App\Http\Controllers\Notifications\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-as-read/{id}', [\App\Http\Controllers\Notifications\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');


    Route::post('/status/{id}',function (Request $request, $id) {
        $order = Order ::findOrFail($id);
        $order->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Order status updated successfully.');
    })->name('orders.status');


});


require __DIR__.'/auth.php';
