<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        using: function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Default web middleware is handled automatically by Laravel 12

        $middleware->alias([
            'auth'           => \App\Http\Middleware\Authenticate::class,
            'auth.basic'     => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'cache.headers'  => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can'            => \Illuminate\Auth\Middleware\Authorize::class,
            'guest'          => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'signed'         => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle'       => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified'       => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'email.verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'email.strict'   => \App\Http\Middleware\StrictEmailVerification::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Only present when the paid Media Library Pro package is installed.
        if (class_exists(\Spatie\MediaLibraryPro\Models\TemporaryUpload::class)) {
            $schedule->command('media-library:delete-old-temporary-uploads')->daily();
        }

        // Sweep leftover temporary-upload hash directories from storage/app/public.
        $schedule->command('tetra:clean-temp-uploads')->daily();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
