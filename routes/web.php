<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchesController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\PurchasesController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReturnsController;
use App\Http\Controllers\SalaryAdvanceController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SuppliersController;
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
    if (auth()->check()) {
        if (auth()->user()->usertype == 'admin') {
            return redirect()->route('admin.home');
        }
        if (auth()->user()->usertype == 'cashier') {
            return redirect()->route('sales.index');
        }
    };
    // return view('ecom.index');
    return view('auth.login');
});

Route::get('/login', [AuthController::class, 'loginIndex'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::group(['middleware' => ['auth', 'admin']], function () {
    Route::get('/admin/home', [HomeController::class, 'admin'])->name('admin.home');
    Route::post('/change_branch', [HomeController::class, 'change_branch'])->name('change_branch');
    Route::post('/change-date', [HomeController::class, 'admin'])->name('change_date');
});
Route::group(['middleware' => ['auth', 'cashier']], function () {
    Route::get('/cashier/home', [HomeController::class, 'cashier'])->name('cashier.home');
});

Route::group(['prefix' => 'inventory', 'middleware' => ['auth', 'admin']], function () {
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

Route::group(['prefix' => 'purchases', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/index', [PurchasesController::class, 'index'])->name('purchase.index');
    Route::get('/details/{date}', [PurchasesController::class, 'details'])->name('purchase.details');
    Route::post('/store', [PurchasesController::class, 'store'])->name('purchase.store');
    Route::get('/create', [PurchasesController::class, 'create'])->name('purchase.create');


    Route::get('/shopping_list/index', [ShoppingListController::class, 'index'])->name('shopping_list.index');
    Route::post('/shopping_list/store', [ShoppingListController::class, 'store'])->name('shopping_list.store');
    Route::get('/shopping_list/edit/{id}', [ShoppingListController::class, 'edit'])->name('shopping_list.edit');
    Route::post('/shopping_list/update/{id}', [ShoppingListController::class, 'update'])->name('shopping_list.update');
    Route::post('/shopping_list/delete', [ShoppingListController::class, 'delete'])->name('shopping_list.delete');
    Route::post('/shopping_list/fetch-shopping_list', [ShoppingListController::class, 'fetchList'])->name('fetch-shopping_list');


    Route::post('/fetch-branch-stocks', [PurchasesController::class, 'fetchStocks'])->name('fetch-branch-stocks');
    Route::post('/fetch-purchases', [PurchasesController::class, 'fetchPurchases'])->name('fetch-purchases');

});

Route::group(['prefix' => 'sales', 'middleware' => ['auth', 'staff']], function () {
    Route::get('/index', [SalesController::class, 'index'])->name('sales.index');
    Route::post('/store', [SalesController::class, 'store'])->name('sales.store');
    Route::post('/sales/details', [SalesController::class, 'details']);
    Route::post('/fetch-sales', [SalesController::class, 'fetchSales'])->name('fetch-sales');
    Route::post('/refresh-table', [SalesController::class, 'refresh'])->name('refresh-table');
    Route::post('/refresh-receipt', [SalesController::class, 'loadReceipt'])->name('refresh-receipt');
    Route::get('/credit_sales/index', [SalesController::class, 'creditIndex'])->name('credit.index');
    Route::post('/credit_sales/store', [SalesController::class, 'creditStore'])->name('credit.store');
    Route::post('/fetch-balance', [SalesController::class, 'fetchBalance'])->name('fetch-balance');
    Route::get('/credit/index', [SalesController::class, 'index'])->name('sales.credit.index');
    Route::post('/credit/store', [SalesController::class, 'store'])->name('sales.credit.store');
    Route::get('/all/index', [SalesController::class, 'allIndex'])->name('sales.all.index');

});

Route::group(['prefix' => 'estimate', 'middleware' => ['auth', 'staff']], function () {
    Route::get('/index', [EstimateController::class, 'index'])->name('estimate.index');
    Route::post('/store', [EstimateController::class, 'store'])->name('estimate.store');
    Route::post('/refresh-table-estimate', [EstimateController::class, 'refresh'])->name('refresh-table-estimate');
    Route::post('/refresh-receipt-estimate', [EstimateController::class, 'loadReceipt'])->name('refresh-receipt-estimate');

    Route::get('/all/index', [EstimateController::class, 'allIndex'])->name('estimate.all.index');
    Route::post('/all/store', [EstimateController::class, 'allStore'])->name('estimate.all.store');

});

Route::group(['prefix' => 'users', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/index', [UsersController::class, 'index'])->name('users.index');
    Route::post('/store', [UsersController::class, 'store'])->name('users.store');
    Route::post('/delete', [UsersController::class, 'delete'])->name('users.delete');
    Route::get('/edit/{id}', [UsersController::class, 'edit'])->name('users.edit');
    Route::post('/update/{id}', [UsersController::class, 'update'])->name('users.update');
});

Route::group(['prefix' => 'suppliers', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/index', [SuppliersController::class, 'index'])->name('suppliers.index');
    Route::post('/store', [SuppliersController::class, 'store'])->name('suppliers.store');
    Route::post('/delete', [SuppliersController::class, 'delete'])->name('suppliers.delete');
    Route::get('/edit/{id}', [SuppliersController::class, 'edit'])->name('suppliers.edit');
    Route::post('/update/{id}', [SuppliersController::class, 'update'])->name('suppliers.update');
});



Route::group(['prefix' => 'reports', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/index', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/generate', [ReportController::class, 'generate'])->name('reports.generate');
});

Route::group(['prefix' => 'print', 'middleware' => ['auth', 'staff']], function () {
    Route::get('/receipt/{id}/', [PrintController::class, 'receipt'])->name('print.receipt');
});

Route::group(['prefix' => 'branches', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/index', [BranchesController::class, 'index'])->name('branches.index');
    Route::post('/store', [BranchesController::class, 'store'])->name('branches.store');
    Route::post('/delete', [BranchesController::class, 'delete'])->name('branches.delete');
});

Route::group(['prefix' => 'returns', 'middleware' => ['auth', 'staff']], function () {
    Route::get('/index', [ReturnsController::class, 'index'])->name('returns');
    Route::post('/record', [ReturnsController::class, 'store'])->name('returns.record');
    Route::post('/refresh-table-return', [ReturnsController::class, 'refresh'])->name('refresh-table-return');
    Route::post('/refresh-receipt-return', [ReturnsController::class, 'loadReceipt'])->name('refresh-receipt-return');
    Route::get('/all/index', [ReturnsController::class, 'allIndex'])->name('returns.all');
});
Route::group(['prefix' => 'report', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/index', [ReportController::class, 'index'])->name('report.index');
    Route::post('/generate', [ReportController::class, 'generate'])->name('report.generate');
});

Route::group(['prefix' => 'expense', 'middleware' => ['auth', 'staff']], function () {
    Route::get('/index', [ExpensesController::class, 'index'])->name('expense.index');
    Route::post('/store', [ExpensesController::class, 'store'])->name('expense.store');
});
Route::group(['prefix' => 'customers', 'middleware' => ['auth', 'staff']], function () {
    Route::get('/index', [UsersController::class, 'customersIndex'])->name('customers.index');
    Route::post('/store', [UsersController::class, 'customerStore'])->name('customers.store');
    Route::get('/profile/{id}', [UsersController::class, 'customerProfile'])->name('customers.profile');
    Route::post('/save_payment', [UsersController::class, 'savePayment'])->name('customers.save.payment');
    Route::post('/save_deposit', [UsersController::class, 'saveDeposit'])->name('customers.save.deposit');
    Route::post('/load-receipt', [UsersController::class, 'loadReceipt'])->name('load-receipt');
    Route::post('/delete', [UsersController::class, 'deleteCustomer'])->name('customers.delete');

    Route::get('/return', [UsersController::class, 'returnIndex'])->name('users.return.index');
    Route::post('/return', [UsersController::class, 'returnStore']);

    Route::get('/cashier/salary_advance', [SalaryAdvanceController::class, 'cashierIndex'])->name('cashier.salary_advance.index');
    Route::post('/cashier/salary_advance', [SalaryAdvanceController::class, 'cashierStore']);

    Route::get('/admin/salary_advance', [SalaryAdvanceController::class, 'adminIndex'])->name('admin.salary_advance.index');

    Route::post('/admin/salary_advance/approve', [SalaryAdvanceController::class, 'approve'])->name('cashier.salary_advance.approve');
    Route::post('/admin/salary_advance/reject', [SalaryAdvanceController::class, 'reject'])->name('cashier.salary_advance.reject');
    Route::post('/admin/salary_advance/delete', [SalaryAdvanceController::class, 'delete'])->name('cashier.salary_advance.delete');


});
Route::get('/post-data', [ApiController::class, 'store'])->name('post-data');

Route::get('/send-data', function () {

    // create a new curl session
    $curl = curl_init();

// get the CSRF token from Laravel
    $csrf_token = file_get_contents('https://elhabibplumbing.com/csrf-cookie');

// set the request options, including the CSRF token as a header
    $url = 'https://elhabibplumbing.com/post-data';
    $data = array(
        'regno' => '08033174228',
        'api_key' => 'K92@218$%_712bn',
    );
    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'X-CSRF-TOKEN: ' . $csrf_token
        ],
    );
    curl_setopt_array($curl, $options);

// send the request and receive the response
    $response = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

// close the curl session
    curl_close($curl);

    if ($httpcode == 200) {
        $data = json_decode($response, true);
        // process the response data as needed
        return $data;
    } else {
        $error = $response;
        return $error;
        // handle the error response as needed
    }

    // return $json÷;
    return 'this passed';

})->name('send-data');


Route::get('/fetch_stocks',  [ReportController::class, 'fetchStocks'])->name('fetch_stocks');

