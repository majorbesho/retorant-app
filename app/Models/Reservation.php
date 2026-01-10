<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reservation_number',
        'restaurant_id',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'number_of_guests',
        'reservation_date',
        'reservation_time',
        'shift',
        'duration',
        'table_number',
        'table_preferences',
        'status',
        'special_requests',
        'occasion',
        'is_confirmed',
        'confirmed_at',
        'reminder_sent',
        'deposit_amount',
        'deposit_status',
        'metadata',
        'source',
    ];

    protected $casts = [
        'reservation_date' => 'datetime',

        'table_preferences' => 'array',
        'metadata' => 'array',
        'is_confirmed' => 'boolean',
        'reminder_sent' => 'boolean',
        'confirmed_at' => 'datetime',
        'deposit_amount' => 'decimal:2',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
