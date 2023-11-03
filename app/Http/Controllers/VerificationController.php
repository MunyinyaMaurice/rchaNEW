<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerificationControllers extends Controller
{

    public function verifyUserEmails($id) {
        // 2. Check if the user exists
        $user = User::find($id);
    
        if (!$user) {
            // Handle the case where the user does not exist
            return ['error' => 'User not found'];
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
    
}
