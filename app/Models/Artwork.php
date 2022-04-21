<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Artwork extends Model
{

    public function url(): Attribute
    {
        return Attribute::make(
            get: fn () => asset("images/collection/{$this->name}"),
        );
    }
}
