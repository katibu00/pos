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
            'identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('identifier', 'password');
        $remember = $request->has('remember');

        $fieldType = filter_var($credentials['identifier'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $credentials[$fieldType] = $credentials['identifier'];
        unset($credentials['identifier']);

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            $redirectRoute = $this->getRedirectRouteForUserType($user->usertype);
            
            return response()->json([
                'success' => true,
                'redirect' => route($redirectRoute)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'The provided credentials do not match our records.'
        ], 401);
    }


    private function getRedirectRouteForUserType($userType)
    {
        switch ($userType) {
            case 'admin':
                return 'admin.home';
            case 'cashier':
                return 'cashier.home';
            default:
                return 'home';
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
