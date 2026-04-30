<?php

namespace App\Models;

use App\Models\Withdrawal;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
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

    /**
     * Total withdrawn (approved withdrawals only).
     */
    public function totalWithdrawn(): float
    {
        return (float) Withdrawal::where('mentor_id', $this->id)
            ->where('status', Withdrawal::STATUS_APPROVED)
            ->sum('amount');
    }

    /**
     * Pending withdrawal amount (not yet approved or rejected).
     */
    public function pendingWithdrawal(): float
    {
        return (float) Withdrawal::where('mentor_id', $this->id)
            ->where('status', Withdrawal::STATUS_PENDING)
            ->sum('amount');
    }

    /**
     * Available balance = earnings - approved withdrawals - pending withdrawals.
     */
    public function availableBalance(): float
    {
        return max(0, $this->totalEarnings() - $this->totalWithdrawn() - $this->pendingWithdrawal());
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class, 'mentor_id');
    }

    public function getUsernameAttribute(): string
    {
        return Str::slug($this->name) . '-' . $this->id;
    }
}
