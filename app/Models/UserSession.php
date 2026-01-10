<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    protected $fillable = [
        'user_id',
        'device_type',
        'platform',
        'browser',
        'ip_address',
        'user_agent',
        'location',
        'is_current',
        'last_activity',
    ];

    protected $casts = [
        'last_activity' => 'datetime',
        'is_current' => 'boolean',
        'location' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeActive($query, $minutes = 30)
    {
        return $query->where('last_activity', '>=', now()->subMinutes($minutes));
    }
}
