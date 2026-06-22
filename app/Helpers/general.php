<?php

use Carbon\Carbon;
use \Intervention\Image\Facades\Image;
function carbon($time)
{
    return new Carbon($time);
}


function user($guard=null)
{
    return auth($guard)->user();
}


function dotToHtmlArray($string = ''){
    $array = explode('.', $string);

    if (count($array) < 2) {
        return $string;
    }

    $name = array_shift($array);

    foreach ($array as $item){
        $name .= "[$item]";
    }

    return $name;
}

function htmlArrayToDot($arrayString = ''){
    if (!str($arrayString)->contains('[')){
        return $arrayString;
    }

    return str($arrayString)->replace(['[', ']'], ['.', '']);
}

function str_to_title($string): string
{
    return str($string)
        ->title()
        ->replace(['_', '.'], ' ')
        ->value();
}

function resizeBase64Image($base64, $scale)
{
    $scale = $scale ?? 1;

    $image = Image::make($base64);
    $image->resize(
        $scale * $image->width(),
        $scale * $image->height()
    );
    return $image->encode('data-url');
}

/**
 * Cache-busting version token for the canvas ES module chain
 * (canvas.js -> CanvasManager.js -> ArtSelection.js / CanvasApi.js).
 *
 * Returns the newest filemtime across those files so the browser refetches
 * the modules after any change instead of running a stale cached copy.
 * Used by editor.blade.php to append "?v=<token>" to the module loads.
 */
function canvas_asset_version(): int
{
    $version = 0;

    foreach (['canvas.js', 'CanvasManager.js', 'ArtSelection.js', 'CanvasApi.js'] as $file) {
        $path = public_path('canvas/' . $file);

        if (is_file($path)) {
            $version = max($version, filemtime($path));
        }
    }

    return $version;
}

/**
 * Format company name with ID for "My Workspace" companies
 * 
 * @param string $companyName
 * @param int $companyId
 * @param int $padLength Length to pad the ID (default: 2)
 * @return string
 */
function formatCompanyName($companyName, $companyId, $padLength = 2)
{
    if ($companyName === 'My Workspace') {
        return $companyName . '_' . str_pad($companyId, $padLength, '0', STR_PAD_LEFT);
    }
    
    return $companyName;
}