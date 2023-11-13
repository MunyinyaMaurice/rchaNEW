<?php

namespace App\Http\Controllers\paymentGatways;



use App\Models\User;
use App\Models\Place;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use KingFlamez\Rave\Facades\Rave as Flutterwave;
// use App\Http\Controllers\RCHAcontroller\paymentController;



class flutterController extends Controller
{
    // public PaymentController $paymentController;

    // public

    // function __construct(paymentController $paymentController)
    // {
    //     $this->paymentController = $paymentController;
    // }
    public function initialize(Request $request)
    {
        try {
            
            $user = Auth::user();
            $user_id = $user->id;
            // $email = $request->get('email');
            $email = $user->email;
            // $user_name = $request->get('first_name') . ' ' . $request->get('last_name');
            $user_name = $user->first_name . ' ' . $user->last_name;
            // $phone_number = $request->get('phone_number');
            $phone_number = $user->phone_number;
            $place_name = $request->get('place_name');
            $place_id = $request->get('place_id');
            $place_location = $request->get('place_location');

            $amount = $request->get('amount');

            $paid_token = $request->get('paid_token');

            $reference = Flutterwave::generateReference();

            // Enter the details of the payment
            //$formatted_currency = format_money($booking->total);
            $data = [
                //'public_key' => 'FLWPUBK_TEST-e2e00ff6ae2bc3dc50655cb4a3fb29ac-X',
                'tx_ref' => $reference,
                'user_name' => $user_name,
                'email' => $email,
                'amount' => $amount,
                'place_name' => $place_name,
                'place_id' => $place_id,
                'place_location' => $place_location,
                'payment_options' => 'card,banktransfer',
                'currency' => "RWF",
                //'currency' => $currency,
                // 'redirect_url' => route('callback'),
                'redirect_url' => route('callback', ['place_id' => $place_id, 'user_id'=> $user_id]),
                
                'customer' => [
                    'email' => $email,
                    'name' => $user_name,
                    'phone_number' => $phone_number
                ],

            ];
            //dd($data);
            $payment = Flutterwave::initializePayment($data);
            // dd($payment);
            if ($payment['status'] !== 'success') {

                return response()->json(['message' => 'Payment initiation failed'], 422);
            }
            return ($payment['data']['link']);
           

        } catch (\Exception $e) {
            Log::error('Exception occured:' . $e->getMessage());
            return response()->json(['message' => 'An error occurred during payment initiation'], 500);
        }
    }
   
    // public function callback()
    public function callback($place_id,$user_id)
    {

        try {
           // $user_id = auth()->user()->id;
           // $token = request()->header('Authorization');
// dd($user_id,$place_id);
            $status = request()->input('status');
            if ($status == 'successful') {
                $transactionID = Flutterwave::getTransactionIDFromCallback();
                $data = Flutterwave::verifyTransaction($transactionID);
                //return response()->json(['place_id' =>$place_id], 201);
                // $payment = app('App\Http\Controllers\RCHAcontroller\paymentController')->payment($place_id);
                $payment = app('App\Http\Controllers\RCHAcontroller\paymentController')->payment($place_id, $user_id);

                Log::info('payment :' . $payment);
               
                // Construct the redirect URL
            
            $redirectUrl = "https://inteko.netlify.app/dashboard/watchVideo/{$place_id}";

           
            return redirect($redirectUrl);
                
            } elseif ($status == 'cancelled') {
                return response()->json(['message' => 'Payment cancelled'], 404);
                //return redirect(route('page.detail'));
            } else {
                return response()->json(['message' => 'Payment failed'], 200);
            }
        } catch (\Exception $e) {
            Log::error('Exception occured:' . $e->getMessage());
            return response()->json(['message' => 'An error occurred during payment callback'], 500);
        }
    }
}



// use App\Models\User;
// use App\Models\Place;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;
// use App\Http\Controllers\Controller;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Mail;
// use KingFlamez\Rave\Facades\Rave as Flutterwave;
// use App\Http\Controllers\RCHAcontroller\paymentController;



// class flutterController extends Controller
// {
//     public PaymentController $paymentController;

//     public

//     function __construct(paymentController $paymentController)
//     {
//         $this->paymentController = $paymentController;
//     }
//     public function initialize(Request $request)
//     {
//         try {
//             //         $payInfoQuery = DB::table('users')
//             // ->join('payments', 'users.id', '=', 'payments.user_id')
//             // ->join('places', 'payments.place_id', '=', 'places.id')
//             // ->join('tokens', 'payments.token_id', '=', 'tokens.id')
//             // ->where('users.id', Auth::user()->id)
//             // ->select('users.email', 'users.phone_number', 'users.first_name', 'users.last_name', 'places.place_name', 'places.place_location', 'payments.amount','tokens.paid_token');
//             // $results = $payInfoQuery->get();
//             // if($results){
//             //     foreach ($results as $result) {
//             //     echo 
//             // }
//             $user = Auth::user();
//             // $user->id;
//             // $email = $request->get('email');
//             $email = $user->email;
//             // $user_name = $request->get('first_name') . ' ' . $request->get('last_name');
//             $user_name = $user->first_name . ' ' . $user->first_name;
//             // $phone_number = $request->get('phone_number');
//             $phone_number = $user->phone_number;
//             $place_name = $request->get('place_name');
//             $place_id = $request->get('place_id');
//             $place_location = $request->get('place_location');

//             $amount = $request->get('amount');

//             $paid_token = $request->get('paid_token');

//             $reference = Flutterwave::generateReference();

//             // Enter the details of the payment
//             //$formatted_currency = format_money($booking->total);
//             $data = [
//                 //'public_key' => 'FLWPUBK_TEST-e2e00ff6ae2bc3dc50655cb4a3fb29ac-X',
//                 'tx_ref' => $reference,
//                 'user_name' => $user_name,
//                 'email' => $email,
//                 'amount' => $amount,
//                 'place_name' => $place_name,
//                 'place_id' => $place_id,
//                 'place_location' => $place_location,
//                 'payment_options' => 'card,banktransfer',
//                 'currency' => "RWF",
//                 //'currency' => $currency,
//                 // 'redirect_url' => route('callback'),
//                 'redirect_url' => route('callback', ['place_id' => $place_id]),
//                 'customer' => [
//                     'email' => $email,
//                     'name' => $user_name,
//                     'phone_number' => $phone_number
//                 ],

//             ];
//             //dd($data);
//             $payment = Flutterwave::initializePayment($data);
//             // dd($payment);
//             if ($payment['status'] !== 'success') {

//                 return response()->json(['message' => 'Payment initiation failed'], 422);
//             }
//             // Extract email-related information
//             $emailData = $this->extractEmailData($request);

//             return [
//                 'paymentLink' => $payment['data']['link'],
//                 'emailData' => $emailData,
//             ];
//             //return ($payment['data']['link']);
//             //return redirect(['paymentLink' => $payment['data']['link'], Auth::user()->id]);

//         } catch (\Exception $e) {
//             Log::error('Exception occured:' . $e->getMessage());
//             return response()->json(['message' => 'An error occurred during payment initiation'], 500);
//         }
//     }
//     // Extract email-related information
//     private function extractEmailData(Request $request)
//     {
//         $user = Auth::user();

//         return [
//             'user' => $user,
//             'place_id' => $request->get('place_id'),
//             'place_name' => $request->get('place_name'),
            
//             'place_location' => $request->get('place_location'),
//             'amount' => $request->get('amount'),
//         ];
//     }

//     // public function callback()
//     public function callback($place_id)
//     {

//         try {

//             $status = request()->input('status');
//             if ($status == 'successful') {
//                 $transactionID = Flutterwave::getTransactionIDFromCallback();
//                 $data = Flutterwave::verifyTransaction($transactionID);

//                 $generatePaidLinkFunction = app('App\Http\Controllers\RCHAcontroller\paymentController')->generatePaidLink($place_id);

//                 Log::info('generatePaidLinkFunction: ' . $generatePaidLinkFunction);

//                 if ($generatePaidLinkFunction) {
//                     $paidToken = $generatePaidLinkFunction->getData()->paidToken;

//                     if ($paidToken) {

//                         // Check if the user is authenticated
//                         // if (Auth::check()) {
//                         //     // Get the authenticated user from the session
//                         //     $user = Auth::user();
//                         //     // Send email with paid token
//                         //     $this->sendEmailWithPaidToken($user, request(), $paidToken);
//                         // } else {
//                         //     // Handle the case where the user is not authenticated
//                         //     return response()->json([
//                         //         'error' => 'User not authenticated.',
//                         //     ], 401);
//                         // }
                      //  // Validate the paid token
                        // $validatePaidToken = app('App\Http\Controllers\RCHAcontroller\paymentController')->validatePaidToken($paidToken);

                        // Log::info('validatePaidToken: ' . $validatePaidToken);
//                         return response()->json([
//                             'paidToken' => $paidToken,
//                             'place_id' => $place_id,
//                         ], 500);
//                     }
//                 } else {
//                     // Handle the case where $generatePaidLinkFunction is null
//                     return response()->json([
//                         'error' => 'Failed to generate paid Token.',
//                     ], 500);
//                 }
//             } elseif ($status == 'cancelled') {
//                 return response()->json(['message' => 'Payment cancelled'], 404);
//                 //return redirect(route('page.detail'));
//             } else {
//                 return response()->json(['message' => 'Payment failed'], 200);
//             }
//         } catch (\Exception $e) {
//             Log::error('Exception occured:' . $e->getMessage());
//             return response()->json(['message' => 'An error occurred during payment callback'], 500);
//         }
//     }
//     private function sendEmailWithPaidToken(User $user, Request $request)
//     {
//         try {
//             $emailData = $this->extractEmailData($request);

//             Mail::to($user->email)
//                 ->send(new \App\Mail\sendVideoLink(
//                     $user,
//                     $emailData['place_id'],
//                     $emailData['place_name'],
//                     $emailData['place_location'],
//                     $emailData['amount'],
//                     $emailData['paidToken']
//                 ));
//         } catch (\Exception $e) {
//             Log::error('Exception occured:' . $e->getMessage());
//             return response()->json(['message' => 'An error occurred sendEmailWithPaidToken'], 501);
//         }
//     }
// }
