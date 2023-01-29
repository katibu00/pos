<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchesController;
use App\Http\Controllers\DebtorsController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\PurchasesController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReturnsController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\Settings\BanksController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/login', [AuthController::class, 'loginIndex'])->name('login');
Route::post('/login', [AuthController::class, 'login']);


Route::get('/logout', [AuthController::class, 'index'])->name('logout');



Route::group(['middleware' => ['auth', 'admin']], function(){
    Route::get('/admin/home', [HomeController::class, 'admin'])->name('admin.home');
});
Route::group(['middleware' => ['auth', 'cashier']], function(){
    Route::get('/cashier/home', [HomeController::class, 'cashier'])->name('cashier.home');
});

Route::group(['prefix' => 'inventory', 'middleware' => ['auth', 'admin']], function(){
    Route::get('/index', [StockController::class, 'index'])->name('stock.index');
    Route::post('/store', [StockController::class, 'store'])->name('stock.store');
    Route::get('/edit/{id}', [StockController::class, 'edit'])->name('stock.edit');
    Route::get('/copy/{id}', [StockController::class, 'copyIndex'])->name('inventory.copy');
    Route::post('/update/{id}', [StockController::class, 'update'])->name('stock.update');
    Route::post('/copy', [StockController::class, 'copyStore'])->name('stock.copy');
    Route::post('/delete', [StockController::class, 'delete'])->name('stock.delete');

    Route::post('/fetch-stocks', [StockController::class, 'fetchStocks'])->name('fetch-stocks');


});

//pagination routes
Route::get('/paginate-stocks', [StockController::class, 'paginate']);
Route::get('/paginate-purchases', [PurchasesController::class, 'fetchPurchases']);

Route::post('/search-stocks', [StockController::class, 'Search'])->name('search-stocks');


Route::group(['prefix' => 'purchases', 'middleware' => ['auth', 'admin']], function(){
    Route::get('/index', [PurchasesController::class, 'index'])->name('purchase.index');
    Route::get('/details/{date}', [PurchasesController::class, 'details'])->name('purchase.details');
    Route::post('/store', [PurchasesController::class, 'store'])->name('purchase.store');
    Route::post('/fetch-branch-stocks', [PurchasesController::class, 'fetchStocks'])->name('fetch-branch-stocks');
    Route::post('/fetch-purchases', [PurchasesController::class, 'fetchPurchases'])->name('fetch-purchases');


});

Route::group(['prefix' => 'sales', 'middleware' => ['auth', 'staff']], function(){
    Route::get('/index', [SalesController::class, 'index'])->name('sales.index');
    Route::post('/store', [SalesController::class, 'store'])->name('sales.store');
    Route::post('/sales/details', [SalesController::class, 'details']);

    Route::post('/fetch-sales', [SalesController::class, 'fetchSales'])->name('fetch-sales');

    Route::post('/refresh-table', [SalesController::class, 'refresh'])->name('refresh-table');
    Route::post('/refresh-receipt', [SalesController::class, 'loadReceipt'])->name('refresh-receipt');

    // Route::post('/admin-search-sales', [SalesController::class, 'adminSearch'])->name('admin-search-sales');

    // Route::post('/cashier-search-sales', [SalesController::class, 'cashierSearch'])->name('cashier-search-sales');
});

Route::group(['prefix' => 'estimate', 'middleware' => ['auth', 'staff']], function(){
    Route::get('/index', [SalesController::class, 'index'])->name('sales.index');
    Route::post('/store', [SalesController::class, 'store'])->name('sales.store');

});



Route::group(['prefix' => 'settings/banks', 'middleware' => ['auth', 'admin']], function(){
    Route::get('/index', [BanksController::class, 'index'])->name('banks.index');
    Route::post('/store', [BanksController::class, 'store'])->name('banks.store');
});



Route::group(['prefix' => 'debtors', 'middleware' => ['auth', 'admin']], function(){
    Route::get('/index', [DebtorsController::class, 'index'])->name('debtors.index');
    Route::post('/store', [DebtorsController::class, 'store'])->name('debtors.store');
});

Route::group(['prefix' => 'users', 'middleware' => ['auth', 'admin']], function(){
    Route::get('/index', [UsersController::class, 'index'])->name('users.index');
    Route::post('/store', [UsersController::class, 'store'])->name('users.store');
    Route::post('/delete', [UsersController::class, 'delete'])->name('users.delete');
    Route::get('/edit/{id}', [UsersController::class, 'edit'])->name('users.edit');
    Route::post('/update/{id}', [UsersController::class, 'update'])->name('users.update');
});
Route::group(['prefix' => 'reports', 'middleware' => ['auth', 'admin']], function(){
    Route::get('/index', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/generate', [ReportController::class, 'generate'])->name('reports.generate');
});

Route::group(['prefix' => 'print', 'middleware' => ['auth', 'staff']], function(){
    Route::get('/receipt/{id}/', [PrintController::class, 'receipt'])->name('print.receipt');
});

Route::group(['prefix' => 'branches', 'middleware' => ['auth', 'admin']], function(){
    Route::get('/index', [BranchesController::class, 'index'])->name('branches.index');
    Route::post('/store', [BranchesController::class, 'store'])->name('branches.store');
    Route::post('/delete', [BranchesController::class, 'delete'])->name('branches.delete');
});

Route::group(['prefix' => 'returns', 'middleware' => ['auth', 'staff']], function(){
    Route::get('/index/{id}', [ReturnsController::class, 'index'])->name('returns');
    Route::post('/record', [ReturnsController::class, 'record'])->name('returns.record');
});
