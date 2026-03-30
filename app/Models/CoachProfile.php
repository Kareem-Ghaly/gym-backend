<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoachProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'specialization',
        'experience_years',
        'certification',
        'rating',
        'total_clients',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
    ];

    /**
     * Get the user that owns the coach profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


