<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'name_translations',
        'values',
        'is_required',
        'min_selections',
        'max_selections',
        'sort_order',
    ];

    protected $casts = [
        'name_translations' => 'array',
        'values' => 'array',
        'is_required' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function options()
    {
        return $this->hasMany(VariationOption::class);
    }
}
