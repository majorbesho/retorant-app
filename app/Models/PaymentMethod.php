<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PaymentMethod extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payment_methods';

    protected $fillable = [
        'uuid',
        'user_id',
        'stripe_payment_method_id',
        'type',
        'brand',
        'last_four',
        'expiry_month',
        'expiry_year',
        'cardholder_name',
        'is_default',
        'is_active',
        'verified_at',
        'country',
        'postal_code',
        'metadata',
    ];

    protected $hidden = [
        'stripe_payment_method_id',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'verified_at' => 'datetime',
        'metadata' => 'array',
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

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class, 'payment_method_id');
    }

    /**
     * Scopes
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    /**
     * Accessors
     */
    public function getMaskedCardAttribute()
    {
        return '**** **** **** ' . $this->last_four;
    }

    public function getExpiryStringAttribute()
    {
        return sprintf('%02d/%02d', $this->expiry_month, $this->expiry_year);
    }

    /**
     * الدوال المساعدة
     */
    public function verify()
    {
        $this->update(['verified_at' => now()]);
        return $this;
    }

    public function setAsDefault()
    {
        // إزالة الافتراضي من جميع الطرق الأخرى
        $this->user->paymentMethods()->update(['is_default' => false]);
        $this->update(['is_default' => true]);
        return $this;
    }

    public function deactivate()
    {
        $this->update(['is_active' => false, 'is_default' => false]);
        return $this;
    }
}
