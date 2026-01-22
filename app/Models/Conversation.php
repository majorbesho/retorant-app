<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasRestaurant;

class Conversation extends Model
{
    use HasFactory, HasRestaurant;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'ai_agent_id',
        'restaurant_id',
        'customer_identifier',
        'customer_name',
        'customer_phone',
        'customer_phone_number',
        'customer_email',
        'channel',
        'session_id',
        'status',
        'intent',
        'messages',
        'message_text',
        'response_text',
        'message_direction',
        'sentiment',
        'escalation_status',
        'context',
        'entities',
        'message_count',
        'token_count',
        'started_at',
        'ended_at',
        'last_message_at',
        'outcome',
        'was_successful',
        'customer_satisfaction',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'messages' => 'array',
        'context' => 'array',
        'entities' => 'array',
        'outcome' => 'array',
        'metadata' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'last_message_at' => 'datetime',
        'was_successful' => 'boolean',
    ];

    public function agent()
    {
        return $this->belongsTo(AIAgent::class, 'ai_agent_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
