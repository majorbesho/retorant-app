<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'restaurant_id',
        'plan_id',
        'plan_name',
        'plan_features',
        'billing_cycle',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'cancelled_at',
        'amount',
        'currency',
        'status',
        'stripe_subscription_id',
        'stripe_customer_id',
        'usage_limits',
        'current_usage',
        'auto_renew',
        'upcoming_changes',
    ];

    protected $casts = [
        'plan_features' => 'array',
        'usage_limits' => 'array',
        'current_usage' => 'array',
        'upcoming_changes' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'auto_renew' => 'boolean',
        'amount' => 'decimal:2',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
