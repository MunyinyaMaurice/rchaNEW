<?php

namespace App\Http\Controllers\paymentGatways;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use KingFlamez\Rave\Facades\Rave as Flutterwave;



class flutterController extends Controller
{
    public function initialize(Request $request)
    {
        try {
    //         $payInfoQuery = DB::table('users')
    // ->join('payments', 'users.id', '=', 'payments.user_id')
    // ->join('places', 'payments.place_id', '=', 'places.id')
    // ->join('tokens', 'payments.token_id', '=', 'tokens.id')
    // ->where('users.id', Auth::user()->id)
    // ->select('users.email', 'users.phone_number', 'users.first_name', 'users.last_name', 'places.place_name', 'places.place_location', 'payments.amount','tokens.paid_token');
    // $results = $payInfoQuery->get();
    // if($results){
    //     foreach ($results as $result) {
    //     echo 
    // }
        Auth::user()->id;
        $email=$request->get('email');
        
        $user_name = $request->get('first_name') .' '.$request->get('last_name');
        
        $phone_number = $request->get('phone_number');

        $place_name = $request->get('place_name');
         
        $place_location = $request->get('place_location'); 
       
        $amount = $request->get('amount');
       
        $paid_token = $request->get('paid_token');

            $reference = Flutterwave::generateReference();
    
            // Enter the details of the payment
            //$formatted_currency = format_money($booking->total);
            $data = [
                //'public_key' => 'FLWPUBK_TEST-e2e00ff6ae2bc3dc50655cb4a3fb29ac-X',
                'tx_ref' =>$reference,
                'user_name' =>$user_name,
                'email' => $email,
                'amount' =>$amount,
                'place_name' =>$place_name,
                'place_location' =>$place_location,
                'payment_options' => 'card,banktransfer',
                'currency' => "USD",
                //'currency' => $currency,
                'redirect_url' => route('callback'),
                'customer' => [
                   'email' => $email,            
                   'name' => $user_name,
                   'phone_number' => $phone_number
               ],
              
            ];
           // dd($data);
            $payment = Flutterwave::initializePayment($data);
            //dd($payment);
            if ($payment['status'] !== 'success') {
             
               return response()->json(['message' => 'Payment initiation failed'], 422);  
            }
            return ($payment['data']['link']);
            //  return redirect(['paymentLink' => $payment['data']['link'], Auth::user()->id]);
            
          } catch (\Exception $e) {
          dd($e);
            return response()->json(['message' => 'An error occurred during payment initiation'], 500);
        }
    }
    public function callback()
{   
try {
    
   $status = request()->input('status');
   if ($status == 'successful') {
       $transactionID = Flutterwave::getTransactionIDFromCallback();
       $data = Flutterwave::verifyTransaction($transactionID);
   return response()->json(['message' =>' Your Payment is Successfuly!'], 200);
   //return route('/getPaymentInfo');
   //  return redirect(route("user.booking_history"));
      } elseif ($status == 'cancelled') {
       return response()->json(['message' => 'Payment cancelled'], 404);
       //return redirect(route('page.detail'));
   } else {
      return response()->json(['message' => 'Payment failed'], 200);
   }
} catch (\Exception $e) {
      dd($e);
    return response()->json(['message' => 'An error occurred during payment callback'], 500);
}
}
}
