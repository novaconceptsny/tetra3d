<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\VerificationCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EmailVerificationController extends Controller
{
    /**
     * Show the verification form
     */
    public function showVerificationForm()
    {
        return view('auth.verify-email');
    }

    /**
     * Send verification code
     */
    public function sendVerificationCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        if ($user->is_verified) {
            return back()->withErrors(['email' => 'Email is already verified.']);
        }

        // Generate verification code
        $verificationCode = strtoupper(Str::random(6));
        
        // Update user with verification code
        $user->update([
            'verification_code' => $verificationCode,
            'verification_code_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Send verification email
        try {
            Mail::to($user->email)->send(new VerificationCodeMail($verificationCode, $user->first_name));
            
            return back()->with('success', 'Verification code has been sent to your email.');
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Failed to send verification code. Please try again.']);
        }
    }

    /**
     * Verify email with code
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'verification_code' => 'required|string|size:6'
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        if ($user->is_verified) {
            return redirect()->route('login')->with('success', 'Email is already verified. Please login.');
        }

        // Check if verification code is valid and not expired
        if ($user->verification_code !== $request->verification_code) {
            return back()->withErrors(['verification_code' => 'Invalid verification code.']);
        }

        if (Carbon::now()->isAfter($user->verification_code_expires_at)) {
            return back()->withErrors(['verification_code' => 'Verification code has expired. Please request a new one.']);
        }

        // Mark user as verified
        $user->update([
            'is_verified' => true,
            'email_verified_at' => Carbon::now(),
            'verification_code' => null,
            'verification_code_expires_at' => null,
        ]);

        return redirect()->route('login')->with('success', 'Email verified successfully! You can now login.');
    }

    /**
     * Resend verification code
     */
    public function resendVerificationCode(Request $request)
    {
        return $this->sendVerificationCode($request);
    }
} 