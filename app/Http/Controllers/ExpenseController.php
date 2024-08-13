<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
   
    public function index()
    {
        return view('expense.index');
    }
    
}