<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class StaffMember extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'staff_members';

    protected $fillable = [
        'uuid',
        'user_id',
        'restaurant_id',
        'role',
        'phone',
        'address',
        'birth_date',
        'gender',
        'join_date',
        'end_date',
        'is_active',
        'total_orders_handled',
        'total_conversations_handled',
        'average_rating',
        'last_active_at',
        'permissions',
        'allowed_channels',
        'national_id',
        'bank_account',
        'metadata',
    ];

    protected $casts = [
        'join_date' => 'date',
        'end_date' => 'date',
        'birth_date' => 'date',
        'is_active' => 'boolean',
        'last_active_at' => 'datetime',
        'permissions' => 'array',
        'allowed_channels' => 'array',
        'metadata' => 'array',
        'average_rating' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
            if (empty($model->join_date)) {
                $model->join_date = now()->toDateString();
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
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeTopPerformers($query, $limit = 10)
    {
        return $query->where('is_active', true)
                     ->orderBy('average_rating', 'desc')
                     ->limit($limit);
    }

    /**
     * الدوال المساعدة
     */
    public function hasPermission($permission)
    {
        $permissions = $this->permissions ?? [];
        return in_array($permission, $permissions);
    }

    public function canHandleChannel($channel)
    {
        if (empty($this->allowed_channels)) {
            return true; // إذا لم يتم تحديد قنوات، يمكنه التعامل مع الجميع
        }
        return in_array($channel, $this->allowed_channels);
    }

    public function updatePerformanceMetrics()
    {
        // محسوبة من قاعدة البيانات
        $this->update([
            'last_active_at' => now(),
        ]);
    }
}
