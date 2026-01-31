<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Cviebrock\EloquentSluggable\Sluggable;
use App\Traits\HasTranslations;

class Restaurant extends Model
{
    use HasFactory, HasTranslations, SoftDeletes, Sluggable;

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    protected static function booted()
    {
        static::saved(function ($restaurant) {
            // clear cache for both locales
            \Illuminate\Support\Facades\Cache::forget("restaurant_context_{$restaurant->slug}_en");
            \Illuminate\Support\Facades\Cache::forget("restaurant_context_{$restaurant->slug}_ar");
            \Illuminate\Support\Facades\Cache::forget("restaurant_info_{$restaurant->id}_en");
            \Illuminate\Support\Facades\Cache::forget("restaurant_info_{$restaurant->id}_ar");
        });
    }

    protected $fillable = [
        'uuid',
        // 'user_id', // Removed as it doesn't exist in table
        'subscription_id',
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
        'delivery_radius',
        'min_order_amount',
        'rating',
        'review_count',
        'order_count',
        'is_active',
        'is_verified',
        'status',
        'stripe_account_id',
        'subscription_details',
        'subscription_plan_name',
        'subscription_features',
        'subscription_limits',
        'current_conversations_count',
        'current_orders_count',
        'current_api_calls',
        'max_api_calls_monthly',
        'subscription_expires_at',
        'last_subscription_renewal_at',
        'subscription_status',
        // WhatsApp Evolution Instance
        'instance_name',
        'instance_token',
        'whatsapp_qr_code',
        'whatsapp_status',
        'whatsapp_connected_at',
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
        'subscription_features' => 'array',
        'subscription_limits' => 'array',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'rating' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'delivery_radius' => 'decimal:2',
        'subscription_expires_at' => 'datetime',
        'last_subscription_renewal_at' => 'datetime',
    ];

    public function owner()
    {
        // Assuming the owner is a user belonging to this restaurant with the 'restaurant_owner' role.
        // If Spatie/Permission is used, we can query by role.
        // For simplicity in this fix, we'll just get the first user associated (or we'd need to join roles).
        // Best approach if strict: 
        // return $this->hasOne(User::class)->whereHas('roles', function($q){ $q->where('name', 'restaurant_owner'); });
        // But for safe fallback if roles aren't set in factories heavily:
        return $this->hasOne(User::class)->oldest();
    }

    public function subscription()
    {
        // A restaurant's subscription is tied to its owner
        return $this->owner ? $this->owner->subscription('default') : null;
    }

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

    public function aiAgents()
    {
        return $this->hasMany(AIAgent::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function faqs()
    {
        return $this->hasMany(FAQ::class);
    }

    public function staffMembers()
    {
        return $this->hasMany(StaffMember::class);
    }

    public function analyticsEvents()
    {
        return $this->hasMany(AnalyticsEvent::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeWithActiveSubscription($query)
    {
        return $query->where('subscription_status', 'active');
    }

    /**
     * الدوال المساعدة
     */
    public function hasFeature($featureName)
    {
        $features = $this->subscription_features ?? [];
        return isset($features[$featureName]) && $features[$featureName] === true;
    }

    public function canUseFeature($featureName)
    {
        if (!$this->hasFeature($featureName)) {
            return false;
        }

        if ($this->subscription_status !== 'active') {
            return false;
        }

        if ($this->subscription_expires_at && $this->subscription_expires_at < now()) {
            return false;
        }

        return true;
    }

    public function getUsagePercentage($feature)
    {
        $limits = $this->subscription_limits ?? [];
        $currentValue = 0;

        switch ($feature) {
            case 'conversations':
                $currentValue = $this->current_conversations_count;
                break;
            case 'orders':
                $currentValue = $this->current_orders_count;
                break;
            case 'api_calls':
                $currentValue = $this->current_api_calls;
                break;
        }

        $limit = $limits[$feature] ?? 0;
        if ($limit === 0) return 0;

        return ($currentValue / $limit) * 100;
    }

    public function isSubscriptionExpired()
    {
        return $this->subscription_expires_at && $this->subscription_expires_at < now();
    }

    public function subscriptionExpiresIn()
    {
        if ($this->subscription_expires_at) {
            return now()->diffInDays($this->subscription_expires_at);
        }
        return null;
    }

    /**
     * Check if WhatsApp is connected and active
     */
    public function hasWhatsAppConnected(): bool
    {
        return $this->whatsapp_status === 'connected' && !empty($this->instance_name);
    }

    /**
     * Get WhatsApp Evolution service instance
     */
    public function getWhatsAppService(): \App\Services\WhatsAppEvolutionService
    {
        return app(\App\Services\WhatsAppEvolutionService::class);
    }

    /**
     * Send WhatsApp message (convenience method)
     */
    public function sendWhatsAppMessage(string $number, string $message): array
    {
        if (!$this->hasWhatsAppConnected()) {
            return [
                'success' => false,
                'error' => 'WhatsApp is not connected for this restaurant',
            ];
        }

        return $this->getWhatsAppService()->sendMessage(
            $this->instance_name,
            $number,
            $message
        );
    }

    /**
     * Get WhatsApp connection status badge color
     */
    public function getWhatsAppStatusBadge(): string
    {
        return match ($this->whatsapp_status) {
            'connected' => 'success',
            'pending' => 'warning',
            'disconnected' => 'secondary',
            'failed' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get WhatsApp connection status label
     */
    public function getWhatsAppStatusLabel(): string
    {
        return match ($this->whatsapp_status) {
            'connected' => 'متصل',
            'pending' => 'في انتظار المسح',
            'disconnected' => 'غير متصل',
            'failed' => 'فشل الاتصال',
            default => 'غير مفعّل',
        };
    }
}
