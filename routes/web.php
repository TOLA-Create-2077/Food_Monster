<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
// 🔒 protected pages
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    });
});

Route::get('/', [DashboardController::class, 'index']);

Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
Route::post('/pos/store', [POSController::class, 'store'])->name('pos.store');


Route::get('/order', [OrderController::class, 'index'])->name('order.index');
Route::get('/order/{id}', [OrderController::class, 'show'])->name('order.show');
Route::get('/order/{id}/edit', [OrderController::class, 'edit'])->name('order.edit');
Route::put('/order/{id}', [OrderController::class, 'update'])->name('order.update');
Route::get('/order/{id}/receipt', [OrderController::class, 'receipt'])->name('order.receipt');
Route::get('/order/{id}/bill', [OrderController::class, 'bill'])->name('order.bill');
Route::post('/order/{id}/status', [OrderController::class, 'updateStatus'])->name('order.status');


use App\Http\Controllers\CategoryController;

Route::get('/customers/categories', [CategoryController::class,'index']);

Route::get('/products',[ProductController::class,'index'])->name('products.index');
Route::get('/products/create',[ProductController::class,'create'])->name('products.create');
Route::post('/products/store',[ProductController::class,'store'])->name('products.store');


Route::post('/order/update-status/{id}',[OrderController::class,'updateStatus']);
Route::get('/invoice', function () {
    return view('invoice');
});

Route::get('/receipt', function () {
    return view('receipt');
});
Route::get('/customers/categories', function () {
    return view('customers.categories');
});
Route::get('/customers/products', function () {
    return view('customers.products');
});