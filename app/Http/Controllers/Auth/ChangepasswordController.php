<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangepasswordController extends Controller
{
    public function index(){
        return view('auth.changepassword');
    }

    public function change(Request $request){

        $this->validate($request, [
            'password' => 'required_with:confirm_password|min:3|same:confirm_password',

        ]);
        $form_data = array(
            'password' => Hash::make($request->password),
        );
        User::whereId(Auth::user()->id)->update($form_data);

        return redirect()->route('student.home');

    }
}
