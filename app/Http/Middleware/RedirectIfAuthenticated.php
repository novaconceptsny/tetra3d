<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // If there's a redirect parameter, use it instead of the default HOME
                if ($request->has('redirect')) {
                    $redirectUrl = $request->get('redirect');
                    
                    // Validate and reconstruct URL for cross-environment compatibility
                    if (filter_var($redirectUrl, FILTER_VALIDATE_URL)) {
                        $parsedUrl = parse_url($redirectUrl);
                        $currentHost = parse_url(config('app.url'), PHP_URL_HOST);
                        
                        // If same domain, redirect directly
                        if ($parsedUrl['host'] === $currentHost) {
                            return redirect($redirectUrl);
                        }
                        
                        // For cross-environment, reconstruct URL
                        if (isset($parsedUrl['path']) && !empty($parsedUrl['path'])) {
                            $path = $parsedUrl['path'];
                            $query = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
                            $fragment = isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '';
                            $reconstructedUrl = config('app.url') . $path . $query . $fragment;
                            
                            if (filter_var($reconstructedUrl, FILTER_VALIDATE_URL)) {
                                return redirect($reconstructedUrl);
                            }
                        }
                    } else {
                        // Handle relative URLs
                        $redirectUrl = ltrim($redirectUrl, '/');
                        if (!empty($redirectUrl)) {
                            return redirect('/' . $redirectUrl);
                        }
                    }
                }
                
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
