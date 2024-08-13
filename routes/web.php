<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchesController;
use App\Http\Controllers\CashCreditsController;
use App\Http\Controllers\CashierDashboardController;
use App\Http\Controllers\DataSyncController;
use App\Http\Controllers\DebtorsController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseReportController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\FundTransferController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OnlineStoreCategoryController;
use App\Http\Controllers\OnlineStoreProductController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\PurchasesController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReturnsController;
use App\Http\Controllers\SalaryAdvanceController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\SMSController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SuppliersController;
use App\Http\Controllers\UserOnlineStoreController;
use App\Http\Controllers\UsersController;
use App\Models\OnlineStoreProduct;
use Illuminate\Support\Facades\Route;
use App\Models\OnlineStoreCategory;

use App\Http\Controllers\ExpenseDepositController;
use App\Http\Controllers\ExpenseRecordController;


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

    $products = OnlineStoreProduct::with('onlineProductImages')->get();
    $categories = OnlineStoreCategory::all();

    // Optional: Calculate discount price if not already done
    foreach ($products as $product) {
        if ($product->discount_applied) {
            $product->discount_price = $product->original_price - $product->discount_price;
            $product->selling_price = $product->discount_price;
        }
    }

    return view('frontend.pages.home', compact('products','categories'));


});
// Route::get('/', function () {

//     return view('frontend.pages.home');


//     if (auth()->check()) {
//         if (auth()->user()->usertype == 'admin') {
//             return redirect()->route('admin.home');
//         }
//         if (auth()->user()->usertype == 'cashier') {
//             return redirect()->route('cashier.home');
//         }
//     };
//     // return view('ecom.index');
//     return view('auth.login');
// });

Route::get('/home', function () {
    if (auth()->check()) {
        if (auth()->user()->usertype == 'admin') {
            return redirect()->route('admin.home');
        }
        if (auth()->user()->usertype == 'cashier') {
            return redirect()->route('cashier.home');
        }
    };
    // return view('ecom.index');
    return view('auth.login');
})->name('home');

Route::get('/login', [AuthController::class, 'loginIndex'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/change/password', [AuthController::class, 'changePasswordIndex'])->name('change.password');
Route::post('/change/password', [AuthController::class, 'changePasswordStore']);

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/admin/all', [HomeController::class, 'admin'])->name('admin.home');
    Route::post('/change_branch', [HomeController::class, 'change_branch'])->name('change_branch');
    Route::post('/change-date', [HomeController::class, 'admin'])->name('change_date');


    Route::get('/select-cashier', [CashierDashboardController::class, 'selectCashier'])->name('admin.select_cashier');
    Route::post('/view-cashier-dashboard', [CashierDashboardController::class, 'viewCashierDashboard'])->name('admin.view_cashier_dashboard');

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

    Route::get('/reorder/new', [ShoppingListController::class, 'index'])->name('reorder.index');
    Route::post('/reorder/store', [ShoppingListController::class, 'store'])->name('reorder.store');
    Route::post('/fetch-products', [ShoppingListController::class, 'filterProducts'])->name('fetch-products');

    Route::get('/reorder/all', [ShoppingListController::class, 'allIndex'])->name('reorder.all.index');
    Route::post('/reorder/fetch',  [ShoppingListController::class, 'fetchReorders'])->name('reorders.fetch');

    Route::get('/reorder/complete/{reorder_no}', [ShoppingListController::class, 'complete'])->name('complete.reorder')->where('reorder_no', '.*');
    Route::post('/reorder/complete', [ShoppingListController::class, 'completeSubmit'])->name('complete.reorder.submit');
    Route::post('/reorder/download-pdf', [ShoppingListController::class, 'downloadPDF'])->name('reorder.download-pdf');
    Route::post('/reorder/delete', [ShoppingListController::class, 'destroyReorders'])->name('reorder.delete');
    Route::post('/reorder/details', [ShoppingListController::class, 'details'])->name('reorder.details');
    Route::post('/reorder/update-supplier', [ShoppingListController::class, 'updateSupplier'])->name('reorder.update-supplier');
    Route::post('/reorder/save-expenses', [ShoppingListController::class, 'saveExpenses'])->name('reorder.save-expenses');
    Route::post('/reorder/fetch-profitability-forecast', [ShoppingListController::class, 'profitabilityForecast'])->name('reorder.fetch-profitability-forecast');

    Route::post('/fetch-branch-stocks', [PurchasesController::class, 'fetchStocks'])->name('fetch-branch-stocks');
    Route::post('/fetch-purchases', [PurchasesController::class, 'fetchPurchases'])->name('fetch-purchases');

});

Route::group(['prefix' => 'transactions', 'middleware' => ['auth', 'staff']], function () {
    Route::get('/', [SalesController::class, 'index'])->name('transactions.index');
    Route::post('/store', [SalesController::class, 'store'])->name('transactions.store');
    Route::post('/sales/details', [SalesController::class, 'details']);
    Route::post('/fetch-sales', [SalesController::class, 'fetchSales'])->name('fetch-transactions');
    Route::post('/refresh-table', [SalesController::class, 'refresh'])->name('refresh-table');
    Route::post('/refresh-receipt', [SalesController::class, 'loadReceipt'])->name('refresh-receipt');
    // Route::get('/credit_sales/index', [SalesController::class, 'creditIndex'])->name('credit.index');
    // Route::post('/credit_sales/store', [SalesController::class, 'creditStore'])->name('credit.store');
    Route::post('/fetch-balance', [SalesController::class, 'fetchBalance'])->name('fetch-balance');
    Route::get('/all/index', [SalesController::class, 'allIndex'])->name('sales.all.index');

    Route::post('/all/search', [SalesController::class, 'allSearch'])->name('sales.all.search');
    Route::post('/all/sort', [SalesController::class, 'filterSales'])->name('sales.all.sort');

    Route::post('/awaiting_pickup', [SalesController::class, 'markAwaitingPickup'])->name('sales.awaiting_pickup');
    Route::post('/deliver', [SalesController::class, 'markDeliver'])->name('sales.deliver');

});

Route::group(['prefix' => 'estimate', 'middleware' => ['auth', 'staff']], function () {
    Route::get('/index', [EstimateController::class, 'index'])->name('estimate.index');
    Route::post('/store', [EstimateController::class, 'store'])->name('estimate.store');
    Route::post('/refresh-table-estimate', [EstimateController::class, 'refresh'])->name('refresh-table-estimate');
    Route::post('/refresh-receipt-estimate', [EstimateController::class, 'loadReceipt'])->name('refresh-receipt-estimate');

    Route::get('/all/index', [EstimateController::class, 'allIndex'])->name('estimate.all.index');
    Route::post('/all/store', [EstimateController::class, 'allStore'])->name('estimate.all.store');


    Route::post('/all/search', [EstimateController::class, 'allSearch'])->name('estimate.all.search');
    Route::post('/all/sort', [EstimateController::class, 'filterSales'])->name('estimate.all.sort');

    Route::post('/update', [EstimateController::class, 'update'])->name('estimate.update');
    Route::post('/edit', [EstimateController::class, 'edit'])->name('estimate.edit');



});


Route::group(['prefix' => 'sms', 'middleware' => ['auth', 'staff']], function () {
    Route::get('/compose', [SMSController::class, 'compose'])->name('sms.compose');
    Route::get('/balance', [SMSController::class, 'balance'])->name('sms.balance');
    Route::post('/send', [SMSController::class, 'send'])->name('sms.send');

});

Route::group(['prefix' => 'users', 'middleware' => ['auth', 'staff']], function () {
    Route::get('/index', [UsersController::class, 'index'])->name('users.index');
    Route::post('/store', [UsersController::class, 'store'])->name('users.store');
    Route::post('/delete', [UsersController::class, 'delete'])->name('users.delete');
    Route::get('/edit/{id}', [UsersController::class, 'edit'])->name('users.edit');
    Route::post('/update/{id}', [UsersController::class, 'update'])->name('users.update');

    Route::post('/search', [UsersController::class, 'search'])->name('users.search');////
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

Route::group(['prefix' => 'cash_credits', 'middleware' => ['auth', 'staff']], function () {
    Route::get('/', [CashCreditsController::class, 'index'])->name('cash_credits.index');
    Route::post('/', [CashCreditsController::class, 'store']);


});

Route::get('/credits-history/{customerId}', [CashCreditsController::class, 'show'])->name('credits-history');
Route::get('/fetch-credit-records/{customerId}', [CashCreditsController::class, 'fetchCreditRecords'])->name('fetch-credit-records');
Route::post('/process-credit-payment', [CashCreditsController::class, 'processCreditPayment']);




Route::group(['prefix' => 'print', 'middleware' => ['auth', 'staff']], function () {
    Route::get('/receipt/{id}/', [PrintController::class, 'receipt'])->name('print.receipt');
});

Route::group(['prefix' => 'branches', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/index', [BranchesController::class, 'index'])->name('branches.index');
    Route::post('/store', [BranchesController::class, 'store'])->name('branches.store');
    Route::post('/delete', [BranchesController::class, 'delete'])->name('branches.delete');
});

Route::group(['prefix' => 'returns', 'middleware' => ['auth', 'staff']], function () {
    // Route::get('/index', [ReturnsController::class, 'index'])->name('returns');
    // Route::post('/record', [ReturnsController::class, 'store'])->name('returns.record');
    // Route::post('/refresh-table-return', [ReturnsController::class, 'refresh'])->name('refresh-table-return');
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
    Route::post('/update_deposit', [UsersController::class, 'updateDeposit'])->name('customers.update.deposit');
    Route::post('/load-receipt', [UsersController::class, 'loadReceipt'])->name('load-receipt');
    Route::post('/delete', [UsersController::class, 'deleteCustomer'])->name('customers.delete');
    Route::get('/edit/{id}', [UsersController::class, 'editCustomer'])->name('customers.edit');

    Route::post('/update/{id}', [UsersController::class, 'updateCustomer'])->name('customers.update');


    Route::get('/return', [UsersController::class, 'returnIndex'])->name('users.return.index');
    Route::post('/return', [UsersController::class, 'returnStore']);

    Route::get('/cashier/salary_advance', [SalaryAdvanceController::class, 'cashierIndex'])->name('cashier.salary_advance.index');
    Route::post('/cashier/salary_advance', [SalaryAdvanceController::class, 'cashierStore']);

    Route::get('/admin/salary_advance', [SalaryAdvanceController::class, 'adminIndex'])->name('admin.salary_advance.index');

    Route::post('/admin/salary_advance/approve', [SalaryAdvanceController::class, 'approve'])->name('cashier.salary_advance.approve');
    Route::post('/admin/salary_advance/reject', [SalaryAdvanceController::class, 'reject'])->name('cashier.salary_advance.reject');
    Route::post('/admin/salary_advance/delete', [SalaryAdvanceController::class, 'delete'])->name('cashier.salary_advance.delete');


    Route::get('/cashier/salary_advance/fetch', [SalaryAdvanceController::class, 'fetchSalaryAdvances'])->name('cashier.salary_advance.fetch');


});

Route::get('/fetch_stocks',  [ReportController::class, 'fetchStocks'])->name('fetch_stocks');

Route::get('/data-sync', [DataSyncController::class, 'index'])->name('data-sync.index');
Route::post('/data-sync/send', [DataSyncController::class, 'sendData'])->name('data-sync.send');


Route::get('/get-product-suggestions',[SalesController::class, 'getProductSuggestions']);
Route::get('/fetch-credit-balance', [SalesController::class, 'fetchBalanceOrDeposit']);


Route::group(['prefix' => 'funds-transfer', 'middleware' => ['auth', 'staff']], function () {

Route::get('/', [FundTransferController::class, 'index'])->name('fund_transfer.index');
Route::post('/store', [FundTransferController::class, 'store'])->name('fund_transfer.store');

// Additional routes for editing and deleting transfers if needed
// Route::get('/funds-transfer/{id}/edit', [FundTransferController::class, 'edit'])->name('fund_transfer.edit');
// Route::put('/funds-transfer/{id}/update', [FundTransferController::class, 'update'])->name('fund_transfer.update');
// Route::delete('/funds-transfer/{id}/delete', [FundTransferController::class, 'destroy'])->name('fund_transfer.destroy');

});



Route::group(['prefix' => 'correct-sales', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/', [StockController::class, 'correctIndex'])->name('correct-sales');
    Route::get('fetch-stocks', [StockController::class, 'fetchAllStocks'])->name('fetch.stocks');
    Route::get('fetch-sales', [StockController::class, 'fetchSales'])->name('fetch.sales');

    Route::post('update-buying-price', [StockController::class, 'updateBuyingPrice'])->name('update.buying_price');

});


Route::get('/online-store/copy/{stock}', [OnlineStoreProductController::class, 'copy'])->name('online-store.copy');
Route::post('/online-store/copy/store', [OnlineStoreProductController::class, 'store'])->name('online-store.copy.store');
// Route::post('/online-store/copy/upload', 'OnlineStoreProductController@upload')->name('online-store.copy.upload');



Route::group(['prefix' => 'online-store', 'middleware' => ['auth', 'admin']], function () {

    Route::get('/products', [OnlineStoreProductController::class, 'index'])->name('online-store.products');


    Route::get('/products/{product}', 'ProductController@show')->name('products.show');
    Route::get('/products/{product}/edit',[OnlineStoreProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [OnlineStoreProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [OnlineStoreProductController::class, 'destroy'])->name('products.destroy');

    Route::post('/delete-image/{id}', [OnlineStoreProductController::class, 'deleteImage'])->name('delete_image_route');



    Route::get('/categories', [OnlineStoreCategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [OnlineStoreCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [OnlineStoreCategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [OnlineStoreCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [OnlineStoreCategoryController::class, 'destroy'])->name('categories.destroy');


});

Route::get('/get-product-details/{productId}', [UserOnlineStoreController::class, 'getProductDetails']);

Route::group(['prefix' => 'debtors', 'middleware' => ['auth', 'admin']], function () {
    
    Route::get('/index', [DebtorsController::class, 'index'])->name('debtors.index');
    Route::get('/customer-sales/{customerId}', [DebtorsController::class, 'getCustomerSales']);


});



Route::middleware(['auth', 'staff'])->group(function () {
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    
    Route::get('/expenses/deposits', [ExpenseDepositController::class, 'index'])->name('expenses.deposits');
    Route::post('/expenses/deposits', [ExpenseDepositController::class, 'store'])->name('expenses.deposits.store');
    
    Route::get('/expenses/records', [ExpenseRecordController::class, 'index'])->name('expenses.records');
    Route::post('/expenses/records', [ExpenseRecordController::class, 'store'])->name('expenses.records.store');
    
    Route::get('/expenses/reports', [ExpenseReportController::class, 'index'])->name('expenses.reports');

    Route::post('/expenses/generate-report', [ExpenseReportController::class, 'generateReport'])->name('expenses.generate_report');

    Route::get('/expenses/balance-cards', [ExpenseDepositController::class, 'getBalanceCards'])->name('expenses.balance_cards');
});


