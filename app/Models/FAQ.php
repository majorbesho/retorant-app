<?php

namespace App\Models;

use App\Traits\HasRestaurant;
use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FAQ extends Model
{
    use HasFactory, HasRestaurant, HasTranslations, SoftDeletes;

    protected $table = 'faqs';

    protected $fillable = [
        'restaurant_id',
        'question_translations',
        'answer_translations',
        'is_active',
    ];

    protected $casts = [
        'question_translations' => 'array',
        'answer_translations' => 'array',
        'is_active' => 'boolean',
    ];
}
