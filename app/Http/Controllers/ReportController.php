<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Returns;
use App\Models\Sale;
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
        $data['branches'] = Branch::all();
        $data['report'] = $request->report;
        $data['date'] = $request->date;
        $data['branch_id'] = $request->branch_id;
        return view('reports.index', $data);

    }
}
