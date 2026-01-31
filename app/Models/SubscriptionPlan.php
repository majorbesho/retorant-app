<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasTranslations;

class SubscriptionPlan extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $table = 'subscription_plans';

    protected $fillable = [
        'name',
        'name_translations',
        'slug',
        'description',
        'description_translations',
        'monthly_price',
        'yearly_price',
        'setup_fee',
        'billing_cycle',
        'trial_days',
        'status',
        'features',
        'limits',
        'stripe_price_id_monthly',
        'stripe_price_id_yearly',
        'stripe_product_id',
        'is_recommended',
        'sort_order',
        'included_features',
    ];

    protected $casts = [
        'name_translations' => 'array',
        'description_translations' => 'array',
        'features' => 'array',
        'limits' => 'array',
        'included_features' => 'array',
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'setup_fee' => 'decimal:2',
        'is_recommended' => 'boolean',
    ];

    /**
     * العلاقات
     */
    public function subscriptions()
    {
        return $this->hasMany(\Laravel\Cashier\Subscription::class, 'stripe_price', 'stripe_price_id_monthly');
    }

    public function restaurants()
    {
        return $this->hasManyThrough(
            Restaurant::class,
            UserSubscription::class,
            'subscription_plan_id',
            'subscription_id',
            'id',
            'id'
        );
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeRecommended($query)
    {
        return $query->where('is_recommended', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Accessors & Mutators
     */
    public function getName()
    {
        return $this->getTranslation('name', app()->getLocale());
    }

    public function getDescription()
    {
        return $this->getTranslation('description', app()->getLocale());
    }
}
