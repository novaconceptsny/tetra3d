# dropbox-ignore-cache.ps1
# -------------------------------------------------------------------
# Stops Laravel's machine-specific generated folders from syncing via
# Dropbox, so a cached config / compiled view from one PC can never
# break another PC (e.g. absolute E:\ paths landing on a C:\ machine).
#
# RUN THIS ONCE ON EACH MACHINE that has the project in Dropbox.
# It is safe to re-run. It must be run in PowerShell on Windows.
#
# What "ignored" means in Dropbox: the folder stays on THIS computer
# but is removed from dropbox.com and your OTHER devices. That's why
# you must run it on every machine — each keeps its own local copy.
# -------------------------------------------------------------------

# Project root = the folder this script lives in (works on any machine).
$proj = $PSScriptRoot

# Volatile, per-machine folders that should never sync.
$targets = @(
    "bootstrap\cache",
    "storage\framework\cache",
    "storage\framework\sessions",
    "storage\framework\views",
    "storage\logs"
)

Write-Host "Project: $proj`n"

foreach ($rel in $targets) {
    $path = Join-Path $proj $rel

    # Recreate the folder if Dropbox already removed it on this machine.
    if (-not (Test-Path $path)) {
        New-Item -ItemType Directory -Path $path -Force | Out-Null
        # Keep the empty folder under git so Laravel always has it.
        Set-Content -Path (Join-Path $path ".gitignore") -Value "*`n!.gitignore"
        Write-Host "  recreated  $rel"
    }

    # Mark it ignored by Dropbox (local NTFS alternate data stream).
    Set-Content -Path $path -Stream com.dropbox.ignored -Value 1
    Write-Host "  ignored    $rel"
}

Write-Host "`nDone. These folders are now Dropbox-ignored on this machine."
Write-Host "Reminder: run this same script once on every other machine too."
