<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

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

    public function changePasswordIndex()
    {
        return view('auth.change_password');
    }


    public function changePasswordStore(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'The current password is incorrect.');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Password changed successfully.');
    }
}
