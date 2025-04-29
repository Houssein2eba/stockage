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

use App\Http\Controllers\Users\UsersController;
use App\Http\Controllers\Notifications\NotificationController;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

define('PAGINATION', 10);

// Public Routes
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Authentication Routes
require __DIR__.'/auth.php';

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Clients
    Route::prefix('clients')->name('clients.')->group(function () {
        Route::get('/', [ClientsController::class, 'index'])->name('index');
        Route::get('/create', [ClientsController::class, 'create'])->name('create');
        Route::post('/', [ClientsController::class, 'store'])->name('store');
        Route::get('/export', [ClientsController::class, 'export'])->name('export');
        Route::get('/exportclient/{id}', [ClientsController::class, 'exportclient'])->name('exportclient');
        Route::get('/{id}/show', [ClientsController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ClientsController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ClientsController::class, 'update'])->name('update');
        Route::delete('/{id}', [ClientsController::class, 'destroy'])->name('destroy');
    });

    // Sales
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [SalesController::class, 'index'])->name('index');
        Route::get('/create', [SalesController::class, 'create'])->name('create');
        Route::post('/', [SalesController::class, 'store'])->name('store');
        Route::get('/sale/{id}', [SalesController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [SalesController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SalesController::class, 'update'])->name('update');
        Route::delete('/{id}', [SalesController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/invoice', [FactureController::class, 'generatePdf'])->name('invoice');
    });

    // Admin-only Routes
    Route::middleware(['role:admin'])->group(function () {
        // Users
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UsersController::class, 'index'])->name('index');
            Route::get('/create', [UsersController::class, 'create'])->name('create');
            Route::post('/', [UsersController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [UsersController::class, 'edit'])->name('edit');
            Route::put('/{id}', [UsersController::class, 'update'])->name('update');
            Route::delete('/{id}', [UsersController::class, 'delete'])->name('delete');
            Route::get('/export', [UsersController::class, 'export'])->name('export');
        });

        // Roles
        Route::resource('roles', RolesController::class)
            ->except(['show'])
            ->names([
                'index' => 'roles.index',
                'create' => 'roles.create',
                'store' => 'roles.store',
                'edit' => 'roles.edit',
                'update' => 'roles.update',
                'destroy' => 'roles.destroy',
            ])->middleware('role:admin');

        // Activity Log
        Route::prefix('activity')->name('activity.')->group(function () {
            Route::get('/', [ActivityLogController::class, 'index'])->name('index');
            Route::get('/{activity}', [ActivityLogController::class, 'view'])->name('view');
        })->middleware('role:admin');

        // Products
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [ProductsController::class, 'index'])->name('index');
            Route::get('/create', [ProductsController::class, 'create'])->name('create');
            Route::post('/', [ProductsController::class, 'store'])->name('store');
            Route::get('/export', [ProductsController::class, 'export'])->name('export');
            Route::get('/{id}/show', [ProductsController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [ProductsController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ProductsController::class, 'update'])->name('update');
            Route::delete('/{id}', [ProductsController::class, 'destroy'])->name('destroy');
        });
    });

    // Categories (accessible to verified users)
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoriesController::class, 'index'])->name('index');
        Route::get('/create', [CategoriesController::class, 'create'])->name('create');
        Route::post('/', [CategoriesController::class, 'store'])->name('store');
        Route::put('/{id}', [CategoriesController::class, 'update'])->name('update');
        Route::delete('/{id}', [CategoriesController::class, 'destroy'])->name('destroy');
    });

    // Payment Methods
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('/', [PaymentMethodController::class, 'index'])->name('index');
        Route::post('/', [PaymentMethodController::class, 'store'])->name('store');
        Route::delete('/{payment}', [PaymentMethodController::class, 'destroy'])->name('destroy');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('markAsRead');
    });

    // Order Status Update
    Route::post('/status/{id}', function (Illuminate\Http\Request $request, $id) {
        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Order status updated successfully.');
    })->name('orders.status');
});

// Fallback Route
Route::fallback(function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});
