<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'coach_id',
        'vip_request_id',
        'type',
        'title',
        'description',
        'content',
        'daily_calories',
        'is_free',
    ];

    protected $casts = [
        'content' => 'array',
        'is_free' => 'boolean',
    ];

    /**
     * Get the user that owns the plan.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the coach that created the plan.
     */
    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    /**
     * Get the VIP request associated with the plan.
     */
    public function vipRequest()
    {
        return $this->belongsTo(VipRequest::class);
    }
}


