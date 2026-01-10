<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class RestaurantRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'restaurant_name',
        'cuisine_type',
        'message',
        'status',
        'admin_notes',
        'role',
        'user_id',
        'restaurant_id',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
