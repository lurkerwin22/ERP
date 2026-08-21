<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\Ai\AiAssistantController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\DebtController;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create']);
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::delete('/logout', [SessionController::class, 'destroy'])->name('logout');

    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::patch('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/products/category/{category}', [ProductController::class, 'category'])->name('products.category');

    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::patch('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Stock Management
    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('/products/{product}/stock', [StockController::class, 'movements'])->name('stock.movements');
    Route::get('/products/{product}/stock/adjust', [StockController::class, 'adjust'])->name('stock.adjust');
    Route::post('/products/{product}/stock/add', [StockController::class, 'addStock'])->name('stock.add');
    Route::post('/products/{product}/stock/remove', [StockController::class, 'removeStock'])->name('stock.remove');

    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::patch('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    // Sales
    Route::resource('sales', SaleController::class);
    Route::patch('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
    Route::get('/sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');
    Route::get('/sales/{sale}/invoice', [SaleController::class, 'invoice'])->name('sales.invoice');

    // Payments
    Route::post('/sales/{sale}/payments', [PaymentController::class, 'store'])->name('sales.payments.store');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

    // Quotes
    Route::resource('quotes', QuoteController::class);
    Route::post('/quotes/{quote}/convert', [QuoteController::class, 'convert'])->name('quotes.convert');
    Route::patch('/quotes/{quote}/status', [QuoteController::class, 'updateStatus'])->name('quotes.update-status');

    //Debt 
    Route::get('/debts', [DebtController::class, 'index'])->name('debts.index');

    // AI Assistant
   
    Route::get('/ai-assistant', [AiAssistantController::class, 'index'])->name('ai.index');
    Route::post('/ai-assistant/chat', [AiAssistantController::class, 'chat'])->name('ai.chat');

    // Search API Route for New Sale Live Search
    Route::get('/api/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::post('/ai/clear', [AiAssistantController::class, 'clear'])->name('ai.clear');

    // API Reports
    Route::get('/stock-alerts', [ReportController::class, 'stockAlerts'])->name('reports.stock');
    Route::get('/monthly-sales', [ReportController::class, 'monthlySales'])->name('reports.sales');
    Route::get('/customer-debts', [ReportController::class, 'customerDebts'])->name('reports.debts');

});