<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    const ROLE_MENTOR = 'mentor';
    const ROLE_MENTEE = 'mentee';
    const ROLE_ADMIN  = 'admin';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'bio',
        'profile_photo',
        'headline',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isMentor(): bool
    {
        return $this->role === self::ROLE_MENTOR;
    }

    public function isMentee(): bool
    {
        return $this->role === self::ROLE_MENTEE;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function mentorProfile(): HasOne
    {
        return $this->hasOne(MentorProfile::class);
    }

    public function sentSessionRequests(): HasMany
    {
        return $this->hasMany(SessionRequest::class, 'mentee_id');
    }

    public function receivedSessionRequests(): HasMany
    {
        return $this->hasMany(SessionRequest::class, 'mentor_id');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'mentee_id')
            ->orWhere('mentor_id', $this->id);
    }

    public function reviewsGiven(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function reviewsReceived(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * Total earnings: sum of paid payments for accepted/completed sessions.
     */
    public function totalEarnings(): float
    {
        return (float) Payment::query()
            ->whereHas('sessionRequest', fn ($q) => $q->where('mentor_id', $this->id))
            ->where('status', 'paid')
            ->sum('amount');
    }

    public function getUsernameAttribute(): string
    {
        return Str::slug($this->name) . '-' . $this->id;
    }
}
