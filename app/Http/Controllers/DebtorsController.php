<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Pagination\LengthAwarePaginator;

class DebtorsController extends Controller
{

   

    public function index()
    {
        $branchId = auth()->user()->branch_id;

        $sales = Sale::where('branch_id', $branchId)
            ->whereNotNull('customer')
            ->where('payment_method', 'credit')
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', 'partial');
            })
            ->with('customerDetail')
            ->orderBy('receipt_no')
            ->get();

        $groupedSales = $sales->groupBy('receipt_no');

        $customers = [];
        foreach ($groupedSales as $receiptNo => $salesGroup) {
            $firstSale = $salesGroup->first();

            // Check if customerDetail exists
            if ($firstSale->customerDetail) {
                $customerId = $firstSale->customerDetail->id;

                if (!isset($customers[$customerId])) {
                    $customers[$customerId] = [
                        'customer_id' => $customerId,
                        'first_name' => $firstSale->customerDetail->first_name,
                        'phone' => $firstSale->customerDetail->phone,
                        'total_owed' => 0,
                        'total_paid' => 0,
                        'last_sales_date' => $firstSale->created_at,
                        'last_payment_date' => null,
                    ];
                }

                foreach ($salesGroup as $sale) {
                    $customers[$customerId]['total_owed'] += $sale->price * $sale->quantity - $sale->discount;
                }

                $customers[$customerId]['total_paid'] += $firstSale->payment_amount ?? 0;
                $customers[$customerId]['last_sales_date'] = max($customers[$customerId]['last_sales_date'], $firstSale->created_at);
            }
        }

        $payments = Payment::where('branch_id', $branchId)
            ->whereIn('customer_id', array_keys($customers))
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('customer_id');

        foreach ($payments as $customerId => $customerPayments) {
            if (isset($customers[$customerId])) {
                $customers[$customerId]['last_payment_date'] = $customerPayments->first()->created_at;
            }
        }

        foreach ($customers as &$customer) {
            if (is_null($customer['last_payment_date'])) {
                $customer['days_since_last_payment'] = now()->diffInDays($customer['last_sales_date']);
            } else {
                $customer['days_since_last_payment'] = now()->diffInDays($customer['last_payment_date']);
            }
        }

        usort($customers, function ($a, $b) {
            return $b['days_since_last_payment'] <=> $a['days_since_last_payment'];
        });

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $itemCollection = collect($customers);
        $perPage = 10;
        $currentPageItems = $itemCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedCustomers = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);

        $paginatedCustomers->setPath(route('debtors.index'));

        return view('debtors.index', ['customers' => $paginatedCustomers]);
    }

    public function getCustomerSales($customerId)
    {
        // Fetch customer's credit sales transactions grouped by receipt_no
        $sales = Sale::with('product')
            ->where('customer', $customerId)
            ->where('payment_method', 'credit')
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', 'partial');
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('receipt_no');

        // Return sales transactions as JSON
        return response()->json($sales);
    }

}
