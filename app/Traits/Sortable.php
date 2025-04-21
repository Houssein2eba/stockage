<?php

namespace App\Traits;

trait Sortable
{
    protected function applySorting($query)
    {
        if (request()->has(['sort', 'direction'])) {
            $sortField = request('sort');
            $direction = request('direction');

            // Handle relationship sorting
            if (str_contains($sortField, '.')) {
                [$relation, $field] = explode('.', $sortField);
                $query->join($relation, $relation.'.id', '=', $this->getTable().'.'.$relation.'_id')
                      ->orderBy($relation.'.'.$field, $direction);
            } else {
                $query->orderBy($sortField, $direction);
            }
        }

        return $query;
    }
}