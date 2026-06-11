<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Makes the Log Viewer "Back to Tetra" button return to the page
 * the visitor came from, instead of a fixed URL.
 *
 * - Arriving from another page on this site: that page is remembered
 *   in the session and used as the back URL (survives refreshes).
 * - Opening /log-viewer directly (bookmark, new tab): falls back to APP_URL.
 *
 * Registered in config/log-viewer.php 'middleware'.
 */
class SetLogViewerBackUrl
{
    public function handle(Request $request, Closure $next)
    {
        $referer = $request->headers->get('referer');

        $cameFromThisSite = $referer
            && parse_url($referer, PHP_URL_HOST) === $request->getHost();

        $cameFromLogViewer = $referer
            && str_contains(parse_url($referer, PHP_URL_PATH) ?? '', '/'.config('log-viewer.route_path', 'log-viewer'));

        if ($cameFromThisSite && ! $cameFromLogViewer) {
            session(['log_viewer_back_url' => $referer]);
        }

        config([
            'log-viewer.back_to_system_url' => session('log_viewer_back_url', config('app.url')),
        ]);

        return $next($request);
    }
}
