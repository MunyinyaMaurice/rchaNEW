<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->has('token_expires_at')) {
            $expiresAt = $request->session()->get('token_expires_at');
            if (now()->gt($expiresAt)) {
                // Session has expired, redirect to 'home' route
                // return redirect()->route('error');
                return response()->json([
                    'message' => 'Session is expired.',
                ], 422);
            }
        }
        return $next($request);
    }
}
