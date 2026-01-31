<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AnalyticsEvent extends Model
{
    use HasFactory;

    protected $table = 'analytics_events';

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'restaurant_id',
        'event_type',
        'event_data',
        'ai_agent_id',
        'conversation_id',
        'order_id',
        'reservation_id',
        'customer_identifier',
        'user_id',
        'user_agent',
        'ip_address',
        'country',
        'city',
        'device_type',
        'browser',
        'os',
        'value',
        'duration_seconds',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'event_data' => 'array',
        'metadata' => 'array',
        'value' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
            if (empty($model->created_at)) {
                $model->created_at = now();
            }
        });
    }

    /**
     * العلاقات
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function aiAgent()
    {
        return $this->belongsTo(AIAgent::class);
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes
     */
    public function scopeByType($query, $type)
    {
        return $query->where('event_type', $type);
    }

    public function scopeForRestaurant($query, $restaurantId)
    {
        return $query->where('restaurant_id', $restaurantId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('created_at', now()->year);
    }

    /**
     * الدوال المساعدة
     */
    public static function track($eventType, $restaurantId, $data = [])
    {
        return static::create([
            'event_type' => $eventType,
            'restaurant_id' => $restaurantId,
            'event_data' => $data,
        ]);
    }

    public static function trackConversation($restaurantId, $conversationId, $eventData = [])
    {
        return static::create([
            'event_type' => 'conversation_started',
            'restaurant_id' => $restaurantId,
            'conversation_id' => $conversationId,
            'event_data' => $eventData,
        ]);
    }

    public static function trackOrder($restaurantId, $orderId, $amount, $eventType = 'order_placed')
    {
        return static::create([
            'event_type' => $eventType,
            'restaurant_id' => $restaurantId,
            'order_id' => $orderId,
            'value' => $amount,
        ]);
    }
}
