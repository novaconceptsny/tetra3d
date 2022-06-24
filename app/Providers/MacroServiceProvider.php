<?php

namespace App\Providers;

use Arr;
use Illuminate\Support\ServiceProvider;

class MacroServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Arr::macro('keyByAndForget', function (&$array, $key) {
            $array = Arr::keyBy($array, $key);
            foreach ($array as &$item){
                if (isset($item[$key])){
                    unset($item[$key]);
                }
            }
            return $array;
        });
    }
}
