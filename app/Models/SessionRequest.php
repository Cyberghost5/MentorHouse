<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionRequest extends Model
{
    protected $fillable = [
        'mentee_id',
        'mentor_id',
        'status',
        'session_type',
        'message',
        'proposed_date',
        'fee_amount',
        'project_description',
    ];

    protected $casts = [
        'proposed_date' => 'datetime',
        'fee_amount'    => 'decimal:2',
    ];

    // ── Status constants ───────────────────────────────────────────────────
    const STATUS_PENDING   = 'pending';
    const STATUS_ACCEPTED  = 'accepted';
    const STATUS_DECLINED  = 'declined';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // ── Relationships ──────────────────────────────────────────────────────
    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function mentee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentee_id');
    }

    // ── Helpers ────────────────────────────────────────────────────────────
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function conversation(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Conversation::class);
    }

    public function review(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function payment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function requiresPayment(): bool
    {
        return $this->session_type === 'paid';
    }

    public function isPaidFor(): bool
    {
        return $this->payment !== null && $this->payment->isPaid();
    }

    public function statusLabel(): string
    {
        return ucfirst($this->status);
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'pending'   => 'bg-yellow-50 text-yellow-700',
            'accepted'  => 'bg-green-50 text-green-700',
            'declined'  => 'bg-red-50 text-red-700',
            'completed' => 'bg-blue-50 text-blue-700',
            'cancelled' => 'bg-gray-100 text-gray-500',
            default     => 'bg-gray-100 text-gray-500',
        };
    }
}
