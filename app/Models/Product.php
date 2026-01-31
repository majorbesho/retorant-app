<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\{HasRestaurant, HasTranslations, Auditable};

class Product extends Model
{
    use HasFactory, HasRestaurant, HasTranslations, SoftDeletes, Auditable;

    protected $touches = ['category'];

    protected $fillable = [
        'restaurant_id',
        'category_id',
        'sku',
        'name',
        'name_translations',
        'description',
        'description_translations',
        'ingredients',
        'ingredients_translations',
        'allergens',
        'nutritional_info',
        'price',
        'discount_price',
        'cost_price',
        'discount_type',
        'discount_value',
        'discount_start',
        'discount_end',
        'stock_quantity',
        'stock_status',
        'low_stock_threshold',
        'images',
        'video_url',
        'is_featured',
        'is_recommended',
        'is_popular',
        'is_vegetarian',
        'is_vegan',
        'is_gluten_free',
        'is_halal',
        'has_spices',
        'spice_level',
        'has_variations',
        'has_addons',
        'customization_options',
        'sales_count',
        'view_count',
        'rating',
        'is_active',
        'is_available',
        'available_from',
        'available_to',
        'availability_schedule',
    ];

    protected $casts = [
        'name_translations' => 'array',
        'description_translations' => 'array',
        'ingredients_translations' => 'array',
        'allergens' => 'array',
        'nutritional_info' => 'array',
        'images' => 'array',
        'customization_options' => 'array',
        'availability_schedule' => 'array',
        'discount_start' => 'datetime',
        'discount_end' => 'datetime',
        'available_from' => 'datetime',
        'available_to' => 'datetime',
        'is_featured' => 'boolean',
        'is_recommended' => 'boolean',
        'is_popular' => 'boolean',
        'is_vegetarian' => 'boolean',
        'is_vegan' => 'boolean',
        'is_gluten_free' => 'boolean',
        'is_halal' => 'boolean',
        'has_spices' => 'boolean',
        'has_variations' => 'boolean',
        'has_addons' => 'boolean',
        'is_active' => 'boolean',
        'is_available' => 'boolean',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'rating' => 'decimal:2',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variations()
    {
        return $this->hasMany(Variation::class);
    }

    public function addonGroups()
    {
        return $this->belongsToMany(AddonGroup::class, 'product_addons')
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
