<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'session_request_id',
        'gateway',
        'gateway_reference',
        'amount',
        'currency',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function sessionRequest(): BelongsTo
    {
        return $this->belongsTo(SessionRequest::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
