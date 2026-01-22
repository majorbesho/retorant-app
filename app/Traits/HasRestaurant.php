<?php

namespace App\Traits;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasRestaurant
{
    /**
     * Boot the trait.
     */
    protected static function bootHasRestaurant(): void
    {
        static::creating(function ($model) {
            if (empty($model->restaurant_id) && auth()->check()) {
                $model->restaurant_id = auth()->user()->restaurant_id;
            }
        });

        static::addGlobalScope('restaurant', function (Builder $builder) {
            if (auth()->check() && !auth()->user()->isSuperAdmin()) {
                $builder->where('restaurant_id', auth()->user()->restaurant_id);
            }
        });
    }

    /**
     * Get the restaurant that owns the model.
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
