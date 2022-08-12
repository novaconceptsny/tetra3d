<?php

namespace App\Traits;


use Illuminate\Support\Facades\Schema;
use Str;

trait Searchable
{
    public function scopeWhereAnyColumnLike($query, $term = '', $columns = array(), $relations = array())
    {
        $term = trim($term);

        $query->where(function ($query) use ($term, $columns) {

            $table = $this->getTable();
            // If columns are not provided at scope level and set at model level
            if (isset($this->searchable_columns) && !empty($this->searchable_columns) && empty($columns)) {
                $columns = $this->searchable_columns;
            }

            if (empty($columns)) {
                $columns = Schema::getColumnListing($table);
            }

            foreach ($columns as $column) {
                $query->orWhere("$table.$column", 'LIKE', '%' . $term . '%');
            }
        });
        if (is_array($relations) && !empty($relations)) {
            foreach ($relations as $relation) {
                $query->whereRelatedColumnsLike($relation, $term);
            }
        } else if ($relations) {
            $query->whereRelatedColumnsLike($relations, $term);
        }
        return $query;
    }

    public function scopeWhereRelatedColumnsLike($query, $relation, $term = '', $columns = array())
    {
        $term = trim($term);

        $model_str = 'App\\Models\\' . ucfirst(Str::singular($relation));
        $model = new $model_str();

        // If columns are not provided at scope level and set at model level
        if (isset($model->searchable_columns) && !empty($model->searchable_columns) && empty($columns)) {
            $columns = $model->searchable_columns;
        }

        if (empty($columns)) {
            $columns = Schema::getColumnListing($model->getTable());
        }

        $query->orwhere(function ($query) use ($term, $columns, $relation) {
            $query->orWhereHas($relation, function ($q) use ($columns, $term) {
                $q->where(function ($q) use ($columns, $term){
                    foreach ($columns as $column) {
                        $q->orWhere($column, 'LIKE', '%'.$term.'%');
                    }
                });
            });
        });

        return $query;
    }
}
