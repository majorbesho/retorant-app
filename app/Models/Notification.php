<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasTranslations;
use Illuminate\Support\Str;

class Notification extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $table = 'notifications';

    protected $fillable = [
        'uuid',
        'user_id',
        'restaurant_id',
        'type',
        'title',
        'title_translations',
        'message',
        'message_translations',
        'action_url',
        'action_type',
        'data',
        'is_read',
        'read_at',
        'send_email',
        'send_push',
        'send_sms',
        'email_sent_at',
        'push_sent_at',
        'priority',
        'click_count',
        'last_clicked_at',
    ];

    protected $casts = [
        'title_translations' => 'array',
        'message_translations' => 'array',
        'data' => 'array',
        'is_read' => 'boolean',
        'send_email' => 'boolean',
        'send_push' => 'boolean',
        'send_sms' => 'boolean',
        'read_at' => 'datetime',
        'email_sent_at' => 'datetime',
        'push_sent_at' => 'datetime',
        'last_clicked_at' => 'datetime',
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
     * العلاقات
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Scopes
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high')->orWhere('priority', 'urgent');
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days))->orderBy('created_at', 'desc');
    }

    /**
     * الدوال المساعدة
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
        return $this;
    }

    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
        return $this;
    }

    public function click()
    {
        $this->update([
            'click_count' => $this->click_count + 1,
            'last_clicked_at' => now(),
        ]);
        $this->markAsRead();
        return $this;
    }

    /**
     * الإرسال
     */
    public function send()
    {
        if ($this->send_email) {
            $this->sendEmail();
        }
        if ($this->send_push) {
            $this->sendPush();
        }
        if ($this->send_sms) {
            $this->sendSMS();
        }
        return $this;
    }

    protected function sendEmail()
    {
        // تنفيذ إرسال البريد الإلكتروني
        $this->update(['email_sent_at' => now()]);
    }

    protected function sendPush()
    {
        // تنفيذ إرسال إشعار Push
        $this->update(['push_sent_at' => now()]);
    }

    protected function sendSMS()
    {
        // تنفيذ إرسال SMS
    }

    /**
     * Accessors
     */
    public function getTitleAttribute($value)
    {
        return $this->getTranslation('title', app()->getLocale()) ?? $value;
    }

    public function getMessageAttribute($value)
    {
        return $this->getTranslation('message', app()->getLocale()) ?? $value;
    }
}
