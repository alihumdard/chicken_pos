<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Mail\SendOtpMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();
        $otp = rand(100000, 999999);

        $user->otp = $otp;
        $user->reset_pswd_time = Carbon::now();
        $user->save();

        Mail::to($user->email)->send(new SendOtpMail($otp));

        return response()->json(['message' => 'An OTP has been sent to your email address.']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|numeric',
        ]);

        $user = User::where('email', $request->email)->where('otp', $request->otp)->first();

        if (!$user) {
            throw ValidationException::withMessages(['otp' => 'Invalid OTP.']);
        }

        $resetTime = Carbon::parse($user->reset_pswd_time);
        if ($resetTime->addMinutes(10)->isPast()) {
            throw ValidationException::withMessages(['otp' => 'OTP has expired. Please request a new one.']);
        }

        return response()->json(['message' => 'OTP verified successfully. You can now reset your password.']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|numeric',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)->where('otp', $request->otp)->first();

        if (!$user) {
            throw ValidationException::withMessages(['email' => 'Invalid OTP or email provided.']);
        }

        $user->password = Hash::make($request->password);
        $user->otp = null;
        $user->reset_pswd_time = null;
        $user->save();

        return response()->json(['message' => 'Your password has been changed successfully.']);
    }
}