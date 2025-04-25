<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();

        if (($user->hasRole('admin') || $user->hasRole('super_admin')) && is_null($user->two_factor_secret)) {
            return response()->json(['error' => '2FA is required for this user'], 403);
        }

        return response()->json(compact('token', 'user'));
    }

    public function setup(Request $request)
    {
        $user = Auth::user();

        // Generate a 6-digit OTP
        $otp = Str::random(6);

        // Store OTP in the user's record
        $user->two_factor_secret = $otp;
        $user->two_factor_expires_at = now()->addMinutes(10); // Expiry time of 10 minutes
        $user->save();

        // Send OTP to the user's email
        Mail::to($user->email)->send(new OtpMail($otp));

        return response()->json([
            'message' => 'OTP sent to your email. Please use it to verify your identity.'
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required']);

        $user = Auth::user();

        // Check if OTP matches and if it hasn't expired
        if ($user->two_factor_secret === $request->code) {
            if ($user->two_factor_expires_at < now()) {
                return response()->json(['error' => 'OTP has expired'], 403);
            }

            // Mark 2FA as confirmed
            $user->two_factor_confirmed_at = now();
            $user->two_factor_secret = null; // Clear the OTP after verification
            $user->save();

            return response()->json(['message' => '2FA verified']);
        }

        return response()->json(['error' => 'Invalid OTP'], 403);
    }
}
