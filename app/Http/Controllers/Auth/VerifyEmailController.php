<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
         // Get the user whose email address needs to be verified.
        // $user = User::where('id', $request->route('id'))->first();
          // Verify the user's email address.
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }
        return redirect()->route('/home');
        //return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
    }
    // public function __invoke(EmailVerificationRequest $request): RedirectResponse
    // {
    //      // Get the user whose email address needs to be verified.
    //      $user = User::where('id', $request->route('id'))->first();

    //      // Verify the user's email address.
    //      if ($request->user()->hasVerifiedEmail()) {
    //          return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
    //      }
 
    //      // Mark the user's email address as verified.
    //      $user->markEmailAsVerified();
 
    //      event(new Verified($request->user()));
 
    //      return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
    // }
    
    
}

