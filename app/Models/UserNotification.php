<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class UserNotification extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'type',
        'channel',
        'subject',
        'message',
        'data',
        'read_at',
        'sent_at',
        'delivery_status',
    ];

    protected $casts = [
        'data' => 'array',
        'delivery_status' => 'array',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead()
    {
        $this->forceFill(['read_at' => now()])->save();
    }

    public function markAsUnread()
    {
        $this->forceFill(['read_at' => null])->save();
    }

    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeByChannel($query, $channel)
    {
        return $query->where('channel', $channel);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}