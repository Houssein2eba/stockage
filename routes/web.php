<?php
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Pdf\FactureController;
use App\Http\Controllers\Products\CategoriesController;
use App\Http\Controllers\Products\ProductsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\Sales\SalesController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Stock\StockController;
use App\Http\Controllers\Users\UsersController;
use App\Http\Controllers\Notifications\NotificationController;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

define('PAGINATION', 7);

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
        Route::get('/', [ClientsController::class, 'index'])->name('index')->middleware('permission:voirClients');
        Route::get('/create', [ClientsController::class, 'create'])->name('create')->middleware('permission:creerClients');
        Route::post('/', [ClientsController::class, 'store'])->name('store')->middleware('permission:creerClients');
        Route::get('/export', [ClientsController::class, 'export'])->name('export')->middleware('permission:exporterClients');
        Route::get('/exportclient/{id}', [ClientsController::class, 'exportclient'])->name('exportclient')->middleware('permission:exporterClients');
        Route::get('/{id}/show', [ClientsController::class, 'show'])->name('show')->middleware('permission:voirClients');
        Route::get('/{id}/edit', [ClientsController::class, 'edit'])->name('edit')->middleware('permission:modifierClients');
        Route::put('/{id}', [ClientsController::class, 'update'])->name('update')->middleware('permission:modifierClients');
        Route::delete('/{id}', [ClientsController::class, 'destroy'])->name('destroy')->middleware('permission:supprimerClients');
        Route::post('/bulk-destroy', [ClientsController::class, 'bulkDestroy'])->name('bulkDestroy')->middleware('permission:supprimerClients');
    });

    // Sales
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [SalesController::class, 'index'])->name('index')->middleware('permission:voirVentes');
        Route::get('/create', [SalesController::class, 'create'])->name('create')->middleware('permission:creerVentes');
        Route::post('/', [SalesController::class, 'store'])->name('store')->middleware('permission:creerVentes');
        Route::get('/sale/{id}', [SalesController::class, 'show'])->name('show')->middleware('permission:voirVentes');
        Route::put('/sale/{id}/markAsPaid', [SalesController::class, 'markAsPaid'])->name('markAsPaid')->middleware('permission:marquerPaye');
        Route::get('/{id}/edit', [SalesController::class, 'edit'])->name('edit')->middleware('permission:modifierVentes');
        Route::put('/{id}', [SalesController::class, 'update'])->name('update')->middleware('permission:modifierVentes');
        Route::delete('/{id}', [SalesController::class, 'destroy'])->name('destroy')->middleware('permission:supprimerVentes');
        Route::get('/{id}/invoice', [FactureController::class, 'generatePdf'])->name('invoice')->middleware('permission:genererFacture');
    });

    // Stocks
    Route::prefix('stocks')->name('stocks.')->group(function () {
        Route::get('/',[StockController::class,'index'])->name('index')->middleware('permission:voirStocks');
        Route::get('/create', [StockController::class, 'create'])->name('create')->middleware('permission:creerStocks');
        Route::post('/', [StockController::class, 'store'])->name('store')->middleware('permission:creerStocks');
        Route::get('/{id}/show', [StockController::class, 'show'])->name('show')->middleware('permission:voirStocks');
        Route::get('/{id}/edit', [StockController::class, 'edit'])->name('edit')->middleware('permission:modifierStocks');
        Route::put('/{id}', [StockController::class, 'update'])->name('update')->middleware('permission:modifierStocks');
        Route::delete('/{id}', [StockController::class, 'destroy'])->name('destroy')->middleware('permission:supprimerStocks');
    });

    // Admin-only Routes
    // Users
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UsersController::class, 'index'])->name('index')->middleware('permission:voirUtilisateurs');
        Route::get('/create', [UsersController::class, 'create'])->name('create')->middleware('permission:creerUtilisateurs');
        Route::post('/', [UsersController::class, 'store'])->name('store')->middleware('permission:creerUtilisateurs');
        Route::get('/{id}/edit', [UsersController::class, 'edit'])->name('edit')->middleware('permission:modifierUtilisateurs');
        Route::put('/{id}', [UsersController::class, 'update'])->name('update')->middleware('permission:modifierUtilisateurs');
        Route::delete('/{id}', [UsersController::class, 'delete'])->name('delete')->middleware('permission:supprimerUtilisateurs');
        Route::post('/bulk-destroy', [UsersController::class, 'bulkDestroy'])->name('bulkDestroy')->middleware('permission:supprimerUtilisateurs');
        Route::get('/export', [UsersController::class, 'export'])->name('export')->middleware('permission:exporterUtilisateurs');
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
        Route::get('/', [ActivityLogController::class, 'index'])->name('index')->middleware('permission:voirJournal');
        Route::get('/{activity}', [ActivityLogController::class, 'view'])->name('view')->middleware('permission:voirJournal');
    });

    // Products
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductsController::class, 'index'])->name('index')->middleware('permission:voirProduits');
        Route::get('/create', [ProductsController::class, 'create'])->name('create')->middleware('permission:creerProduits');
        Route::post('/', [ProductsController::class, 'store'])->name('store')->middleware('permission:creerProduits');
        Route::get('/export', [ProductsController::class, 'export'])->name('export')->middleware('permission:exporterProduits');
        Route::get('lowStock', [ProductsController::class, 'lowStock'])->name('lowStock')->middleware('permission:exporterProduits');
        Route::get('/single-export/{id}', [ProductsController::class, 'exportSingle'])->name('single_export')->middleware('permission:exporterProduits');
        Route::get('/{id}/show', [ProductsController::class, 'show'])->name('show')->middleware('permission:voirProduits');
        Route::get('/{id}/edit', [ProductsController::class, 'edit'])->name('edit')->middleware('permission:modifierProduits');
        Route::put('/{id}', [ProductsController::class, 'update'])->name('update')->middleware('permission:modifierProduits');
        Route::delete('/{id}', [ProductsController::class, 'destroy'])->name('destroy')->middleware('permission:supprimerProduits');
    });
    
    // Categories
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoriesController::class, 'index'])->name('index')->middleware('permission:voirCategories');
        Route::get('/create', [CategoriesController::class, 'create'])->name('create')->middleware('permission:creerCategories');
        Route::post('/', [CategoriesController::class, 'store'])->name('store')->middleware('permission:creerCategories');
        Route::put('/{id}', [CategoriesController::class, 'update'])->name('update')->middleware('permission:modifierCategories');
        Route::delete('/{id}', [CategoriesController::class, 'destroy'])->name('destroy')->middleware('permission:supprimerCategories');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index')->middleware('permission:voirNotifications');
        Route::post('/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('markAsRead')->middleware('permission:voirNotifications');
    });

    // Order Status Update
    Route::post('/status/{id}', function (Illuminate\Http\Request $request, $id) {
        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Statut de la commande mis à jour avec succès.');
    })->name('orders.status')->middleware('permission:modifierVentes');

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::put('/', [SettingController::class, 'update'])->name('update');
    });
});

// Fallback Route
Route::fallback(function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});
?>