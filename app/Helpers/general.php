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
    return isset($array[1]) ? "{$array[0]}[{$array[1]}]" : $string;
}
