<?php

namespace App\Http\Controllers;

use App\Models\Estimate;
use App\Models\Returns;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DataSyncController extends Controller
{
    public function index()
    {
        // Get the count of records for each model
        $salesCount = Sale::count();
        $estimatesCount = Estimate::count();
        $returnsCount = Returns::count();

        return view('data_synch.index', compact('salesCount', 'estimatesCount', 'returnsCount'));
    }



   


    public function sendData(Request $request)
    {
        // Fetch all records from each model
        $salesData = Sale::all();
        $estimatesData = Estimate::all();
        $returnsData = Returns::all();

        // Prepare the data to be sent in JSON format
        $data = [
            'sales' => $salesData,
            'estimates' => $estimatesData,
            'returns' => $returnsData,
        ];

        // Send the data to the web application using the HTTP Client.
        $response = Http::post('https://elhabibplumbing.com/api/receive-data', $data);

        // Check if the response status is 200 (OK) and the response content indicates success.
        if ($response->successful() && $response->json('status') === 'success') {
            // If the response is successful and the content indicates success,
            // delete the local data.
            Sale::truncate();
            Estimate::truncate();
            Returns::truncate();

            return response()->json(['message' => 'Data synchronization complete.']);

            // Log or notify about the successful synchronization.
            \Log::info('Data successfully synchronized to web application.');
        } else {
            // Handle the case when the response is not successful (e.g., log an error or retry later).
            \Log::error('Data synchronization to web application failed: ' . $response->status());
        }

        return response()->json(['message' => 'error failed.']);
    }





}
