<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class AIAgent extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'ai_agents';

    protected $fillable = [
        'restaurant_id',
        'name',
        'channel_type',
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
        // Prompt Settings
        'system_prompt',
        'custom_instructions',
        'personality',
        'response_tone',
        'max_response_length',
        'enable_context_learning',
        'enable_sentiment_analysis',
        'enable_intent_detection',
        'enable_auto_escalation',
        'escalation_keywords',
        'language_detection',
        'enable_translation',
        'escalation_message',
        'error_message',
        'voice_language',
        'voice_gender',
        'speaking_rate',
        'pitch',
        'enable_outside_hours_message',
        'outside_hours_message',
        'auto_reply_enabled',
        'auto_reply_message',
        'rate_limit_per_minute',
        'rate_limit_per_hour',
        'webhook_url',
        'webhook_secret',
        'enable_webhook_logs',
        'total_conversations',
        'average_response_time',
        'resolution_rate',
        'escalation_rate',
        'customer_satisfaction_score',
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
        'temperature' => 'decimal:2',
        'last_active_at' => 'datetime',
        // Prompt Settings
        'custom_instructions' => 'array',
        'escalation_keywords' => 'array',
        'escalation_message' => 'array',
        'error_message' => 'array',
        'outside_hours_message' => 'array',
        'auto_reply_message' => 'array',
        'enable_context_learning' => 'boolean',
        'enable_sentiment_analysis' => 'boolean',
        'enable_intent_detection' => 'boolean',
        'enable_auto_escalation' => 'boolean',
        'enable_translation' => 'boolean',
        'enable_outside_hours_message' => 'boolean',
        'auto_reply_enabled' => 'boolean',
        'enable_webhook_logs' => 'boolean',
        'speaking_rate' => 'decimal:2',
        'pitch' => 'decimal:2',
        'average_response_time' => 'decimal:2',
        'resolution_rate' => 'decimal:2',
        'escalation_rate' => 'decimal:2',
        'customer_satisfaction_score' => 'decimal:2',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'ai_agent_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByChannel($query, $channel)
    {
        return $query->where('channel_type', $channel);
    }

    public function scopeTopPerformers($query, $limit = 10)
    {
        return $query->where('status', 'active')
                     ->orderBy('resolution_rate', 'desc')
                     ->orderBy('customer_satisfaction_score', 'desc')
                     ->limit($limit);
    }

    /**
     * الدوال المساعدة
     */
    public function isOnline()
    {
        return $this->status === 'active' && $this->working_hours;
    }

    public function canAcceptRequests()
    {
        if ($this->status !== 'active') {
            return false;
        }

        // Check working hours if enabled
        if ($this->working_hours) {
            $today = strtolower(now()->format('l'));
            $schedule = $this->working_hours[$today] ?? null;
            if (!$schedule) {
                return false;
            }

            $now = now()->format('H:i');
            return $now >= $schedule['open'] && $now <= $schedule['close'];
        }

        return true;
    }

    public function getHealthScore()
    {
        $score = 0;
        $score += ($this->resolution_rate ?? 0) * 0.4;
        $score += ($this->customer_satisfaction_score ?? 0) * 0.3;
        $score += min((($this->total_conversations ?? 0) / 1000) * 100, 100) * 0.2;
        $score += (100 - ($this->escalation_rate ?? 0)) * 0.1;

        return round(min($score, 100), 2);
    }

    public function recordInteraction($tokens = 0)
    {
        $this->update([
            'total_calls' => $this->total_calls + 1,
            'total_tokens_used' => $this->total_tokens_used + $tokens,
            'last_active_at' => now(),
        ]);
    }

    public function updatePerformanceMetrics($data = [])
    {
        $currentStats = $this->conversation_stats ?? [];
        $currentStats = array_merge($currentStats, $data);

        $this->update([
            'conversation_stats' => $currentStats,
            'last_active_at' => now(),
        ]);
    }
}
