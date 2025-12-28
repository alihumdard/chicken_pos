<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PoultryController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\ReportController;
// -- NEW CONTROLLERS --
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StockTransferController;
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

        Route::prefix('contacts')->name('contacts.')->group(function () {
            Route::get('/', [SupplierCustomerController::class, 'index'])->name('index');
            Route::post('/', [SupplierCustomerController::class, 'store'])->name('store');
            Route::put('/{id}', [SupplierCustomerController::class, 'update'])->name('update');
            Route::delete('/{id}', [SupplierCustomerController::class, 'destroy'])->name('destroy');
            Route::get('/create', [SupplierCustomerController::class, 'create'])->name('create');
        });
        Route::put('ledger/{id}', [SupplierCustomerController::class, 'updateLedger'])->name('admin.ledger.update');
        Route::delete('ledger/{id}', [SupplierCustomerController::class, 'destroyLedger'])->name('admin.ledger.destroy');
        // 3. Purchase (Daily Batches)
        Route::get('purchases', [PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
        Route::post('purchases', [PurchaseController::class, 'store'])->name('purchases.store');
        Route::delete('purchases/{purchase}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');

        // 3. Stock Transfer
        Route::get('stocks', [StockTransferController::class, 'index'])->name('stocks.index');
        Route::post('/stock/transfer', [StockTransferController::class, 'create'])->name('stock.create');
        Route::post('stocks', [StockTransferController::class, 'store'])->name('stock.transfer.store');
        Route::delete('stocks/{stock}', [StockTransferController::class, 'destroy'])->name('stock.destroy');
        Route::post('/stock/adjustment', [StockTransferController::class, 'storeAdjustment'])->name('stock.adjustment.store');

        // 4. Sell (POS & Wholesale)
        Route::get('rates', [RateController::class, 'index'])->name('rates.index');
        Route::get('rates/create', [RateController::class, 'create'])->name('rates.create');
        Route::post('rates', [RateController::class, 'store'])->name('rates.store');
        Route::delete('rates/{rate}', [RateController::class, 'destroy'])->name('rates.destroy');

        // NEW AJAX ROUTE FOR DYNAMIC SUPPLIER DATA
        Route::post('rates/supplier-data', [RateController::class, 'getSupplierData'])->name('rates.supplier.data');

        // SALES ROUTES
        Route::get('sales', [SalesController::class, 'index'])->name('sales.index');
        Route::get('sales/create', [SalesController::class, 'create'])->name('sales.create');
        Route::post('sales', [SalesController::class, 'store'])->name('sales.store');
        Route::delete('sales/{sale}', [SalesController::class, 'destroy'])->name('sales.destroy');

        // 🟢 FIX: NEW ROUTE FOR SYNCHRONIZATION
        Route::get('sales/fetch-rates', [SalesController::class, 'getLatestRates'])->name('sales.fetch-rates');

        // 5. Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/profit-loss', [ReportController::class, 'profitLossReportDynamic'])->name('reports.pnl');
        Route::get('/reports/stock', [ReportController::class, 'stock'])->name('reports.stock');

        // Dynamic Report Routes
        Route::get('/reports/purchase', [ReportController::class, 'purchaseReport'])->name('reports.purchase');
        Route::post('/reports/purchase/filter', [ReportController::class, 'filterPurchaseReport'])->name('reports.purchase.filter');
        Route::get('/reports/sell-summary', [ReportController::class, 'sellSummaryReport'])->name('reports.sell.summary');

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');

        Route::get('/stock-moniter', function () {
            return view('pages.stock_moniter');
        })->name('stock.index');

        Route::get('/rates', [RateController::class, 'index'])->name('rates.index');
        Route::post('/rates', [RateController::class, 'store'])->name('rates.store');

        // 🟢 NEW FORMULA ROUTES
        Route::get('/settings/rates/formulas', [RateController::class, 'getRateFormulas'])->name('rates.formulas.get');
        Route::post('/settings/rates/formulas', [RateController::class, 'updateRateFormula'])->name('rates.formulas.update');

        Route::get('/poultry', [PoultryController::class, 'index'])->name('poultry.index');
        Route::post('/poultry', [PoultryController::class, 'store'])->name('poultry.store');
        Route::get('/poultry/{id}/edit', [PoultryController::class, 'edit'])->name('poultry.edit');
        Route::put('/poultry/{id}', [PoultryController::class, 'update'])->name('poultry.update');
        Route::delete('/poultry/{id}', [PoultryController::class, 'destroy'])->name('poultry.destroy');

        Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
        Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
        Route::get('/expenses/{id}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
        Route::put('/expenses/{id}', [ExpenseController::class, 'update'])->name('expenses.update');
        Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    });
});
Route::get('/admin/suppliers/{id}/ledger', [SupplierCustomerController::class, 'getLedger']);
Route::post('/admin/suppliers/payment', [SupplierCustomerController::class, 'storePayment']);
Route::resource('admin/purchases', App\Http\Controllers\PurchaseController::class, ['as' => 'admin']);
Route::get('/admin/suppliers/{id}/ledger', [SupplierCustomerController::class, 'getSupplierLedger']);
Route::get('/admin/customers/{id}/ledger', [SupplierCustomerController::class, 'getCustomerLedger']);
Route::post('/admin/customers/payment', [SupplierCustomerController::class, 'storePayment']);
Route::get('/admin/reports/sell-monthly', [ReportController::class, 'monthlySalesReport'])->name('admin.reports.sell.monthly');
Route::get('/admin/reports/profit-loss', [ReportController::class, 'profitLossReport'])->name('admin.reports.pnl');