<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'restaurant_id',
        'user_id',
        'order_id',
        'reviewer_name',
        'reviewer_email',
        'reviewer_phone',
        'rating',
        'aspect_ratings',
        'comment',
        'comment_translations',
        'images',
        'videos',
        'is_verified',
        'is_featured',
        'is_helpful',
        'helpful_count',
        'report_count',
        'owner_response',
        'responded_at',
        'is_approved',
        'is_public',
    ];

    protected $casts = [
        'aspect_ratings' => 'array',
        'comment_translations' => 'array',
        'images' => 'array',
        'videos' => 'array',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'is_helpful' => 'boolean',
        'is_approved' => 'boolean',
        'is_public' => 'boolean',
        'responded_at' => 'datetime',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
