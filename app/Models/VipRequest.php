<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VipRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'coach_id',
        'age',
        'height',
        'weight',
        'goal',
        'activity_level',
        'allergies',
        'status',
    ];

    protected $casts = [
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    /**
     * Get the user that made the request.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the coach assigned to the request.
     */
    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    /**
     * Get the plans created for this VIP request.
     */
    public function plans()
    {
        return $this->hasMany(Plan::class);
    }
}


