<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function loginIndex()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|max:191',
            'password' => 'required|max:191',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        }

        if (!auth()->attempt($request->only('email', 'password'), $request->remember)) {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid Login Details',
            ]);
        }

        if (Auth::user()->usertype == 'admin') {
            return response()->json([
                'status' => 200,
                'user' => 'admin',
            ]);
        } else if (Auth::user()->usertype == 'cashier') {
            return response()->json([
                'status' => 200,
                'user' => 'cashier',
            ]);
        } else {
            return back()->with('status', 'You are not authorized to access this content');
        }

    }

    public function logout(){
        auth()->logout();
        return redirect()->route('login');
    }
}
