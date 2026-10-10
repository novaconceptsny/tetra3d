# Tetra3D Change Log

Detailed record of every change made to this codebase (by people or AI).
Newest entries at the top. AI assistants: read this file before working on the code,
and add an entry for every change you make (see `CLAUDE.md` for the format).

---

## 2026-10-09 — Tour editor: new sculptures dropped at a size-aware distance (gizmo visible)

**Type:** Improvement
**Made by:** Claude (AI), requested by Nova
**Status:** Code changed locally — JS syntax-checked; not yet tested in a browser / not deployed

### Problem
Clicking a sculpture in the Sculpture List always dropped it **1 m in front of the camera** (`findAddModelPosition()` returned
the camera's unit direction × 200). Large sculptures then filled the whole screen and you had to zoom out to find the gizmo.

### Decision
Keep dropping it in the direction you are looking, but choose the distance so the whole sculpture fits in the view,
and never behind a wall.

### Changes
**`resources/views/pages/tour.blade.php`**
1. `findAddModelPosition(maxDimension)` — horizontal direction you are looking at (same `-direction` convention as before);
   distance = `size*1.2 / (2*tan(fov/2)) + size/2` using krpano `view.fov` and the sculpture's largest dimension
   (min 0.5 m), clamped to **1.5 m – 15 m**, and shortened to stay in front of the first wall. Returns `{x, z}` in scene
   units (200 = 1 m) — callers no longer multiply by `offsetScale`.
2. New `distanceToWall(direction)` — raycast from the viewer against the (invisible) 3D space model `model`; `null` if no model.
3. `add_model()` — computes the drop position **after** the GLB is loaded, from its bounding box × `sculptureScaleFor(imageId)`
   (so the resize factor from the sculpture page is included), and passes it to `addTemp()`.
4. `addTemp(object, url, spherical_position)` — uses the given position so the invisible interaction model sits exactly on the
   sculpture (falls back to computing one if not given).

### Not changed / follow-ups
- Height is unchanged (`offset_y`, as before). Rotation unchanged (`rz: -180`).
- Loading saved sculptures (`load_model`) is unchanged — only newly added ones.
- If the space model has furniture/low walls at camera height, the wall check may place the sculpture closer (min 0.5 m).

### How to test
1. Open a layout, Sculpture List → click a large sculpture → it appears fully in view in front of you, gizmo/label reachable without zooming.
2. Click a small sculpture → appears ~1.5 m away (not tiny far away).
3. Face a nearby wall and add a big sculpture → it stays in front of the wall, not behind it.
4. Drag / rotate / save the new sculpture → move to another spot → it is where you saved it.

---

## 2026-10-09 — Company admins can upload / edit sculptures from Inventory (backend stays super-admin only)

**Type:** Feature (permissions)
**Made by:** Claude (AI), requested by Nova
**Status:** Code changed locally — PHP/JS syntax-checked; not yet tested in a browser / not deployed

### Problem
Only super admins could add sculptures (backend `/backend/sculptures`, `SculptureModelPolicy` = super admin only).
Company admins need to upload and edit their company's sculptures — but from the **Inventory** page, not the backend.
Sculptures were not shown on the Inventory page at all.

### Decision
- New front-end section **Inventory → Sculptures** (`/inventory/sculptures`): list, add, edit, delete.
- Who: **company admins** (own company only) and super admins (all companies). Employees: no access.
- Backend sculpture pages unchanged: still super-admin only (policy not touched, so the backend sidebar item stays hidden for company admins).
- Re-use the existing sculpture form (3D preview, auto size, resize) instead of a copy, rendered with the front-end layout.
- Company admins cannot choose the company; collections are limited to their company.

### Changes
1. **`app/Providers/AuthServiceProvider.php`** — new gate `manage-inventory-sculptures` = `isSuperAdmin() || isCompanyAdmin()`.
2. **`routes/web.php`** — in the `auth` group: `inventory/sculptures` group with `can:manage-inventory-sculptures`, names
   `inventory.sculptures.index|create|store|edit|update|destroy` → `InventorySculptureController`.
3. **`app/Http/Controllers/InventorySculptureController.php`** (new)
   - `index` (optional `collection_id` filter), `create`, `store`, `edit`, `update`, `destroy`.
   - Company admin: `company_id` is always `user()->company_id`; super admin: `company_id` from the form (required).
   - `ensureCollectionBelongsToCompany()` — collection must belong to that company (validation error otherwise).
   - `ensureCanManage()` — 403 if a company admin opens another company's sculpture (HasCompany global scope already hides them; double check).
   - Validation: `ValidationRules::storeSculpture()` / `updateSculpture()` + `artwork_collection_id` required.
   - Redirects back to `inventory.sculptures.index` with a success message.
4. **`resources/views/inventory/sculptures/index.blade.php`** (new) — table: thumbnail, (company for super admin), collection,
   name, artist, type, size L x W x H m (+ "% of model" if resized), edit / delete (with confirm). Collection filter, "Add Sculpture", back to Inventory.
5. **`resources/views/backend/sculpture/form.blade.php`** (shared)
   - `@extends($layout ?? 'layouts.backend')`; optional `$backUrl` (back button + page padding).
   - `$lockCompany`: shows the company as read-only text instead of the select; collection select lists that company's collections.
   - Front-end layout: adds `@mediaLibraryStyles` (missing from `layouts.redesign`) and skips the duplicate three.js import map
     (already in `layouts.redesign`).
   - JS: company-select listeners are skipped when there is no company select.
   - Backend behaviour unchanged (no `$layout` / `$lockCompany` passed).
6. **`resources/views/inventory/index.blade.php`** — "Sculptures" button in the toolbar (next to "+ Artwork"), shown with `@can('manage-inventory-sculptures')`.

### Not changed / follow-ups
- **Placing sculptures in tours** (`sculpture_save` / `sculpture_delete`, gate `perform-admin-actions`) is still **super admin only**.
  Company admins can upload sculptures but cannot save their placement in a layout yet — waiting for Nova's decision.
- Pre-existing: `HasCompany::booted()` sets `company_id` to the *logged-in user's* company on `created`. When a super admin
  creates a sculpture for another company (backend or inventory), it ends up in the super admin's own company. Not changed.
- Company admins can also **delete** their sculptures (deleting removes them from every layout where they are placed).
- Backend `SculptureController::update()` returns a view instead of a redirect (pre-existing).

### How to test
1. Log in as a **company admin** → Inventory → "Sculptures" button → list shows only own company's sculptures.
2. Add Sculpture → company is fixed, collections are own company's → upload GLB/thumbnail/interaction → preview + size work → Create → back on the list.
3. Edit it (change size/name) → Update → list shows new values. Delete → gone.
4. Company admin opens `/backend/sculptures` → still not allowed; backend sidebar has no Sculptures item.
5. Company admin opens `/inventory/sculptures/{id of another company}/edit` → 404/403.
6. **Employee** → no Sculptures button; `/inventory/sculptures` → 403.
7. **Super admin** → Inventory → Sculptures shows all companies with a Company column; backend sculpture pages still work as before.

---

## 2026-10-09 — Backend sculpture page: sculpture size can be changed (proportional resize)

**Type:** Feature
**Made by:** Claude (AI), requested by Nova
**Status:** Code changed locally — PHP/JS syntax-checked; not yet tested in a browser / not deployed

### Problem
On `/backend/sculptures/create` (and edit) the Length / Width / Height fields were read-only and always equal to the size
of the GLB file. There was no way to make a sculpture bigger or smaller than the exported model.

### Decision
- **Uniform (proportional) resize only** — changing one value updates the other two, so the sculpture is never stretched.
- Store a size factor `data.scale` (1 = model as exported) plus the measured model size
  (`data.original_length/width/height`). `data.length/width/height` keep holding the **displayed / real size in metres**
  (already used for the sculpture list "L x W x H meter" in the tour).
- The tour multiplies its fixed sculpture scale (200) by `data.scale`, so placed sculptures appear at the new size.
- Allowed factor: 0.01–100 (1%–10000%).

### Changes
1. **`resources/views/backend/sculpture/form.blade.php`**
   - Length / Width / Height are editable number inputs (`step="any"`, `min="0.01"`), labelled "(m)".
   - New hidden inputs `data[scale]`, `data[original_length]`, `data[original_width]`, `data[original_height]`.
   - New "Reset to model size" button and an info line ("Resized to 150% of the original model (…)").
   - JS: `GLTFLoad(url, initialScale)` measures the model at scale 1 → `originalSize`, then `applyScale()` scales the preview,
     fills all fields and hidden inputs, and re-frames the camera. Typing in a size field → `onSizeFieldInput()` computes the
     factor from that field and updates the others. `getSize()` no longer writes to the fields.
   - New upload starts at scale 1; editing an existing sculpture starts at its saved `data.scale` (old sculptures → 1).
2. **`app/Helpers/ValidationRules.php`** — `storeSculpture()` (and so `updateSculpture()`): `data.length/width/height`
   `nullable|numeric|gt:0`, `data.scale` `nullable|numeric|between:0.01,100`, `data.original_*` `nullable|numeric`.
   (Controller already saves the whole `data` array via `$request->only([... 'data' ...])` — no controller change.)
3. **`resources/views/pages/tour.blade.php`**
   - `sculptureUrls[id]` now includes `scale` (from `data.scale`, default 1).
   - New `sculptureScaleFor(imageId)` = `sculptureScale * scale`; used in `add_model()`, `load_model()`, and for the invisible
     interaction model in `loadTemp()` / `addTemp()` (addTemp used `tourScale`, same value 200) so click/drag area matches.

### Not changed / follow-ups
- Changing a sculpture's size also changes it in **every layout** where it is already placed (positions stay the same).
- Assumes GLB units are metres (glTF standard). A model exported in cm/mm will show 100×/1000× too big — fix with the size fields.
- No non-uniform (stretch) resize.

### How to test
1. Create a sculpture with a GLB → fields fill with the model size, info says "Original model size".
2. Change Height to double → Length and Width double, preview grows, info "Resized to 200%". Save.
3. Edit it again → preview opens at the saved size; "Reset to model size" returns to 100%.
4. In a tour/layout, add the sculpture → it appears at the new size; click/drag/save still work on it.
5. Existing sculptures (created before this change) → open edit → shown at 100%, unchanged in tours.
6. Try 0 or a negative value → not accepted.

---

## 2026-10-09 — Tour editor: placed sculptures disappear after moving to another spot

**Type:** Bug fix
**Made by:** Claude (AI), requested by Nova
**Status:** Code changed locally — PHP/JS syntax-checked; not yet tested in a browser / not deployed

### Problem
In a layout, a sculpture (e.g. "sculpture 01", 3ds max) is placed in the 360 view and saved with the save button.
After clicking a navigation hotspot to move to another spot, the sculpture is gone (and it is also gone after a reload).

### Root cause
The sculpture was **never saved**:
1. The tour page (`resources/views/pages/tour.blade.php`) posts to `route('sculpture_save')` / `route('sculpture_delete')`,
   which were defined in **`routes/api.php`** with `auth:sanctum`.
2. Since the Laravel 12 upgrade, routes are registered in `bootstrap/app.php`; the old `app/Http/Kernel.php` (which
   defined the `api` group) is no longer used, and `statefulApi()` is not enabled. So `/api/*` requests get **no session**,
   `auth:sanctum` cannot see the browser login, and every save/delete returned **401 Unauthenticated**.
3. The `$.ajax` `success` / `error` callbacks were empty, and the save button set `userData.changed = false` *before*
   the request — so nothing told the user the save failed.
4. Moving to another spot is a full page load (`NavigateTo` in `public/krpano/action.xml` → `tours.show?spot_id=…&layout_id=…`),
   which loads sculptures from the `sculptures` table for the layout — empty, so nothing is drawn.
(The save/load position maths — `position + spot offset` on save, `position − spot offset` on load — is consistent; not the cause.)

### Decision
Move these routes into the authenticated **web** route group (session + CSRF), keep the **same route names** so the
Blade code keeps working, keep the existing permission (`can:perform-admin-actions`), and show an error when a save fails.

### Changes
1. **`routes/web.php`** — inside the `auth` group, new group `prefix('sculpture-placements')` with
   `can:perform-admin-actions`: `POST save` → `sculpture_save`, `POST delete` → `sculpture_delete`, `POST load` → `sculpture_load`
   (`App\Http\Controllers\SculptureController`), `POST canvas-image` → `store_canvas_image` (`Backend\SculptureController`).
   Full class names are used because `web.php` already imports `Backend\SculptureController` as `SculptureController`.
2. **`routes/api.php`** — removed the 4 sculpture routes and their `use` lines (comment points to web.php).
   URLs change from `/api/sculpture_save` etc. to `/sculpture-placements/save` etc.; nothing hard-codes the old URLs.
3. **`resources/views/pages/tour.blade.php`**
   - Save and delete `$.ajax` calls send `X-CSRF-TOKEN` (from `<meta name="csrf-token">` in `layouts/redesign`).
   - Save: `object.userData.changed = false` only in `success`; on `error` it stays "changed" and an alert explains why.
   - Delete: alert on error.
   - New helper `sculptureRequestErrorMessage(xhr, action)` (401/419 → session expired, 403 → not allowed, else status + message).

### Not changed / follow-ups
- **Permission:** `perform-admin-actions` = **super admin only** (`AuthServiceProvider`). Company users see the save
  button but will now get "your account is not allowed to change sculptures". Decide whether company admins/users should
  be allowed to place sculptures; if so, change the gate on this route group.
- Sculptures placed before this fix were never stored — they need to be placed and saved again.
- `store_canvas_image` route points to `Backend\SculptureController::store_canvas_image`, which **does not exist**
  (pre-existing, nothing calls it).
- Other routes left in `routes/api.php` (`/api/user`, `/api/companies*`) get no session either; check they don't rely on login.
- After deploying run `php artisan route:clear` (or `route:cache`) so the new routes are picked up.

### How to test
1. Log in as super admin, open a layout in the tour, add a sculpture, move/rotate it, click save → no alert.
2. Click a navigation hotspot to another spot → the sculpture is there (in the same place in the room).
3. Reload the page → still there. Delete it → reload → gone.
4. As a non-super-admin user → clicking save shows the "not allowed" alert (and nothing disappears silently).
5. Let the session expire (or log out in another tab) → save shows the "session has expired" alert.

---

## 2026-10-09 — Backend "Add New Sculpture": 3D preview stays empty, Length/Width/Height not filled

**Type:** Bug fix
**Made by:** Claude (AI), requested by Nova
**Status:** Code changed locally — JS syntax-checked; not yet tested in a browser / not deployed

### Problem
`/backend/sculptures/create`: after choosing a GLB in **Sculpture Model**, the file uploads ("GLB 2.23 MB  Remove")
but the 3D preview on the right stays empty grey and Length / Width / Height stay empty. Tried with 2 different models.
This used to work.

### Root cause
The preview and size fill-in are done in the browser (three.js) in `resources/views/backend/sculpture/form.blade.php`,
by reading the chosen file from the upload `<input>`. Not reproduced in a browser (no access to the backend login from
the AI session), but the code has three ways to fail silently:
1. **Listener attached to the wrong thing for the new uploader.** The old code attached a `change` listener directly
   to each `<input>` inside `#sculpture-model-upload` (re-attached every animation frame). Since the Laravel 12 /
   Livewire 3 / medialibrary-pro 6 upgrade, that uploader is a Livewire component:
   - **dragging** a file onto the drop zone calls `$wire.upload()` directly (`handleDrop` in
     `vendor/spatie/laravel-medialibrary-pro/resources/views/livewire/uploader.blade.php`) — the input never fires `change`,
     so the preview code never runs;
   - Livewire can re-render/replace the input.
2. **Compressed GLB files.** `GLTFLoader` had no `DRACOLoader` / `MeshoptDecoder`, so Draco- or Meshopt-compressed GLBs
   (common from exporters/optimisers) fail to load.
3. **No error callback** on `loader.load()`, so any load failure was invisible (only in the browser console).
Also `OrbitControls` was imported without `.js` (`three/addons/controls/OrbitControls`).

### Decision
Make the preview independent of how the file is chosen (click or drag), support compressed GLBs, and show an alert when
a model cannot be previewed instead of failing silently. No server-side change: sizes are still computed in the browser.

### Changes
1. **`resources/views/backend/sculpture/form.blade.php`** (module script)
   - Imports `DRACOLoader` (decoder path `https://unpkg.com/three@0.161.0/examples/jsm/libs/draco/gltf/`) and
     `MeshoptDecoder` (`three/addons/libs/meshopt_decoder.module.js`); `GLTFLoad()` calls
     `loader.setDRACOLoader()` / `loader.setMeshoptDecoder()`. (Paths checked on unpkg for three@0.161.0.)
   - `OrbitControls` import fixed to `three/addons/controls/OrbitControls.js`.
   - Removed `eventFuntion()` and the per-frame re-attaching from `animate()`.
   - `addSculptureModelUploadListener()` now runs once and adds **capture-phase** `change` and `drop` listeners on the
     wrapper `#sculpture-model-upload` (outside the Livewire component, so they survive re-renders). Both call new
     `loadSculptureFile(file)`, which only previews `.glb` / `.gltf` files.
   - `loader.load()` has an error callback: logs to console and alerts "The 3D model could not be previewed…" with the reason.

### Not changed / follow-ups
- Not tested in a real browser yet. If it still fails, the new alert / console message shows the exact reason.
- Models with KTX2/Basis textures would still need a `KTX2Loader` (not added).
- The upload limit is still `max:20480` (20 MB) per file.

### How to test
1. `/backend/sculptures/create` → click **Sculpture Model** and pick a GLB → model appears, Length/Width/Height fill in.
2. Reload, **drag** a GLB onto the Sculpture Model drop zone → same result.
3. Try a Draco-compressed GLB → loads.
4. Pick a broken/non-3D file renamed to .glb → alert explains it could not be previewed.
5. Edit an existing sculpture → its model still loads on page open.
6. Uploading a JPG in **Thumbnail Image** must not affect the 3D preview.

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
