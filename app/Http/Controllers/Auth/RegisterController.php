<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function index() {
        return view('auth.register');
    }

    public function store(Request $request) {
// dd($request->all());
        $this->validate($request, [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
            ]);
// dd($request->all());
                $user = new User();
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->usertype = 'customer';
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->save();
    
            return redirect()->route('login')->with('success','Registered Successfully. Please login below.');
            //  return back()->with('status','Invalid login details');
    }


}
