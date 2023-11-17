<?php

namespace App\Http\Controllers\RCHAcontroller;

use view;
use session;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Place;
use App\Models\Token;
// use Cohensive\OEmbed\OEmbed;
use App\Models\Payment;
use Cohensive\OEmbed\Embed;
use Illuminate\Support\Str;
// use App\Exports\PaymentInfoExport;
use Illuminate\Http\Request;
use App\Exports\PaymentInfoExport;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\FreeToken;
use App\Models\PaidVideos;
use Cohensive\OEmbed\Facades\OEmbed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
// use App\Http\Controllers\userAuthController;

class paymentController extends Controller
{
  
    // public function generatePaidLink($place_id)
    // {
    //     try {
    //         $user = JWTAuth::parseToken()->authenticate(); // Get the authenticated user using JWT
    //         $place = Place::find($place_id);
    //         if (!$place) {
    //             return response()->json([
    //                 'message' => 'Place not found!',
    //             ], 404);
    //         }
    //         // Search for the last payment record made by the user for the specific place
    //         $lastPayment = Payment::where('user_id', $user->id)
    //         ->where('place_id', $place_id)
    //         ->latest()
    //         ->first();

             
    //         if ($lastPayment && !$lastPayment->token_id) {
             
             
    //         $paidToken = Str::random(32);
    //         // Set the token expiration time to 1 minutes from now
    //         // $tokenExpiresAt = Carbon::now()->addMinutes(20);
    //         $tokenExpiresAt = Carbon::now()->addHours(24);

    //         $token = new Token();
    //         $token->paid_token = $paidToken;
    //         $token->token_expires_at = $tokenExpiresAt;
    //         $token->paid_link = $place->place_link . '/' . $paidToken;
    //         $token->save();

    //         // If a payment record is found and token_id is null, update the token_id
    //         $lastPayment->token_id = $token->id;
    //         $lastPayment->save();

    //         //send paid token to user
    //         if (isset($paidToken)) {
    //             Mail::to($user->email)
    //                 ->send(new \App\Mail\sendVideoLink($user, $paidToken));


    //             //return redirect(url('/http://localhost:3000/dashboard/watchVideo/{{$paidToken}}'));

    //             // return 'Email sent successfully!';
    //         }
            
    //         return response()->json([
    //             'message' => 'Paid link generated successfully! sent to user email!',
    //             'paidLink' => $token->paid_link,
    //             'paidToken' => $paidToken,
    //             'expires_in' => $tokenExpiresAt,
    //             // 'token' => $token->id,

    //         ], 200);
    //     }
    //     } catch (\Exception $e) {
    //         Log::error('Exception occurred: ' . $e->getMessage());
    //         return response()->json([
    //             'message' => 'An error occurred while generating the paid link.',
    //         ], 500);
    //     }
    // }

    public function generatePaidLink($place_id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate(); // Get the authenticated user using JWT
            $place = Place::find($place_id);
            if (!$place) {
                return response()->json([
                    'message' => 'Place not found!',
                ], 404);
            }
            // Search for the last payment record made by the user for the specific place
            $lastPayment = Payment::where('user_id', $user->id)
            ->where('place_id', $place_id)
            ->latest()
            ->first();

             
            if ($lastPayment && !$lastPayment->token_id) {
             
             
            $paidToken = Str::random(32);
            // Set the token expiration time to 1 minutes from now
            // $tokenExpiresAt = Carbon::now()->addMinutes(20);
            $tokenExpiresAt = Carbon::now()->addHours(24);

            //Get PAid Videos
            $paidVideos = PaidVideos::where('place_id', $place_id)->first();
                if(!$paidVideos){
                    return response()->json(['message' =>'Paid videos are not found'] ,404);
                }
            $token = new Token();
            $token->paid_token = $paidToken;
            $token->token_expires_at = $tokenExpiresAt;
            $token->long_version_self_guided = $paidVideos->long_version_self_guided . '/' . $paidToken;
            $token->long_eng_version_360_video = $paidVideos->long_eng_version_360_video . '/' . $paidToken;
            $token->long_french_version_360_video = $paidVideos->long_french_version_360_video . '/' . $paidToken;
            $token->long_kiny_version_360_video = $paidVideos->long_kiny_version_360_video . '/' . $paidToken;
            $token->save();

            // If a payment record is found and token_id is null, update the token_id
            $lastPayment->token_id = $token->id;
            $lastPayment->save();

            //send paid token to user
            if (isset($paidToken)) {
                Mail::to($user->email)
                    ->send(new \App\Mail\sendVideoLink($user, $paidToken));


                //return redirect(url('/http://localhost:3000/dashboard/watchVideo/{{$paidToken}}'));

                // return 'Email sent successfully!';
            }
            
            return response()->json([
                'message' => 'Paid link generated successfully! sent to user email!',
                'long_version_self_guided' => $token->long_version_self_guided,
                'long_eng_version_360_video' => $token->long_eng_version_360_video,
                'long_french_version_360_video' => $token->long_french_version_360_video,
                'long_kiny_version_360_video' => $token->long_kiny_version_360_video,
                'paidToken' => $paidToken,
                'expires_in' => $tokenExpiresAt,
                // 'token' => $token->id,

            ], 200);
        }
        } catch (\Exception $e) {
            Log::error('Exception occurred: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while generating the paid link.',
            ], 500);
        }
    }


    public function validatePaidToken($paidToken)
    {
        try {
            // Get the token from the database
            $token = Token::where('paid_token', $paidToken)->first();

            // If the token is not found, return an error response
            if (!$token) {
                return response()->json(['message' => 'Invalid token'], 422);
            }

            // Get the current time
            $now = Carbon::now();

            // Get the token expiration time
            $tokenExpiresAt = Carbon::parse($token->token_expires_at);

            // Check if the token is expired
            if ($now->isAfter($tokenExpiresAt)) {
                return response()->json(['message' => 'Token has expired'], 422);
            }
            //return response()->json(['token' =>$token->paid_link], 201);
            // return view('videoView')->with('token', $token->paid_link);
            // return redirect('https://inteko.netlify.app/dashboard/watchVideo/' . $token->paid_link);

            //Construct the URL for redirect
            $redirectUrl = $token->paid_link;
            // Redirect away to the stored external URL
            return redirect()->away($redirectUrl);
        } catch (\Exception $e) {
            Log::error('Exception occurred: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while validating the paid token.',
            ], 500);
        }
    }
    //this function will generate free token in admin and this token will be sent to orgisan email
    public function generateFreeLink(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate(); // Get the authenticated user using JWT
            $place = Place::find($request->get('place_id'));
            if (!$place) {
                return response()->json([
                    'message' => 'Place not found!',
                ], 404);
            }
    
            $paidToken = Str::random(32);
            // Set the token expiration time to 1 minutes from now
            // $tokenExpiresAt = Carbon::now()->addMinutes(20);
            $tokenExpiresAt = Carbon::now()->addHours(24);
    
            $freetoken = new FreeToken();
            $freetoken->paid_token = $paidToken;
            $freetoken->token_expires_at = $tokenExpiresAt;
            $freetoken->paid_link = $place->place_link . '/' . $paidToken;
            $freetoken->save();
    
            return response()->json([
                'message' => 'Paid link generated successfully!',
                'freeLink' => $freetoken->paid_link,
                'freeToken' => $paidToken,
                'expires_in' => $tokenExpiresAt,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception occurred: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while generating the paid link.',
            ], 500);
        }
    }
    

    public function payment($place_id,$user_id)
    {
        // dd($user_id,$place_id);
        try {
            
            $place = Place::find($place_id);
            $payment = new Payment();
            // $payment->user_id = $user->id;
            $payment->user_id = $user_id;
            $payment->place_id = $place_id;
            // $payment->token_id = $token->id;
            $payment->token_id = null;
            $payment->amount = $place->amount;

            if ($payment->save()) {
                Log::info('Payment created');
                return response()->json([
                    'message' => 'Payment created successfully!',
                    'payment' => $payment,
                ], 201);
            }
            Log::info('Payment not created');
            return response()->json([
                'message' => 'Payment info is not saved!',
            ], 422);
        } catch (\Exception $e) {
            // Handle the exception
            Log::error('Exception occurred: ' . $e->getMessage());
            //dd($e);
            return response()->json([
                'message' => 'An error occurred while processing your request.',
            ], 500);
        }
    }

    public function getPaymentInfo($sortBy, $sortDirection, $perPage)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            // dd($user);
            // Check if the authenticated user has an admin role
            if ($user->role === 'admin') {
                $payInfoQuery = DB::table('users')
                    ->join('payments', 'users.id', '=', 'payments.user_id')
                    ->join('places', 'payments.place_id', '=', 'places.id')
                    ->join('tokens', 'payments.token_id', '=', 'tokens.id');
            } else {
                $payInfoQuery = DB::table('users')
                    ->join('payments', 'users.id', '=', 'payments.user_id')
                    ->join('places', 'payments.place_id', '=', 'places.id')
                    ->join('tokens', 'payments.token_id', '=', 'tokens.id')
                    ->where('users.id', $user->id);
            }

            // Add sorting logic
            $payInfoQuery->orderBy($sortBy, $sortDirection);
            //returning the object
            $results = $payInfoQuery
                ->select(
                    'users.email',
                    'users.phone_number',
                    'users.first_name',
                    'users.last_name',
                    'places.place_name',
                    'places.place_location',
                    'payments.amount',
                    'payments.created_at',
                    'tokens.paid_token'
                    //    )->get();
                )->paginate($perPage);

            // }
            // return $results; // Count the number of records
            // $count = $results->count();

            return response()->json([
                // 'count' => $count,
                'results' => $results
            ], 200);
            // return [
            //     'results' => $results->items(), // Get the paginated items
            // ];
            return Excel::download(new PaymentInfoExport($data['results']), 'payment_info.xlsx');
        } catch (\Exception $e) {
            Log::error('Exception occurred' . $e->getMessage());
            return response()->json(['message' => 'Something happed while gettingPayment info']);
        }
    }
    public function showPaymentInfo(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            // $sortBy = $request->query('sortBy', 'created_at'); // Default to sorting by created_at
            $sortBy = $request->query('sortBy', 'first_name');
            $sortDirection = $request->query('sortDirection', 'desc'); // Default to descending order

            // Pass sorted data in payment info
            $perPage = $request->query('perPage', 20); // Default to 10 records per page

            $paymentInfo = $this->getPaymentInfo($sortBy, $sortDirection, $perPage);

            // return response()->json([
            //     'paymentInfo' => $paymentInfo,
            // ], 200);
            return $paymentInfo;
            // $paymentInfo = $this->getPaymentInfo($sortBy, $sortDirection);

            //     $response = $paymentInfo->getData();

            //   //  $count = $response->count; // Access the count property

            //     return response()->json([
            //        // 'count' => $count,
            //         'paymentInfo' => $response->results
            //     ], 200);
            // return $paymentInfo;
        } catch (\Exception $e) {
            Log::error('Exception occurred' . $e->getMessage());
            return response()->json(['message' => 'something happend while showPaymentInfo']);
        }
    }
    public function calculateTotalAmountPaid()
    {
        try {
            $totalAmountPaid = Payment::sum('amount');

            return $totalAmountPaid;
        } catch (\Exception $e) {
            Log::error('Exception occurred' . $e->getMessage());
            return response()->json(['message' => 'something happend while calculateTotalAmountPaid']);
        }
    }
    public function exportPaymentInfo()
    {
        try {
            $sortBy = 'created_at';
            $sortDirection = 'desc';
            $perPage = 20; // Replace with your desired number of records per page

            $response = $this->getPaymentInfo($sortBy, $sortDirection, $perPage);

            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getContent(), true);

                return Excel::download(new PaymentInfoExport($data['results']), 'payment_info.xlsx');
            }

            return response()->json(['message' => 'Error exporting data to Excel'], $response->getStatusCode());
        } catch (\Exception $e) {
            Log::error('Exception occurred: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while exporting data to Excel'], 500);
        }
    }

    public function infoBeforePayment($place_id)
    {

        try {
            $user = JWTAuth::parseToken()->authenticate(); // Get the authenticated user using JWT

            $place = Place::where('id', $place_id)->first();
            if (!$place) {
                return response()->json(['message' => 'place not found'], 404);
            }
            return response()->json([
                'user' => $user,
                'place' => $place,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Exception occured:' . $e->getMessage());
            return response()->json(['message' => 'something happened while getting infoBeforePayment.'], 501);
        }
    }
}


  //Validate the paid token
            // $validatePaidToken = app('App\Http\Controllers\RCHAcontroller\paymentController')->validatePaidToken($paidToken);

            // Log::info('validatePaidToken: ' . $validatePaidToken);
            // if ($validatePaidToken) {
            //     // return redirect(url('https://rcha.innorios.com/api/auth/videoView/{{$paidToken}}'));
            //     // Construct the redirect URL
            //     //$redirectUrl = url('/api/auth/videoView/' . $paidToken);

            //     // Construct the URL for redirect
            //     $redirectUrl = "https://inteko.netlify.app/dashboard/videoView/{$paidToken}";


            //     // Redirect to the constructed URL
            //     return redirect($redirectUrl);
            // }
