<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Auth, Validator, DB, Mail;

class AuthController extends Controller{
    public function login(Request $request){
        return view('auth.login');
    }

    public function signin(Request $request){
        $validator = Validator::make(
                                    ['email' => $request->email, 'password' => $request->password],
                                    ['email' => 'required', 'password' => 'required']
                                );

        if($validator->fails()){
            return redirect()->route('login')->withErrors($validator)->withInput();
        }else{
            $auth = auth()->attempt(['email' => $request->email, 'password' => $request->password]);

            if($auth != false){
                $user = User::where(['email' => $request->email])->orderBy('id', 'desc')->first();

                if($user->status == 'inactive'){
                    Auth::logout();
                    return redirect()->route('login')->with('error', 'Account belongs to this credentials is inactive, please contact administrator');
                }elseif($user->status == 'deleted'){
                    Auth::logout();
                    return redirect()->route('login')->with('error', 'Account belongs to this credentials is deleted, please contact administrator');
                }else{
                    return redirect()->route('dashboard')->with('success', 'Login successfully');
                }
            }else{
                return redirect()->route('login')->with('error', 'invalid credentials, please check credentials');
            }
        }
    }

    public function logout(Request $request){
        Auth::logout();
        return redirect()->route('login');
    }
}
