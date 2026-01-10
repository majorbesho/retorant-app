<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Cashier\Billable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Billable, HasFactory, HasRoles, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'name_translations',
        'email',
        'phone',
        'password',
        'profile_image',
        'locale',
        'timezone',
        'notification_preferences',
        'restaurant_id',
        'is_super_admin',
        'restaurant_access',
        'is_active',
        'status',
        'last_login_at',
        'last_login_ip',
        'phone_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'name_translations' => 'array',
        'notification_preferences' => 'array',
        'restaurant_access' => 'array',
        'is_super_admin' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user's name in the current locale.
     */
    public function getNameAttribute($value): string
    {
        $locale = app()->getLocale();
        $translations = $this->name_translations;

        if ($translations && isset($translations[$locale])) {
            return $translations[$locale];
        }

        // Fallback to English or the original name
        if ($translations && isset($translations['en'])) {
            return $translations['en'];
        }

        return $value;
    }

    /**
     * Set the user's name translations.
     */
    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = $value;

        // Auto-create translations if not provided
        if (empty($this->attributes['name_translations'])) {
            $this->attributes['name_translations'] = json_encode([
                'ar' => $value,
                'en' => $value
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Get the user's notification preferences with defaults.
     */
    public function getNotificationPreferencesAttribute($value): array
    {
        $preferences = $value ? json_decode($value, true) : [];

        return array_merge([
            'email' => true,
            'sms' => false,
            'whatsapp' => true,
            'push' => true,
            'order_updates' => true,
            'reservation_reminders' => true,
            'marketing' => false,
        ], $preferences);
    }

    /**
     * Check if user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin === true;
    }

    /**
     * Check if user is a restaurant owner.
     */
    public function isRestaurantOwner(): bool
    {
        return $this->hasRole('restaurant_owner') && $this->restaurant_id !== null;
    }

    /**
     * Check if user is a restaurant staff/assistant.
     */
    public function isRestaurantStaff(): bool
    {
        return $this->hasRole('restaurant_staff') && $this->restaurant_id !== null;
    }

    /**
     * Check if user can access a specific restaurant.
     */
    public function canAccessRestaurant(int $restaurantId): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->restaurant_id === $restaurantId) {
            return true;
        }

        $accessList = $this->restaurant_access ?? [];
        return in_array($restaurantId, $accessList);
    }

    /**
     * Get all restaurants this user can access.
     */
    public function accessibleRestaurants()
    {
        if ($this->isSuperAdmin()) {
            return Restaurant::query();
        }

        $restaurantIds = [$this->restaurant_id];

        if ($this->restaurant_access) {
            $restaurantIds = array_merge($restaurantIds, $this->restaurant_access);
        }

        return Restaurant::whereIn('id', array_filter(array_unique($restaurantIds)));
    }

    /**
     * Mark user's phone as verified.
     */
    public function markPhoneAsVerified(): bool
    {
        return $this->forceFill([
            'phone_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Check if user's phone is verified.
     */
    public function hasVerifiedPhone(): bool
    {
        return !is_null($this->phone_verified_at);
    }

    /**
     * Check if user should receive notification via specific channel.
     */
    public function shouldReceiveNotification(string $type, string $channel): bool
    {
        $preferences = $this->notification_preferences;

        // Check if channel is enabled
        if (!($preferences[$channel] ?? false)) {
            return false;
        }

        // Check specific notification type preference
        if (isset($preferences[$type])) {
            return $preferences[$type];
        }

        return true;
    }

    /**
     * Record login activity.
     */
    public function recordLogin(string $ip): void
    {
        $this->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ])->save();
    }

    /**
     * Generate avatar URL if no profile image.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->profile_image) {
            return Storage::url($this->profile_image);
        }

        // Generate initials avatar
        $name = Str::of($this->name)->trim();
        $initials = $name->contains(' ')
            ? $name->substr(0, 1) . $name->afterLast(' ')->substr(0, 1)
            : $name->substr(0, 2);

        return 'https://ui-avatars.com/api/?name=' . urlencode($initials->upper()) .
            '&color=FFFFFF&background=4F46E5&bold=true';
    }

    /**
     * Get user's active sessions.
     */
    public function activeSessions()
    {
        return $this->hasMany(UserSession::class)->where('is_current', true);
    }

    /**
     * Get user's all sessions.
     */
    public function sessions()
    {
        return $this->hasMany(UserSession::class);
    }

    /**
     * Get user's social accounts.
     */
    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    /**
     * Get user's restaurant.
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Get user's notifications.
     */
    public function notifications()
    {
        return $this->hasMany(UserNotification::class)->latest();
    }

    /**
     * Get user's unread notifications.
     */
    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }

    /**
     * Scope for active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    /**
     * Scope for restaurant owners.
     */
    public function scopeRestaurantOwners($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'restaurant_owner');
        });
    }

    /**
     * Scope for super admins.
     */
    public function scopeSuperAdmins($query)
    {
        return $query->where('is_super_admin', true);
    }

    /**
     * Scope for users with verified phone.
     */
    public function scopePhoneVerified($query)
    {
        return $query->whereNotNull('phone_verified_at');
    }

    /**
     * Scope for users by restaurant.
     */
    public function scopeByRestaurant($query, $restaurantId)
    {
        return $query->where(function ($q) use ($restaurantId) {
            $q->where('restaurant_id', $restaurantId)
                ->orWhereJsonContains('restaurant_access', $restaurantId);
        });
    }

    /**
     * Get dashboard URL based on user role.
     */
    public function getDashboardUrlAttribute(): string
    {
        if ($this->isSuperAdmin()) {
            return route('admin.dashboard');
        }

        if ($this->isRestaurantOwner() || $this->isRestaurantStaff()) {
            return route('restaurant.dashboard', $this->restaurant_id);
        }

        return route('home');
    }



    /**
     * Get user's reservations.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get user's orders.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get user's reviews.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
