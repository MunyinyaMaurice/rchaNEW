<?php

namespace App\Http\Controllers;

use Carbon\Factory;
use App\Models\User;
use App\Mail\welcomeMail;
use App\Models\verifyToken;

// use Illuminate\Support\Facades\Mail;
// use Illuminate\Auth\Events\Registered;
// use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Http\Request;
use App\Mail\SendVerificationEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Notifications\Messages\MailMessage;

class userAuthController extends Controller
{
    private $SendVerificationEmail;
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register','verify','verification.notice','verify-account','verifyotp']]);
    }
    
    public function register(Request $request)
    {
        try{
        $validator = Validator::make($request->All(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|string|email|unique:users',
            'phone_number' => 'required',
            'country' => 'required',
            'password' => 'required|string|confirmed|min:6',
            'city'=> 'required|string',
            // 'role'=>'user',

        ]);
        if ($validator->fails())
            return response()->json($validator->errors()->toJson(), 400);
        $user = new User();
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->phone_number = $request->input('phone_number');    
        $user->country = $request->input('country'); 
        $user->phone_number = $request->input('phone_number'); 
        $user->password = Hash::make($request->input('password'));
        $user->city = $request->input('city'); 
        $user->role='user';
        $user->save();
     
        $validToken = rand(10,100..'2023');
        $get_token = new verifyToken();
        $get_token -> token = $validToken;
        $get_user_email = $user->email;
        $get_user_name= $user->first_name;
        Mail::to($user->email)->send(new welcomeMail($get_user_email,$validToken,$get_user_name));
       
        event(new Registered($user));

        Auth::login($user);
        // $user->sendEmailVerificationNotification(); 
        // Send email verification email '  this is link verification'
        //   $verificationToken = hash_hmac('sha256', $user->email, config('app.key'));
        //   Mail::to($user->email)->send(new SendVerificationEmail($user, $verificationToken));

        return response()->json(
            [
                'message' => 'User is registered successful! and verification link has been sent to your email address',
                'user' => $user,
               // 'verificationToken'=> $verificationToken,
                //'id' => $user->id, 
            ],
            201);
    //}
}catch(\Exception $e){
    Log::error('Exception occurred: ' . $e->getMessage());
    return response()->json([
        'message' => 'An error occurred while registering user.',
    ], 501);

}
    }
    public function updateUser(Request $request)
{
    try {
        $user = Auth::user(); // Get the authenticated user
        
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|string|email|unique:users,email,'.$user->id,
            'phone_number' => 'required',
            'country' => 'required',
            'city' => 'required|string',
            'password' => 'string|confirmed|min:6|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        // Update user information
        $user->update([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'phone_number' => $request->input('phone_number'),
            'country' => $request->input('country'),
            'city' => $request->input('city'),
        ]);

        // Update password if provided
        if ($request->has('password')) {
            $user->password = Hash::make($request->input('password'));
            $user->save();
        }

        return response()->json([
            'message' => 'User information updated successfully!',
            'user' => $user,
        ], 200);
    } catch (\Exception $e) {
        Log::error('Exception occurred: ' . $e->getMessage());
        return response()->json([
            'message' => 'An error occurred while updating user information.',
        ], 500);
    }
}

    public function verifyUserEmail($id)
{

    $user = User::where('id', $id)->first();

    if (!$user) {
         // Handle the case where the user does not exist
         return ['message' => 'User not found'];
     }
 
     // 3. Check if the user is already verified
     if ($user->email_verified_at != null) {
         return ['message' => 'User is already verified'];
     }
 
     // 4. Update email_verified_at if the user is not verified
     $user->email_verified_at = now();
     $user->save();
 
     return ['message' => 'User email verified'];

}
    public function login(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Attempt to log in the user
        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    } catch (\Exception $e) {
        // Handle any errors that occur
        Log::error($e->getMessage());
        return response(['message'=> 'An error occurred while creating token for user user login.'], 501);
    }
}

public function createNewToken($token)
{
    try {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 4320,
            'user' => auth()->user()
        ],201);
        
    } catch (\Exception $e) {
        // Handle any errors that occur
        Log::error($e->getMessage());
        return response(['message'=> 'An error occurred while creating token for user user login in createNewToken.'], 500);
    }
}

    public function profile()
    {
        return response()->json([auth()->user()]);
    }
    public function getAllUser(Request  $request)
    {
        try {
            $perPage = $request->input('per_page', 50); // Default to 10 items per page
    
            $user = User::paginate($perPage);
    
            if($user->count() > 0) {
                return $user;
            }
    
            return response()->json(['message'=>'no records found'], 404);
        } catch(\Exception $e) {
            Log::error($e->getMessage());
            return response(['message'=> 'An error occurred while fetching All users.'], 501);
        }
    }
    public function logout()
    {
        auth()->logout();
        return response()->json(
            ['message' => 'User is loged out successful!']
        );
    }
}
