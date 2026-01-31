<?php

namespace App\Models;

use App\Traits\HasRestaurant;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class FAQ extends Model
{
    use HasFactory, HasRestaurant, HasTranslations, SoftDeletes;

    protected $touches = ['restaurant'];

    protected $table = 'faqs';

    protected $fillable = [
        'uuid',
        'restaurant_id',
        'category',
        'question',
        'question_translations',
        'answer',
        'answer_translations',
        'keywords',
        'priority',
        'display_order',
        'is_active',
        'ai_context',
        'usage_count',
        'last_used_at',
        'helpful_count',
        'not_helpful_count',
        'helpfulness_score',
        'internal_notes',
        'related_faqs',
        'tags',
    ];

    protected $casts = [
        'question_translations' => 'array',
        'answer_translations' => 'array',
        'keywords' => 'array',
        'is_active' => 'boolean',
        'ai_context' => 'boolean',
        'last_used_at' => 'datetime',
        'related_faqs' => 'array',
        'tags' => 'array',
        'helpfulness_score' => 'decimal:2',
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
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForAI($query)
    {
        return $query->where('ai_context', true)->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeMostHelpful($query)
    {
        return $query->orderBy('helpfulness_score', 'desc')->orderBy('usage_count', 'desc');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('created_at', 'desc');
    }

    /**
     * الدوال المساعدة
     */
    public function incrementUsage()
    {
        $this->update([
            'usage_count' => $this->usage_count + 1,
            'last_used_at' => now(),
        ]);
        return $this;
    }

    public function markAsHelpful()
    {
        $this->update(['helpful_count' => $this->helpful_count + 1]);
        $this->updateHelpfulnessScore();
        return $this;
    }

    public function markAsNotHelpful()
    {
        $this->update(['not_helpful_count' => $this->not_helpful_count + 1]);
        $this->updateHelpfulnessScore();
        return $this;
    }

    protected function updateHelpfulnessScore()
    {
        $total = $this->helpful_count + $this->not_helpful_count;
        if ($total === 0) {
            $score = 0;
        } else {
            $score = ($this->helpful_count / $total) * 5;
        }
        $this->update(['helpfulness_score' => round($score, 2)]);
    }
}
