<?php

namespace App\Traits;


use Illuminate\Support\Facades\Schema;
use Str;

trait Sortable
{
    public function scopeSort($query, $orderBy = 'id', $order = 'asc'){
        if (Schema::hasColumn($this->getTable(), $orderBy)) {
            $query->orderBy($orderBy, $order);
        } else{
            $query->orderBy('id', 'desc');
        }
        return $query;
    }
}
