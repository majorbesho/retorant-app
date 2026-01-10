<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_name_translations',
        'quantity',
        'unit_price',
        'total_price',
        'variations',
        'addons',
        'special_instructions',
        'product_data',
    ];

    protected $casts = [
        'product_name_translations' => 'array',
        'variations' => 'array',
        'addons' => 'array',
        'product_data' => 'array',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
