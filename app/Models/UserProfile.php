<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'age',
        'height',
        'weight',
        'goal',
        'activity_level',
        'allergies',
        'is_vip',
        'coach_id',
    ];

    protected $casts = [
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_vip' => 'boolean',
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the coach assigned to this user.
     */
    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }
}


