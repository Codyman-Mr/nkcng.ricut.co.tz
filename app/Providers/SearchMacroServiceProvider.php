<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;

class SearchMacroServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot()
    {
        Builder::macro('searchLike', function ($attributes, string $searchTerm) {
            /** @var \Illuminate\Database\Eloquent\Builder $this */
            $attributes = is_array($attributes) ? $attributes : [$attributes];
            $searchTerm = trim($searchTerm);

            if (empty($searchTerm)) {
                return $this;
            }

            return $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                foreach ($attributes as $attribute) {
                    if (str_contains($attribute, '.')) {
                        // Handle nested relationship (e.g., customerVehicle.id)
                        $parts = explode('.', $attribute);
                        $relation = implode('.', array_slice($parts, 0, -1)); // e.g., customerVehicle
                        $column = end($parts); // e.g., id

                        $query->orWhereHas($relation, function (Builder $relationQuery) use ($column, $searchTerm) {
                            $relationQuery->where($column, 'LIKE', "%{$searchTerm}%");
                        });
                    } else {
                        // Handle simple attribute (e.g., first_name)
                        $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                    }
                }
            });
        });
    }
}
