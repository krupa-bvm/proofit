<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        dd(JWTAuth::attempt($credentials));
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();

        if (($user->hasRole('admin') || $user->hasRole('super_admin')) && is_null($user->two_factor_confirmed_at)) {
            return response()->json(['error' => '2FA is required for this user'], 403);
        }

        return response()->json(compact('token', 'user'));
    }

    public function setup(Request $request)
    {
        $user = Auth::user();

        // Generate a 6-digit OTP (digits only)
        $otp = rand(100000, 999999);

        // Store OTP and its expiry
        $user->two_factor_secret = $otp;
        // $user->two_factor_expires_at = now()->addMinutes(10);
        $user->save();
dd($otp);
        // Send OTP as a raw email
        Mail::raw("Your OTP code is: $otp\n\nIt will expire in 10 minutes.", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Your OTP Code');
        });

        return response()->json([
            'message' => 'OTP sent to your email. Please use it to verify your identity.'
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required']);

        $user = Auth::user();

        if ($user->two_factor_secret === $request->code) {
            // if ($user->two_factor_expires_at < now()) {
            //     return response()->json(['error' => 'OTP has expired'], 403);
            // }

            $user->two_factor_confirmed_at = now();
            $user->two_factor_secret = null; 
            $user->save();

            return response()->json(['message' => '2FA verified']);
        }

        return response()->json(['error' => 'Invalid OTP'], 403);
    }
}
