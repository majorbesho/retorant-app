<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AIAgent extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ai_agents';

    protected $fillable = [
        'restaurant_id',
        'name',
        'type',
        'status',
        'settings',
        'greeting_message',
        'fallback_message',
        'working_hours',
        'ai_provider',
        'ai_model',
        'ai_config',
        'temperature',
        'voice_provider',
        'voice_id',
        'voice_settings',
        'total_tokens_used',
        'total_calls',
        'last_active_at',
        'conversation_stats',
        'performance_metrics',
    ];

    protected $casts = [
        'settings' => 'array',
        'greeting_message' => 'array',
        'fallback_message' => 'array',
        'working_hours' => 'array',
        'ai_config' => 'array',
        'voice_settings' => 'array',
        'conversation_stats' => 'array',
        'performance_metrics' => 'array',
        'last_active_at' => 'datetime',
        'temperature' => 'decimal:2',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'ai_agent_id');
    }
}
