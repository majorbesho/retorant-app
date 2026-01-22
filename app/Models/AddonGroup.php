<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\{HasRestaurant, HasTranslations};

class AddonGroup extends Model
{
    use HasFactory, HasRestaurant, HasTranslations, SoftDeletes;

    protected $fillable = [
        'restaurant_id',
        'name',
        'name_translations',
        'description',
        'is_required',
        'min_selections',
        'max_selections',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'name_translations' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function addons()
    {
        return $this->hasMany(Addon::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_addons')
            ->withPivot('sort_order')
            ->withTimestamps();
    }
}
