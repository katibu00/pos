<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale; // Assuming Sale, Estimate, and Return are your model classes
use App\Models\Estimate;
use App\Models\Returns;

class DataReceiveController extends Controller
{
   
    public function store(Request $request)
    {
        try {
            // Assuming the data sent in the request is in JSON format
            $data = $request->json()->all();

            // Assuming the data structure is the same as sent from your local application
            $salesData = $data['sales'];
            $estimatesData = $data['estimates'];
            $returnsData = $data['returns'];

            // Save the data to your database
            foreach ($salesData as $saleData) {
                Sale::create($saleData);
            }

            foreach ($estimatesData as $estimateData) {
                Estimate::create($estimateData);
            }

            foreach ($returnsData as $returnData) {
                Returns::create($returnData);
            }

            // Return a success response
            return response()->json(['status' => 'success', 'message' => 'Data received and saved successfully.']);
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            \Log::error('Data receive error: ' . $e->getMessage());

            // Return an error response
            return response()->json(['status' => 'error', 'message' => 'An error occurred while processing the data.']);
        }
    }

}
