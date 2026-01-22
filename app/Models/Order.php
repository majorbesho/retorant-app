<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\HasRestaurant;
use App\Traits\Auditable;

class Order extends Model
{
    use HasFactory, HasRestaurant, SoftDeletes, Auditable;

    protected $fillable = [
        'order_number',
        'restaurant_id',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'order_type',
        'source',
        'delivery_address',
        'preferred_delivery_time',
        'special_instructions',
        'subtotal',
        'delivery_fee',
        'service_charge',
        'tax_amount',
        'discount_amount',
        'discount_code',
        'total_amount',
        'paid_amount',
        'payment_method',
        'payment_status',
        'payment_reference',
        'payment_details',
        'status',
        'confirmed_at',
        'preparing_at',
        'ready_at',
        'delivered_at',
        'cancelled_at',
        'delivery_driver_id',
        'delivery_notes',
        'delivery_distance',
        'estimated_delivery_time',
        'rating',
        'review',
        'reviewed_at',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'customer_address' => 'array',
        'delivery_address' => 'array',
        'payment_details' => 'array',
        'metadata' => 'array',
        'preferred_delivery_time' => 'datetime',
        'confirmed_at' => 'datetime',
        'preparing_at' => 'datetime',
        'ready_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'delivery_distance' => 'decimal:2',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deliveryDriver()
    {
        return $this->belongsTo(User::class, 'delivery_driver_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
