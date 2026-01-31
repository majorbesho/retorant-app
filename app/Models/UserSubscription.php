<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class UserSubscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'user_subscriptions';

    protected $fillable = [
        'uuid',
        'user_id',
        'subscription_plan_id',
        'stripe_subscription_id',
        'stripe_customer_id',
        'billing_cycle',
        'current_price',
        'status',
        'started_at',
        'current_period_start',
        'current_period_end',
        'trial_ends_at',
        'canceled_at',
        'cancel_reason',
        'next_billing_date',
        'auto_renew',
        'payment_method_id',
        'active_features',
        'usage_stats',
        'invoice_count',
        'failed_payment_count',
        'notes',
        'credit_balance',
        'credit_balance_notes',
    ];

    protected $casts = [
        'active_features' => 'array',
        'usage_stats' => 'array',
        'started_at' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'trial_ends_at' => 'datetime',
        'canceled_at' => 'datetime',
        'next_billing_date' => 'datetime',
        'auto_renew' => 'boolean',
        'current_price' => 'decimal:2',
        'credit_balance' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }

    /**
     * العلاقات
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function restaurants()
    {
        return $this->hasMany(Restaurant::class, 'subscription_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOnTrial($query)
    {
        return $query->where('status', 'trial');
    }

    public function scopeExpired($query)
    {
        return $query->where('current_period_end', '<', now());
    }

    public function scopeExpiringSoon($query)
    {
        return $query->whereBetween('current_period_end', [now(), now()->addDays(7)]);
    }

    /**
     * Accessors & Mutators
     */
    public function getIsActiveAttribute()
    {
        return $this->status === 'active' && $this->current_period_end >= now();
    }

    public function getIsExpiredAttribute()
    {
        return $this->current_period_end < now();
    }

    public function getIsOnTrialAttribute()
    {
        return $this->status === 'trial' && $this->trial_ends_at >= now();
    }

    /**
     * الدوال المساعدة
     */
    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'canceled',
            'cancel_reason' => $reason,
            'canceled_at' => now(),
            'auto_renew' => false,
        ]);
        return $this;
    }

    public function pause()
    {
        $this->update(['status' => 'paused']);
        return $this;
    }

    public function resume()
    {
        $this->update(['status' => 'active']);
        return $this;
    }

    public function incrementUsage($feature, $amount = 1)
    {
        $stats = $this->usage_stats ?? [];
        $stats[$feature] = ($stats[$feature] ?? 0) + $amount;
        $this->update(['usage_stats' => $stats]);
        return $this;
    }

    public function getUsagePercentage($feature)
    {
        $limit = $this->subscriptionPlan->limits[$feature] ?? 0;
        $current = $this->usage_stats[$feature] ?? 0;
        if ($limit === 0) return 0;
        return ($current / $limit) * 100;
    }

    public function isFeatureAvailable($feature)
    {
        $features = $this->subscriptionPlan->features ?? [];
        return isset($features[$feature]) && $features[$feature] === true;
    }
}
