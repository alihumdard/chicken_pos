<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\ReportController;
// -- NEW CONTROLLERS --
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SupplierCustomerController;
use App\Http\Controllers\UserController;
use Faker\Guesser\Name;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [AuthController::class, 'index']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('register', [AuthController::class, 'register']);
Route::match(['get', 'post'], 'logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('verify-otp', [ForgotPasswordController::class, 'showOtpForm'])->name('password.otp.form');
Route::post('verify-otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.otp.verify');
Route::get('reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
Route::post('reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ADMIN ROUTES
    Route::prefix('admin')->name('admin.')->group(function () {

        // 1. System Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::patch('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');

        // 2. Supplier & Customer Management
        Route::get('contacts', [SupplierCustomerController::class, 'index'])->name('contacts.index');
        Route::post('contacts', [SupplierCustomerController::class, 'store'])->name('contacts.store');
        Route::delete('contacts/{contact}', [SupplierCustomerController::class, 'destroy'])->name('contacts.destroy');
        Route::get('contacts/create', [SupplierCustomerController::class, 'create'])->name('contacts.create');

        // 3. Purchase (Daily Batches)
        Route::get('purchases', [PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
        Route::post('purchases', [PurchaseController::class, 'store'])->name('purchases.store');
        Route::delete('purchases/{purchase}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');

        // 4. Sell (POS & Wholesale)
        Route::get('rates', [RateController::class, 'index'])->name('rates.index');
        Route::get('rates/create', [RateController::class, 'create'])->name('rates.create');
        Route::post('rates', [RateController::class, 'store'])->name('rates.store');
        Route::delete('rates/{rate}', [RateController::class, 'destroy'])->name('rates.destroy');

        // NEW AJAX ROUTE FOR DYNAMIC SUPPLIER DATA
        Route::post('rates/supplier-data', [RateController::class, 'getSupplierData'])->name('rates.supplier.data');

        Route::get('sales', [SalesController::class, 'index'])->name('sales.index');
        Route::get('sales/create', [SalesController::class, 'create'])->name('sales.create');
        Route::post('sales', [SalesController::class, 'store'])->name('sales.store');
        // 🚨 CORRECTED: Changed {rate} parameter to {sale} 
        Route::delete('sales/{sale}', [SalesController::class, 'destroy'])->name('sales.destroy'); 

        // 5. Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/profit-loss', [ReportController::class, 'profitLossReportDynamic'])->name('reports.pnl');
        Route::get('/reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
        
        // Dynamic Report Routes
        Route::get('/reports/purchase', [ReportController::class, 'purchaseReport'])->name('reports.purchase');
        Route::post('/reports/purchase/filter', [ReportController::class, 'filterPurchaseReport'])->name('reports.purchase.filter');
        Route::get('/reports/sell-summary', [ReportController::class, 'sellSummaryReport'])->name('reports.sell.summary');
    });
});