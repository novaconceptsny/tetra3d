# Tetra3D Change Log

Detailed record of every change made to this codebase (by people or AI).
Newest entries at the top. AI assistants: read this file before working on the code,
and add an entry for every change you make (see `CLAUDE.md` for the format).

---

## 2026-10-08 — Inventory: "Server Error" when adding artworks, duplicate rows, broken images

**Type:** Bug fix
**Made by:** Claude (AI), requested by Nova
**Status:** Code changed locally — image code tested against spatie/image 3.9.4 + GD; not yet tested in the full app / not deployed

### Problem
On `app.tetra3d.com/inventory`, adding artworks with an image (one or many, via **+ Artwork → Save All**):
- An alert said **"Error saving items: Server Error"**.
- But the artworks *were* created — trying 3 times created 3 copies (collection `test`: "379233" and two "tumblr_lo2srfoT4D1qikgdeo1_r1_1280").
- Those artworks had **no image**: placeholder in Inventory, and in the tour editor the artwork list showed "card-img" and the alert **"Image could not be loaded..."**.
- The description typed in the row was also not saved.

### Root cause
1. `app/Models/Artwork.php` and `app/Http/Controllers/InventoryController.php` use `Intervention\Image\Facades\Image`,
   but the **`intervention/image` package is not installed** (it is not in `composer.json` / `composer.lock`; it was
   dropped during the Laravel 12 upgrade — medialibrary 11 now uses `spatie/image` 3 instead).
   Calling `Image::make()` throws `Error: Class "Intervention\Image\Facades\Image" not found`.
2. That is a PHP `Error`, not an `Exception`. Every `catch (\Exception $e)` around the image code missed it, so it went
   straight to Laravel's handler → HTTP 500 with the generic message "Server Error".
3. The artwork row is saved (`$artwork->save()`) **before** the image code runs, so each attempt left an artwork with no
   image behind. The front-end kept the unsaved rows on screen after the error, so the user clicked Save again → duplicates.
   With several rows, the request died on the first image, so only the first row was saved each time.
4. `bulkStore()` never copied `description` onto the artwork.

### Decision
- Use **`spatie/image`** (already installed as a dependency of spatie/laravel-medialibrary; picks Imagick or GD
  automatically). No `composer require` needed, so deploying the code is enough.
- If an image cannot be stored, the artwork is **deleted again** and reported as failed — never leave an artwork without its image.
- Catch `\Throwable` (not only `\Exception`) and log it, so the user sees the real error message.
- Front-end removes only the rows the server confirms as saved; failed rows stay (red border) so they can be retried without duplicates.

### Changes
1. **`app/Http/Controllers/InventoryController.php`**
   - `use Intervention\Image\Facades\Image` → `use Spatie\Image\Image`.
   - `compressImage()` rewritten with spatie/image: writes the upload to a temp file, `Image::load()`, `width($maxWidth)` if wider,
     `background('#ffffff')` (transparent PNG → white, not black), same quality loop 70→40 and the extra 80% shrink pass as before,
     returns `data:image/jpeg;base64,...`. Temp file deleted in `finally`. Catches `\Throwable`.
   - New private `attachArtworkImage(Artwork $artwork, string $base64Image): ?string` — compress → `addMediaFromBase64()` →
     `updateSizeData()` → `resizeImage()`. Returns `null` on success or the error message if the image could not be stored.
     Errors after the image is stored (size data/resize) are only logged as warnings.
   - `bulkStore()`: saves `description`; reads `row_key` from each item; uses `attachArtworkImage()` and deletes the artwork if
     it returns an error; catches `\Throwable` per item and overall (logged with `\Log::error`). Response now includes
     `saved_row_keys`, `failed_row_keys`, `errors` (also on the 400 "nothing saved" response).
   - `addArtworks()` (the multi-upload / import modal): same `attachArtworkImage()` + delete-on-failure, `\Throwable` catches, logging.
2. **`app/Models/Artwork.php`**
   - `use Intervention\Image\Facades\Image` → `use Spatie\Image\Image`.
   - `getOriginalAspectRatio()`: uses `getimagesize()` on the media file (no full decode, no 1G memory bump); returns 1 if unreadable.
   - `resizeImage()`: returns early if there is no media or the target size is < 1px; uses `Image::load()->resize(w, h)` and
     `base64($format)` keeping the original file format (was `encode('data-url')`).
3. **`resources/views/inventory/index.blade.php`** (Save All for new rows)
   - Each new row gets `data-save-key` and sends `row_key` with its data.
   - New `handleBulkSaveResult(response)`: removes only rows in `saved_row_keys`, adds `border-danger` to rows in
     `failed_row_keys`, reloads the table if anything was saved, and shows the message plus each error line.
   - `error:` handler: if the server sent `failed_row_keys` (400) it uses the same handler; for unexpected errors it shows the
     message, warns that some items may already be saved, and reloads the table.

### Not changed / follow-ups
- **Existing broken artworks are still in the database** (the 3 in collection `test`, and any others created while this bug
  was live). Delete them from Inventory, or find them with:
  ```sql
  SELECT a.id, a.name, a.created_at FROM artworks a
  LEFT JOIN media m ON m.model_type = 'App\\Models\\Artwork' AND m.model_id = a.id AND m.collection_name = 'image'
  WHERE m.id IS NULL ORDER BY a.created_at DESC;
  ```
  Some may be on layouts already (surface states) — check before deleting.
- `compressImage()` target size is still **2 KB** (pre-existing) — in practice every image ends at quality 40 and ~640px wide.
  Consider raising `$targetSizeKB` if images look too soft.
- The server must have the PHP **GD or Imagick** extension (needed by spatie/image and medialibrary anyway).
- No other files in `app/` use Intervention. If any other code (outside `app/`) does, it will fail the same way.

### How to test
1. Inventory → **+ Artwork** → one row with a JPG, collection, title, description → **Save All**.
   Expect "Successfully saved 1 item(s)."; row disappears; artwork shows with its image and description; only 1 artwork created.
2. Same with a transparent PNG → image saved, background white.
3. Several rows at once (like the 11-row test) → all saved with images; no duplicates.
4. Open a layout in the tour editor and add one of the new artworks → image loads, no "Image could not be loaded".
5. Multi-upload / import modal (`/inventory/artworks/add`) with images → artworks created with images.
6. Force a failure (e.g. temporarily rename the media disk folder) → alert lists the failed item; that row stays with a red border;
   no artwork without an image is left in the table. Check `storage/logs/laravel.log` for the logged error.

---

## 2026-10-08 — Layouts left behind after a tour is removed from a project

**Type:** Bug fix
**Made by:** Claude (AI), requested by Nova
**Status:** Code changed locally — not yet tested on staging / not deployed

### Problem
Project `2234` (company Renata) has only one tour attached (Template Gallery 09), but its
Layouts panel showed 3 layouts on other tours (Template Gallery 01, 02, 07), and you could
click **Enter** and open them.

### Root cause
There are two places to edit a project's tours, and they behaved differently:

| Place | File | What it did when a tour was removed |
|---|---|---|
| Backend admin "Edit Project" | `app/Http/Controllers/Backend/ProjectController.php` → `update()` | Deleted that tour's layouts in the project ✅ |
| Front-end Tour 360 "Edit project" | `app/Http/Controllers/Tour360Controller.php` → `update()` | Only ran `tours()->sync()` — **layouts were left behind** ❌ |

On top of that, nothing checked whether a layout's tour was still attached:
- The Layouts panel (`resources/views/livewire/updated-tour-switcher.blade.php`) listed `$project->layouts()` — every layout.
- `TourController@show` (the **Enter** link) only checked that the user can see the project.

Also found: the backend deleted layouts with a mass query (`Layout::where(...)->delete()`),
which does **not** fire the `Layout::deleted` model event, so the layouts' `surface_states`
were never deleted.

### Decision
Option A : removing a tour from a project **permanently deletes** that
tour's layouts in that project — same rule as the backend. The front-end now asks for
confirmation first. Orphaned layouts that already exist are hidden and cannot be opened.

### Changes
1. **`app/Models/Layout.php`**
   - Added scope `scopeOnAttachedTour()` → only layouts whose `(project_id, tour_id)` exists in the `project_tour` pivot.
   - Added `isOnAttachedTour(): bool` → same check for a single layout.
2. **`app/Models/Project.php`**
   - Added `activeLayouts()` → `layouts()->onAttachedTour()`. Use this for anything users see.
   - Added `deleteLayoutsForTours(array $tourIds): int` → deletes layouts one by one so
     `Layout::deleted` fires and their surface states are deleted too. Returns how many were deleted.
3. **`app/Http/Controllers/Tour360Controller.php`**
   - `update()`: captures the result of `tours()->sync()`. For detached tours, calls
     `$project->deleteLayoutsForTours()`. Logs a `tours_updated` activity (like the backend).
     JSON response now includes `deleted_layouts`.
   - `edit()`: JSON now includes `layoutCountsByTour` (`{tour_id: count}`) for the confirmation dialog.
   - `index()`: project `layouts_count`, `$favorites` and `$allLayouts` (global search) only include layouts on attached tours.
4. **`app/Http/Controllers/Backend/ProjectController.php`**
   - `update()`: uses `$project->deleteLayoutsForTours()` instead of the mass delete, so surface states are cleaned up.
5. **`app/Http/Controllers/TourController.php`**
   - `show()`: when opened with `layout_id`, returns **404** if the layout's tour is no longer attached to its project.
6. **`app/Livewire/UpdatedTourSwitcher.php`**
   - `toggleFavorite()`: refreshed favourites list only includes layouts on attached tours.
7. **`resources/views/livewire/updated-tour-switcher.blade.php`**
   - Layouts panel uses `$project->activeLayouts()` instead of `$project->layouts()`.
8. **`resources/views/tour360/index.blade.php`**
   - Project card layout count falls back to `activeLayouts()->count()`.
   - Edit project: remembers the original tours and layout counts when the form loads.
   - `handleUpdateProject()`: if any removed tour has layouts, shows a `confirm()` listing
     `tour name: N layout(s)` and stops if the user cancels.

### Not changed / follow-ups
- **Existing orphaned layouts are still in the database** (e.g. layouts `A`, `1`, `b` in project 2234).
  They are now hidden and blocked. If the tour is attached to the project again, they reappear.
  To find them:
  ```sql
  SELECT l.id, l.name, l.project_id, l.tour_id
  FROM layouts l
  LEFT JOIN project_tour pt ON pt.project_id = l.project_id AND pt.tour_id = l.tour_id
  WHERE pt.project_id IS NULL;
  ```
  Decide later whether to delete them.
- Sculptures linked by `layout_id` are not deleted when a layout is deleted (pre-existing behaviour, `Layout::boot` only deletes surface states).
- `Backend/ProjectController.php` still imports `App\Models\Layout` (now unused, harmless).

### How to test
1. Project with tours A and B, with a layout on each. Front-end **Edit project** → remove B → **Update**.
   Expect a confirmation listing B's layouts. Cancel → nothing changes. Confirm → B's layouts are gone; A's remain.
2. Open project 2234's Layouts panel → only layouts on Template Gallery 09 show; the card count matches.
3. Open an old Enter URL for an orphaned layout (`/tours/{tour}?layout_id={id}`) → 404.
4. Favourites row and global search no longer show orphaned layouts.
5. Backend admin → edit project → remove a tour → its layouts **and** their surface states are deleted.
