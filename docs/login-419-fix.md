# Login 419 "Page Expired" fix

**Date:** 2026-06-10

## Symptom

Submitting the login form at `/login` intermittently showed **419 Page Expired**, even though the user ended up logged in (visiting `tetra3d.site` afterward went straight to `/tour-360`). Only happened in a browser with a previous session still alive; never after an explicit logout, and never in a fresh browser.

## Cause

The user was already authenticated (`SESSION_LIFETIME=120`), but the browser displayed a **stale cached copy** of the login form containing a CSRF token from an old session. Submitting it caused a token mismatch → 419. A fresh request to `/login` would never have shown the form at all — the `guest` middleware redirects authenticated users to the dashboard.

## Fix

Two changes:

1. **`app/Http/Controllers/Auth/LoginController.php`** — constructor now applies `cache.headers:no_store` to `showLoginForm`, so browsers never cache the login page and always fetch a fresh CSRF token.

2. **`bootstrap/app.php`** — `withExceptions` renders `TokenMismatchException` as a redirect back (input preserved except token/password, flash message in `status`) instead of the 419 error page. If the user is already authenticated, the `guest` middleware then forwards them to `/tour-360`.

## Notes

- The login blade can display the flash message via `session('status')` if desired.
- After deploying, run `php artisan optimize:clear` so the new exception handler is picked up.
