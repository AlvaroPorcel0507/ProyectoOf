<?php

use App\Http\Controllers\ActivitiesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CustomersController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});


Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Rutas de Usuarios
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::put('/users/{user}/softDelete', [UserController::class, 'delete'])->name('users.softDelete');
    Route::get('/users/profile', [UserController::class, 'profile'])->name('users.profile');
});

// Rutas de Categorias
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/categories', [CategoriesController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoriesController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoriesController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoriesController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoriesController::class, 'update'])->name('categories.update');
    Route::put('/categories/{category}/softDelete', [CategoriesController::class, 'delete'])->name('categories.softDelete');
});

// Rutas de Productos
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductsController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductsController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductsController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductsController::class, 'update'])->name('products.update');
    Route::put('/products/{product}/softDelete', [ProductsController::class, 'delete'])->name('products.softDelete');
});

// Rutas de Clientes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/customers', [CustomersController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomersController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomersController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}/edit', [CustomersController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomersController::class, 'update'])->name('customers.update');
    Route::put('/customers/{customer}/softDelete', [CustomersController::class, 'delete'])->name('customers.softDelete');
});

// Rutas de Actividades
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/activities', [ActivitiesController::class, 'index'])->name('activities.index');
    Route::get('/activities/create', [ActivitiesController::class, 'create'])->name('activities.create');
    Route::post('/activities', [ActivitiesController::class, 'store'])->name('activities.store');
    Route::get('/activities/{activity}/edit', [ActivitiesController::class, 'edit'])->name('activities.edit');
    Route::put('/activities/{activity}', [ActivitiesController::class, 'update'])->name('activities.update');
    Route::put('/activities/{activity}/softDelete', [ActivitiesController::class, 'delete'])->name('activities.softDelete');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

require __DIR__.'/auth.php';
