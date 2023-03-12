<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function store(Request $request)
    {

        // Validate API key
        $key = sha1('K92@218$%_712bn');
        if ($_GET['api_key'] !== $key) {
            http_response_code(401);
            exit('Invalid API key');
        }

// Retrieve data from database
        $regno = $_GET['regno'];
// TODO: Query database or other data source to retrieve data for the given registration number
        $data = array(
            'name' => 'John Doe',
            'level' => '200',
            'department' => 'Computer Science',
            // TODO: Add other data fields as needed
        );

// Encode data as JSON and return response
        header('Content-Type: application/json');
        return json_encode($data);

        return 123;
    }
}
