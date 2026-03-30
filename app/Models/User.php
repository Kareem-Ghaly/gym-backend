<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_approved',
        'gender',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_approved' => 'boolean',
        ];
    }

    /**
     * Get the coach profile for the user.
     */
    public function coachProfile()
    {
        return $this->hasOne(CoachProfile::class);
    }

    /**
     * Get the user profile for the user.
     */
    public function userProfile()
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get the VIP requests sent by the user.
     */
    public function vipRequests()
    {
        return $this->hasMany(VipRequest::class, 'user_id');
    }

    /**
     * Get the VIP requests received by the coach.
     */
    public function receivedVipRequests()
    {
        return $this->hasMany(VipRequest::class, 'coach_id');
    }

    /**
     * Get the plans created by the coach.
     */
    public function createdPlans()
    {
        return $this->hasMany(Plan::class, 'coach_id');
    }

    /**
     * Get the plans assigned to the user.
     */
    public function assignedPlans()
    {
        return $this->hasMany(Plan::class, 'user_id');
    }

    /**
     * Get the posts created by the user.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the comments created by the user.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the likes created by the user.
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Get the users assigned to this coach.
     */
    public function clients()
    {
        return $this->hasMany(UserProfile::class, 'coach_id');
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is coach.
     */
    public function isCoach(): bool
    {
        return $this->hasRole('coach');
    }

    /**
     * Check if user is regular user.
     */
    public function isUser(): bool
    {
        return $this->hasRole('user');
    }
}
