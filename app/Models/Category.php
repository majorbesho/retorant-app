<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\{HasRestaurant, HasTranslations, Auditable};

class Category extends Model
{
    use HasFactory, HasRestaurant, HasTranslations, SoftDeletes, Auditable;

    protected $fillable = [
        'restaurant_id',
        'menu_id',
        'parent_id',
        'name',
        'name_translations',
        'description',
        'description_translations',
        'image',
        'sort_order',
        'is_active',
        'tags',
    ];

    protected $casts = [
        'name_translations' => 'array',
        'description_translations' => 'array',
        'tags' => 'array',
        'is_active' => 'boolean',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
