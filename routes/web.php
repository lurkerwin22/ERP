<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistredUserController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\SessionController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegistredUserController::class, 'create']);
    Route::post('/register', [RegistredUserController::class, 'store']);

    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store']);
});

Route::delete('/logout', [SessionController::class, 'destroy'])
    ->middleware('auth');


/*
|--------------------------------------------------------------------------
| Products
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // When visiting /products → show categories
    Route::get('/products', [ProductsController::class, 'index'])
        ->name('products.index');

    // Create product
    Route::get('/products/create', [ProductsController::class, 'create'])
        ->name('products.create');

    Route::post('/products', [ProductsController::class, 'store'])
        ->name('products.store');

    // Edit product
    Route::get('/products/{product}/edit', [ProductsController::class, 'edit'])
        ->name('products.edit');

    Route::patch('/products/{product}', [ProductsController::class, 'update'])
        ->name('products.update');

    Route::delete('/products/{product}', [ProductsController::class, 'destroy'])
        ->name('products.destroy');

    // Click a category → show its products
    Route::get('/products/categorie/{categorie}', [ProductsController::class, 'categorie'])
    ->name('products.categorie');
});


/*
|--------------------------------------------------------------------------
| Categories
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/categories', [CategorieController::class, 'index'])
        ->name('categories.index');

    Route::get('/categories/create', [CategorieController::class, 'create'])
        ->name('categories.create');

    Route::post('/categories', [CategorieController::class, 'store'])
        ->name('categories.store');

    Route::get('/categories/{categorie}', [CategorieController::class, 'show'])
        ->name('categories.show');

    Route::get('/categories/{categorie}/edit', [CategorieController::class, 'edit'])
        ->name('categories.edit');

    Route::patch('/categories/{categorie}', [CategorieController::class, 'update'])
        ->name('categories.update');

    Route::delete('/categories/{categorie}', [CategorieController::class, 'destroy'])
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