<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'expertise',
        'availability',
        'session_type',
        'hourly_rate',
        'years_of_experience',
    ];

    protected $casts = [
        'expertise'   => 'array',
        'hourly_rate' => 'decimal:2',
    ];

    protected $attributes = [
        'expertise' => null,
    ];

    public function getExpertiseAttribute(mixed $value): array
    {
        if (is_null($value)) {
            return [];
        }
        return is_array($value) ? $value : json_decode($value, true) ?? [];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isOpen(): bool
    {
        return $this->availability === 'open';
    }

    public function sessionTypeLabel(): string
    {
        return match ($this->session_type) {
            'free'           => 'Free',
            'paid'           => 'Paid',
            'project_based'  => 'Project-based',
            default          => ucfirst($this->session_type),
        };
    }
}
