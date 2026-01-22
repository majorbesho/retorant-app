<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\{HasRestaurant, HasTranslations};

class Addon extends Model
{
    use HasFactory, HasRestaurant, HasTranslations, SoftDeletes;

    protected $fillable = [
        'addon_group_id',
        'name',
        'name_translations',
        'description',
        'price',
        'stock_quantity',
        'is_active',
        'sort_order',
        'nutritional_info',
    ];

    protected $casts = [
        'name_translations' => 'array',
        'nutritional_info' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function group()
    {
        return $this->belongsTo(AddonGroup::class, 'addon_group_id');
    }
}
