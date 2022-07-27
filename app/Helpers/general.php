<?php

use Carbon\Carbon;

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
