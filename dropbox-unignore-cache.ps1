# dropbox-unignore-cache.ps1
# -------------------------------------------------------------------
# REVERTS dropbox-ignore-cache.ps1: removes the Dropbox "ignore" flag
# from Laravel's cache/storage folders so Dropbox syncs them again.
#
# Run in PowerShell on Windows, on EACH machine where you ran the
# ignore script. The folders will sync back up to Dropbox afterwards.
# -------------------------------------------------------------------

$proj = $PSScriptRoot

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
    if (Test-Path $path) {
        Clear-Content -Path $path -Stream com.dropbox.ignored -ErrorAction SilentlyContinue
        Write-Host "  un-ignored  $rel"
    } else {
        Write-Host "  missing     $rel (skipped)"
    }
}

Write-Host "`nDone. These folders will sync via Dropbox again on this machine."
Write-Host "Run this on every machine where you applied the ignore."
