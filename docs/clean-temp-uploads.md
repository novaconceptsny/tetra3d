# `tetra:clean-temp-uploads` — Temporary Upload Cleanup

Removes leftover **Media Library Pro temporary-upload** directories from `storage/app/public`.

## Background

Every file uploaded through a Media Library Pro component is first staged in a temporary
folder on the `public` disk before it gets attached to its real model (Artwork, Surface,
Spot, ...). When the upload is saved, the file moves to its permanent `media/...` path —
but the staging folder is left behind. Abandoned uploads (user never saved the form) leave
folders *with* files behind.

Two staging patterns exist in this app:

| Pattern | Example | Source |
|---|---|---|
| md5 hash dirs at disk root | `storage/app/public/00dea7ee15fb83b4.../` | Media Library Pro default path |
| Numbered dirs | `storage/app/public/media/temporary_uploads/{upload_id}/{media_id}/` | Custom `App\MediaLibrary\PathGenerator` |

Spatie's own cleanup command (`media-library:delete-old-temporary-uploads`) requires the
paid Pro package (no longer in `composer.json`) and only deletes uploads still tracked in
the `temporary_uploads` DB table. By 2026 ~7,500 orphaned folders (~1.5 GB) had accumulated
because the Laravel scheduler cron was never set up on the production server.

## Usage

```bash
# Preview what would be deleted (nothing is removed)
php artisan tetra:clean-temp-uploads --dry-run

# Normal run: delete matching folders untouched for 2+ days
php artisan tetra:clean-temp-uploads

# Custom age threshold (minimum 1 day)
php artisan tetra:clean-temp-uploads --days=7

# One-time manual sweep ignoring the age check
# (do NOT schedule with --force; the age check protects in-progress uploads)
php artisan tetra:clean-temp-uploads --force
```

## What it will never touch

- `media/` content other than `media/temporary_uploads` (photos, surface_states, shared_layouts, ...)
- `3dmodel/`, tour files, or anything not matching the two patterns above
- Folders modified within the last `--days` days (unless `--force`)

## Scheduling

Registered in `bootstrap/app.php` to run **daily**:

```php
$schedule->command('tetra:clean-temp-uploads')->daily();
```

> **Note:** `app/Console/Kernel.php` is dead code since the Laravel 12 upgrade —
> scheduling lives in `bootstrap/app.php` only.

The scheduler only fires if the server's crontab calls it every minute:

```cron
* * * * * cd /path/to/tetra3d && php artisan schedule:run >> /dev/null 2>&1
```

Check with `crontab -l` (run as the user the app runs under).

## Source

`app/Console/Commands/CleanTemporaryUploadDirectories.php`
