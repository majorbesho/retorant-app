<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\{HasRestaurant, HasTranslations};

class VariationOption extends Model
{
    use HasFactory, HasRestaurant, HasTranslations;

    protected $fillable = [
        'variation_id',
        'value',
        'value_translations',
        'price_adjustment',
        'stock_quantity',
        'is_default',
        'is_active',
        'image',
    ];

    protected $casts = [
        'value_translations' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'price_adjustment' => 'decimal:2',
    ];

    public function variation()
    {
        return $this->belongsTo(Variation::class);
    }
}
