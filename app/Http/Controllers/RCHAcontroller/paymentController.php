<?php

namespace App\Http\Controllers\RCHAcontroller;

use view;
use session;
use Carbon\Carbon;
use App\Models\Place;
use App\Models\Token;
use App\Models\Payment;
// use Cohensive\OEmbed\OEmbed;
use Cohensive\OEmbed\Embed;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
// use App\Exports\PaymentInfoExport;
use App\Exports\PaymentInfoExport;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Cohensive\OEmbed\Facades\OEmbed;
use Maatwebsite\Excel\Facades\Excel;
use Tymon\JWTAuth\Contracts\Providers\Auth;

class paymentController extends Controller
{
    public function generatePaidLink(Request $request)
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

            $token = new Token();
            $token->paid_token = $paidToken;
            $token->token_expires_at = $tokenExpiresAt;
            $token->paid_link = $place->place_link . '/' . $paidToken;
            $token->save();

            return response()->json([
                'message' => 'Paid link generated successfully!',
                'paidLink' => $token->paid_link,
                'paidToken' => $paidToken,
                'expires_in' => $tokenExpiresAt,
            ], 200);
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
            return view('videoView')->with('token', $token->paid_link);
        } catch (\Exception $e) {
            Log::error('Exception occurred: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while validating the paid token.',
            ], 500);
        }
    }

    public function payment(Request $request)
    {

        try {
            // $user = Auth::user();
            $user = JWTAuth::parseToken()->authenticate(); // Get the authenticated user using JWT
            $token = Token::find($request->get('token_id'));
            $place = Place::find($request->get('place_id'));
            $payment = new Payment();
            $payment->user_id = $user->id;
            $payment->place_id = $place->id;
            $payment->token_id = $token->id;
            $payment->amount = $request->get('amount');

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
          
            }catch (\Exception $e) {
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
            $sortBy = 'created_at'; // Replace with your desired sorting criteria
            $sortDirection = 'desc'; // Replace with your desired sorting direction
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
    

}
