<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Check if there's a redirect parameter
        if ($request->has('redirect')) {
            $redirectUrl = $request->get('redirect');
            // Validate that the redirect URL is safe (same domain)
            if (filter_var($redirectUrl, FILTER_VALIDATE_URL)) {
                $parsedUrl = parse_url($redirectUrl);
                $currentHost = parse_url(config('app.url'), PHP_URL_HOST);
                if ($parsedUrl['host'] === $currentHost) {
                    return redirect($redirectUrl);
                }
            } else {
                // Handle relative URLs (not full URLs)
                $redirectUrl = ltrim($redirectUrl, '/');
                if (!empty($redirectUrl)) {
                    return redirect('/' . $redirectUrl);
                }
            }
        }

        // Default redirect behavior
        return redirect()->intended($this->redirectPath());
    }
}
