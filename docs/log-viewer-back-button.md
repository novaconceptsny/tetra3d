# Log Viewer "Back to Tetra" button — dynamic back URL

**Added:** 2026-06-11

## What it does

The "Back to Tetra" button on `/log-viewer` returns you to the page you came from, instead of always going to the site root.

- Arriving from any page on this site → the button points back to that page. It's remembered in the session, so refreshing or navigating within Log Viewer doesn't lose it.
- Opening `/log-viewer` directly (bookmark, new tab, pasted URL) → falls back to `APP_URL`.

## How it works

`app/Http/Middleware/SetLogViewerBackUrl.php` runs on the Log Viewer page (registered in `config/log-viewer.php` → `middleware`). On each page load it checks the `Referer` header; if it's a same-host page outside Log Viewer, it stores it in the session and injects it into `config('log-viewer.back_to_system_url')` at runtime — the value Log Viewer's UI uses for the button.

No vendor files are modified, so this survives package updates.

## Notes

- The session key is `log_viewer_back_url`.
- If the button ever appears wrong, check that the `web` middleware (session) is still first in the log-viewer middleware list.
