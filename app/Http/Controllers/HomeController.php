<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function admin(){
        $todays = Sale::whereDate('created_at', Carbon::today())->get();

        $todays_total = 0;
        foreach($todays as $today)
        {
            $sum1 = $today['product']['selling_price']*$today->quantity;
            $todays_total += $sum1;
        }

        $weeks = Sale::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();


        $weeks_total = 0;
        foreach($weeks as $week)
        {
            $sum2 = $week['product']['selling_price']*$week->quantity;
            $weeks_total += $sum2;
        }

        $data['todays_total'] = $todays_total;
        $data['weeks_total'] = $weeks_total;

        $lows = [];
        $stocks = Stock::all();
        foreach($stocks as $stock){

            if($stock->quantity <= $stock->critical_level){
                array_push($lows, $stock);
            }
        }
        $data['lows'] = $lows;
        return view('admin',$data);
    }

    public function cashier(){

        $todays = Sale::where('branch_id',Auth::user()->branch_id)->whereDate('created_at', Carbon::today())->get();

        $todays_total = 0;
        foreach($todays as $today)
        {
            $sum1 = $today['product']['selling_price']*$today->quantity;
            $todays_total += $sum1;
        }
        $data['todays_total'] = $todays_total;
        return view('cashier',$data);
    }
}
