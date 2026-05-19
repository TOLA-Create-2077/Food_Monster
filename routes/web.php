<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SetMenuController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::post('/pos/store', [POSController::class, 'store'])->name('pos.store');

    Route::get('/order', [OrderController::class, 'index'])->name('order.index');
    Route::get('/order/{id}', [OrderController::class, 'show'])->name('order.show');
    Route::get('/order/{id}/edit', [OrderController::class, 'edit'])->name('order.edit');
    Route::put('/order/{id}', [OrderController::class, 'update'])->name('order.update');
    Route::get('/order/{id}/receipt', [OrderController::class, 'receipt'])->name('order.receipt');
    Route::get('/order/{id}/bill', [OrderController::class, 'bill'])->name('order.bill');
    Route::post('/order/{id}/status', [OrderController::class, 'updateStatus'])->name('order.status');

    Route::get('/item_management/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/item_management/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/item_management/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/item_management/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/item_management/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');

    Route::get('/item_management/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/item_management/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/item_management/products/{id}', [ProductController::class, 'update'])->name('products.update');

    Route::get('/item_management/set-menu', [SetMenuController::class, 'index'])->name('set_menus.index');
    Route::post('/item_management/set-menu', [SetMenuController::class, 'store'])->name('set_menus.store');
    Route::put('/item_management/set-menu/{id}', [SetMenuController::class, 'update'])->name('set_menus.update');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::view('/invoice', 'invoice')->name('invoice');
    Route::view('/receipt', 'receipt')->name('receipt');
});