<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;


Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create']);
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store']);
});

Route::delete('/logout', [SessionController::class, 'destroy'])
    ->middleware('auth');


/*
|--------------------------------------------------------------------------
| Product
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // When visiting /products → show categories
    Route::get('/products', [ProductController::class, 'index'])
        ->name('products.index');

    // Create product
    Route::get('/products/create', [ProductController::class, 'create'])
        ->name('products.create');

    Route::post('/products', [ProductController::class, 'store'])
        ->name('products.store');

    // Edit product
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
        ->name('products.edit');

    Route::patch('/products/{product}', [ProductController::class, 'update'])
        ->name('products.update');

    Route::delete('/products/{product}', [ProductController::class, 'destroy'])
        ->name('products.destroy');

    // Click a category → show its products
    Route::get('/products/category/{category}', [ProductController::class, 'category'])
    ->name('products.category');
});


/*
|--------------------------------------------------------------------------
| Categories
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/categories', [CategoryController::class, 'index'])
        ->name('categories.index');

    Route::get('/categories/create', [CategoryController::class, 'create'])
        ->name('categories.create');

    Route::post('/categories', [CategoryController::class, 'store'])
        ->name('categories.store');

    Route::get('/categories/{category}', [CategoryController::class, 'show'])
        ->name('categories.show');

    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])
        ->name('categories.edit');

    Route::patch('/categories/{category}', [CategoryController::class, 'update'])
        ->name('categories.update');

    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
        ->name('categories.destroy');
});
 
Route::middleware('auth')->group(function (){
    /*
    |--------------------------------------------------------------------------
    | Stock Management
    |--------------------------------------------------------------------------
    */
    Route::get('/stock', [StockController::class, 'index'])
        ->name('stock.index');

    Route::get('/products/{product}/stock', [StockController::class, 'movements'])
        ->name('stock.movements');

    Route::get('/products/{product}/stock/adjust', [StockController::class, 'adjust'])
        ->name('stock.adjust');

    Route::post('/products/{product}/stock/add', [StockController::class, 'addStock'])
        ->name('stock.add');

    Route::post('/products/{product}/stock/remove', [StockController::class, 'removeStock'])
        ->name('stock.remove');
});

Route::middleware('auth')->group(function (){
    /*
    |--------------------------------------------------------------------------
    | Customers
    |--------------------------------------------------------------------------
    */
    Route::get('/customers', [CustomerController::class, 'index'])
        ->name('customers.index');

    Route::get('/customers/create', [CustomerController::class, 'create'])
        ->name('customers.create');

    Route::post('/customers', [CustomerController::class, 'store'])
        ->name('customers.store');

    Route::get('/customers/{customer}', [CustomerController::class, 'show'])
        ->name('customers.show');

    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])
        ->name('customers.edit');

    Route::patch('/customers/{customer}', [CustomerController::class, 'update'])
        ->name('customers.update');

    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])
        ->name('customers.destroy');
});

Route::middleware('auth')->group(function (){
    Route::resource('sales', SaleController::class);
    Route::patch('/sales/{sale}/cancel', [SaleController::class, 'cancel'])
        ->name('sales.cancel');
    Route::get('/sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');
    Route::get('sales/{sale}/invoice', [SaleController::class, 'invoice'])->name('sales.invoice');
});