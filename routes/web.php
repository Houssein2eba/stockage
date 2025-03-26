<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\Users\UsersController;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth:web,admin', 'verified'])->name('dashboard');

Route::middleware(['auth:web,admin','can:admin'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/users',[UsersController::class,'index'])->name('users.index');
Route::get('/users/create',[UsersController::class,'create'])->name('users.create');
Route::post('/users/create',[UsersController::class,'store'])->name('users.store');
Route::delete('/users/{id}',[UsersController::class,'delete'])->name('users.delete');
Route::get('/users/{id}',[UsersController::class,'edit'])->name('users.edit');
Route::put('/users/{id}',[UsersController::class,'update'])->name('users.update');


Route::get('/roles',[RolesController::class,'index'])->name('roles.index');
Route::get('/roles/create',[RolesController::class,'create'])->name('roles.create')->can('create');
Route::post('/roles/create',[RolesController::class,'store'])->name('roles.store');
Route::delete('/roles/{id}',[RolesController::class,'destroy'])->name('roles.destroy');
Route::get('/roles/{id}',[RolesController::class,'edit'])->name('roles.edit');
Route::put('/roles/{id}',[RolesController::class,'update'])->name('roles.update');
});


require __DIR__.'/auth.php';
