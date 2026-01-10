<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Restaurant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'name_translations',
        'slug',
        'description',
        'description_translations',
        'type',
        'cuisine_type',
        'cuisine_tags',
        'phone',
        'whatsapp_number',
        'email',
        'website',
        'address',
        'address_translations',
        'latitude',
        'longitude',
        'city',
        'area',
        'social_links',
        'features',
        'payment_methods',
        'logo',
        'gallery',
        'cover_image',
        'settings',
        'working_hours',
        'holidays',
        'preparation_time',
        'rating',
        'review_count',
        'order_count',
        'is_active',
        'is_verified',
        'status',
        'stripe_account_id',
        'subscription_details',
    ];

    protected $casts = [
        'name_translations' => 'array',
        'description_translations' => 'array',
        'cuisine_tags' => 'array',
        'address_translations' => 'array',
        'social_links' => 'array',
        'features' => 'array',
        'payment_methods' => 'array',
        'gallery' => 'array',
        'settings' => 'array',
        'working_hours' => 'array',
        'holidays' => 'array',
        'subscription_details' => 'array',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'rating' => 'decimal:2',
    ];

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function phones()
    {
        return $this->hasMany(\App\Models\RestaurantPhone::class);
    }
}
