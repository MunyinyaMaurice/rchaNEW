<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\verifyToken;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // return view('home');
        $get_user= User::where('email',auth()->user()->email)->first();
        if($get_user->is_activated== 1){
            return view('home');
        }else 
        return redirect('/verify-account');
    }

    public function verifyAccount(){
        return view('otp_verification');
    }
    public function userActivation(Request $request){
        $get_token =  $request->token();
        $get_token = verifyToken::where('token', $get_token)->first();
        if($get_token){
            $get_token->is_activated =1;
            $get_token->save();
            $user = User::where('email', $get_token->user->email)->first();
            $user->is_activated = 1;
            $user->save();
            $getting_token= VerifyToken::where('token', $get_token->token)->first();
            $getting_token->delete();
            return redirect('/home')->with('activated','Your account has been activated successfuly');
        
        }else{
            return redirect('/verify-account')->with('incorrect','Your OTP is invalid');
        
        }
    }
}
