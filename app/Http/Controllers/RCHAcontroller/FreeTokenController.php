<?php

namespace App\Http\Controllers\RCHAcontroller;

use Carbon\Carbon;
use App\Models\Place;
use App\Models\FreeToken;
use App\Models\PaidVideos;
use App\Mail\sendFreeToken;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class FreeTokenController extends Controller
{
    //this function will generate free token in admin and this token will be sent to orgisan email
    public function generateFreeLink(Request $request, $place_id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate(); // Get the authenticated user using JWT
            $place = Place::where('id', $place_id)->first();
           // dd( $place);
            if (!$place) {
                return response()->json([
                    'message' => 'Place not found!',
                ], 404);
            }
            //Get PAid Videos
            $paidVideos = PaidVideos::where('place_id', $place_id)->first();
            if (!$paidVideos) {
                return response()->json(['message' => 'Paid videos are not found'], 404);
            }
            $FreeToken = Str::random(32);
            // Set the token expiration time to 1 minutes from now
            // $tokenExpiresAt = Carbon::now()->addMinutes(20);
            $tokenExpiresAt = Carbon::now()->addHours(24);

            $freetoken = new FreeToken();
            $freetoken->organisation_name = $request->input('organisation_name');
            $freetoken->organisation_email = $request->input('organisation_email');
            $freetoken->freeToken = $FreeToken;
            $freetoken->token_expires_at = $tokenExpiresAt;
            //  $freetoken->paid_link = $place->place_link . '/' . $paidToken;
            // $freetoken->long_version_self_guided = $paidVideos->long_version_self_guided . '/' . $paidToken;
            // $freetoken->long_eng_version_360_video = $paidVideos->long_eng_version_360_video . '/' . $paidToken;
            // $freetoken->long_french_version_360_video = $paidVideos->long_french_version_360_video . '/' . $paidToken;
            // $freetoken->long_kiny_version_360_video = $paidVideos->long_kiny_version_360_video . '/' . $paidToken;
            $freetoken->save();

            //  try {
                Mail::to($freetoken->organisation_email)
                    ->send(new sendFreeToken($FreeToken));
                // return 'Email sent successfully!';
            // } catch (\Exception $e) {
            //     Log::error('Exception occurred: ' . $e->getMessage());
            //     return response()->json(['message' => 'An error occurred while sending the free token.'], 500);
            // }
            return response()->json([
                'message' => 'Paid link generated successfully! and Email sent successfully!',
                // 'freeLink' => $freetoken->paid_link,
                'freeToken' => $FreeToken,
                'expires_in' => $tokenExpiresAt,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Exception occurred: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while generating the paid link.',
            ], 500);
        }
    }
   
   

}
