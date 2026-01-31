<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\{HasRestaurant, HasTranslations};

class Menu extends Model
{
    use HasFactory, HasRestaurant, HasTranslations, SoftDeletes;

    protected $touches = ['restaurant'];

    protected $fillable = [
        'restaurant_id',
        'name',
        'name_translations',
        'description',
        'description_translations',
        'sort_order',
        'is_active',
        'available_from',
        'available_to',
        'availability_rules',
    ];

    protected $casts = [
        'name_translations' => 'array',
        'description_translations' => 'array',
        'availability_rules' => 'array',
        'is_active' => 'boolean',
        'available_from' => 'datetime',
        'available_to' => 'datetime',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
