<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Estimate;
use App\Models\Purchase;
use App\Models\Returns;
use App\Models\Sale;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $data['branches'] = Branch::all();
        return view('reports.index',$data);
    }

    public function generate(Request $request)
    {
        // dd($request->all());
        if($request->report == 'gross' && $request->date == 'today'){
            $data['sales'] = Sale::select('receipt_no')->where('branch_id',$request->branch_id)->whereDate('created_at', Carbon::today())->groupBy('receipt_no')->latest()->get();
        }
        if($request->report == 'gross' && $request->date == 'week'){
            $data['sales'] = Sale::select('receipt_no')->where('branch_id',$request->branch_id)->whereDate('created_at',  [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->groupBy('receipt_no')->latest()->get();
        }
        if($request->report == 'gross' && $request->date == 'month'){
            $data['sales'] = Sale::select('receipt_no')->where('branch_id',$request->branch_id)->whereMonth('created_at',  Carbon::now()->month)->groupBy('receipt_no')->latest()->get();
        }
        if($request->report == 'gross' && $request->date == 'range'){
            $data['sales'] = Sale::select('receipt_no')->where('branch_id',$request->branch_id)->whereBetween('created_at',  [$request->start, $request->end])->groupBy('receipt_no')->latest()->get();
        }

        if($request->report == 'inventory' && $request->date == 'today'){
            $data['inventories'] = Sale::select('stock_id')->where('branch_id',$request->branch_id)->whereDate('created_at', Carbon::today())->groupBy('stock_id')->get();
            $data['frame'] = $request->date;
        }
        if($request->report == 'inventory' && $request->date == 'week'){
            $data['inventories'] = Sale::select('stock_id')->where('branch_id',$request->branch_id)->whereDate('created_at',  [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->groupBy('stock_id')->get();
            $data['frame'] = $request->date;
        }
        if($request->report == 'inventory' && $request->date == 'month'){
            $data['inventories'] = Sale::select('stock_id')->where('branch_id',$request->branch_id)->whereMonth('created_at',  Carbon::now()->month)->groupBy('stock_id')->get();
            $data['frame'] = $request->date;
        }
        if($request->report == 'inventory' && $request->date == 'range'){
            $data['inventories'] = Sale::select('stock_id')->where('branch_id',$request->branch_id)->whereBetween('created_at',  [$request->start, $request->end])->groupBy('stock_id')->get();
            $data['frame'] = $request->date;
            $data['start'] = $request->start;
            $data['end'] = $request->end;
        }


        if($request->report == 'returns' && $request->date == 'today'){
            $data['returns'] = Returns::where('branch_id',$request->branch_id)->whereDate('created_at', Carbon::today())->get();
            $data['frame'] = $request->date;
        }

        if($request->report == 'returns' && $request->date == 'week'){
            $data['returns'] = Returns::where('branch_id',$request->branch_id)->whereDate('created_at',  [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();
            $data['frame'] = $request->date;
        }
        if($request->report == 'returns' && $request->date == 'month'){
            $data['returns'] = Returns::where('branch_id',$request->branch_id)->whereMonth('created_at',  Carbon::now()->month)->get();
            $data['frame'] = $request->date;
        }

        if($request->report == 'returns' && $request->date == 'range'){
            $data['returns'] = Returns::where('branch_id',$request->branch_id)->whereBetween('created_at',  [$request->start, $request->end])->get();
            $data['frame'] = $request->date;
            $data['start'] = $request->start;
            $data['end'] = $request->end;
        }

        if($request->report == 'today') {
          
            $todays = Sale::where('branch_id', $request->branch_id)->whereDate('created_at', Carbon::today())->get();

            $todays_total = 0;
            $todays_discount = 0;
            foreach($todays as $today)
            {
                $sum1 = $today['product']['selling_price']*$today->quantity ;
                $todays_total += $sum1;
                $todays_discount += $today->discount;
            }
            $data['gross'] = $todays_total;
            $data['discount'] = $todays_discount;
            $data['sales_count'] = Sale::select('receipt_no')->where('branch_id',$request->branch_id)->whereDate('created_at', Carbon::today())->groupBy('receipt_no')->get()->count();
            $data['items_sold'] = Sale::select('receipt_no')->where('branch_id',$request->branch_id)->whereDate('created_at', Carbon::today())->get()->count();
            $purchases = Purchase::select('stock_id','quantity')->where('branch_id', $request->branch_id)->whereDate('created_at', Carbon::today())->get();

            $todays_purchases = 0;
            foreach($purchases as $purchase)
            {
                $todays_purchases += $purchase['product']['buying_price']*$purchase->quantity ;
            }
            $data['todays_purchases'] = $todays_purchases;
            
            //estimate
            $estimates = Estimate::where('branch_id', $request->branch_id)->whereDate('created_at', Carbon::today())->get();

            $todays_estimate = 0;
            foreach($estimates as $estimate)
            {
                $todays_estimate += $estimate['product']['selling_price']*$estimate->quantity - $estimate->discount;
                
            }
            $data['todays_estimate'] = $todays_estimate;
            $data['estimate_count'] = Estimate::select('estimate_no')->where('branch_id',$request->branch_id)->whereDate('created_at', Carbon::today())->groupBy('estimate_no')->get()->count();

            //return
            $returns = Returns::where('branch_id', $request->branch_id)->whereDate('created_at', Carbon::today())->get();

            $todays_returns = 0;
            foreach($returns as $return)
            {
                $todays_returns += $return->price*$return->quantity;
                
            }
            $data['todays_returns'] = $todays_returns;
            $data['returns_count'] = Returns::select('return_no')->where('branch_id',$request->branch_id)->whereDate('created_at', Carbon::today())->groupBy('return_no')->get()->count();

            $lows = 0;
            $total_stock = 0;
            $stocks = Stock::where('branch_id',$request->branch_id)->get();
            foreach($stocks as $stock){
    
                if($stock->quantity <= $stock->critical_level){
                    $lows ++;
                }
                $total_stock++;
            }
            $data['lows'] = $lows;
            $data['total_stock'] = $total_stock;

        }

        $data['branches'] = Branch::all();
        $data['report'] = $request->report;
        $data['date'] = $request->date;
        $data['branch_id'] = $request->branch_id;
        return view('reports.index', $data);

    }
}
