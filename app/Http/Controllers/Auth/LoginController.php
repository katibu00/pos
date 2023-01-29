<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{


    public function __construct()
    {
        auth()->logout();
        $this->middleware('guest')->except('logout');

    }



    public function index() {
        auth()->logout();
        return view('auth.login');
    }


    public function login(Request $request) {
       
        $validator = Validator::make($request->all(), [
            'email'=>'required|max:191',
            'password'=>'required|max:191'
        ]);
       
        if($validator->fails()){
            return response()->json([
                'status'=>400,
                'errors'=>$validator->messages(),
            ]);
        }

            // $login = request()->input('email');
            // $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'login_id';
            // request()->merge([$fieldType => $login]);


                if(!auth()->attempt($request->only('email', 'password'),$request->remember)){
                    return response()->json([
                        'status'=>401,
                        'message'=>'Invalid Login Details'
                    ]);
                }
                


                if(Auth::user()->usertype == 'admin'){
                    return response()->json([
                        'status'=>200,
                        'user'=>'admin',
                    ]);
                }else if (Auth::user()->usertype == 'cashier'){
                    return response()->json([
                        'status'=>200,
                        'user'=>'cashier',
                    ]);
                }else {
                    return back()->with('status','You are not authorized to access this content');
                }

                

    }
}
