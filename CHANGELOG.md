# Tetra3D Change Log

Detailed record of every change made to this codebase (by people or AI).
Newest entries at the top. AI assistants: read this file before working on the code,
and add an entry for every change you make (see `CLAUDE.md` for the format).

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
Option A (agreed with Nova): removing a tour from a project **permanently deletes** that
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
