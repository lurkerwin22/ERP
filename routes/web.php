<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistredUserController;
use App\Http\Controllers\ProductsController;
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

Route::middleware('auth')->group(function () {
    Route::get('/products', [ProductsController::class, 'index']);
    Route::get('/products/create', [ProductsController::class, 'create']);
    Route::post('/products', [ProductsController::class, 'store']);
    //Route::get('/products/{product}', [ProductsController::class, 'show']);
    Route::get('/products/{product}/edit', [ProductsController::class, 'edit']);
    
    Route::patch('/products/{product}', [ProductsController::class, 'update']);
    Route::delete('/products/{product}', [ProductsController::class, 'destroy']);
});