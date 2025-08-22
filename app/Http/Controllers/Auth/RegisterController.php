<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Mail\VerificationCodeMail;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/verify-email';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Generate verification code
        $verificationCode = strtoupper(Str::random(6));
        
        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'company' => $data['company'] ?? null,
            'email' => $data['email'],
            'password' => $data['password'],
            'verification_code' => $verificationCode,
            'verification_code_expires_at' => Carbon::now()->addMinutes(10),
            'is_verified' => false,
        ]);
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        // Store email in session for verification form
        session(['verification_email' => $user->email]);
        
        // Send verification email
        try {
            Mail::to($user->email)->send(new VerificationCodeMail($user->verification_code, $user->first_name));
            
            // Check if we're using log driver
            if (config('mail.default') === 'log') {
                return redirect()->route('verification.notice')
                    ->with('success', 'Registration successful! Verification code has been generated and logged. Check your Laravel logs for the code.')
                    ->with('verification_code', $user->verification_code); // Show code for testing
            }
            
            return redirect()->route('verification.notice')
                ->with('success', 'Registration successful! Please check your email for the verification code.');
                
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Failed to send verification email: ' . $e->getMessage());
            
            // If email fails, still redirect to verification page but show error
            return redirect()->route('verification.notice')
                ->with('error', 'Registration successful! However, we could not send the verification email. Error: ' . $e->getMessage())
                ->with('verification_code', $user->verification_code); // Show code for testing
        }
    }
}
