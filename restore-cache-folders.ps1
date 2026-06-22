# restore-cache-folders.ps1
# -------------------------------------------------------------------
# Recreates Laravel's cache/storage folders if they went missing
# (e.g. removed by a Dropbox ignore on another machine).
# Laravel regenerates the CONTENTS automatically; this just rebuilds
# the empty folder skeleton with the .gitignore keep-files.
#
# Run in PowerShell from the project root on the machine that is
# missing the folders.
# -------------------------------------------------------------------

$proj = $PSScriptRoot

# folder => keep-file contents
$dirs = @(
    "bootstrap\cache",
    "storage\framework\cache\data",
    "storage\framework\sessions",
    "storage\framework\views",
    "storage\logs"
)

foreach ($rel in $dirs) {
    $path = Join-Path $proj $rel
    New-Item -ItemType Directory -Path $path -Force | Out-Null
    $gi = Join-Path $path ".gitignore"
    if (-not (Test-Path $gi)) {
        "*`r`n!.gitignore" | Set-Content -Path $gi -Encoding ascii
    }
    Write-Host "  ready  $rel"
}

Write-Host "`nDone. Cache/storage skeleton restored."
Write-Host "Now reload the site (Laravel will rebuild compiled views/cache on its own)."
