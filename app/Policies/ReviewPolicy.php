<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\SessionRequest;
use App\Models\User;

class ReviewPolicy
{
    /**
     * A review can only be created if:
     * - the session is completed
     * - the authenticated user is a party to the session
     * - the user has not already reviewed this session
     */
    public function create(User $user, SessionRequest $sessionRequest): bool
    {
        // Must be completed
        if (! $sessionRequest->isCompleted()) {
            return false;
        }

        // Must be a party
        $isParty = $user->id === $sessionRequest->mentor_id
            || $user->id === $sessionRequest->mentee_id;
        if (! $isParty) {
            return false;
        }

        // Must not have already reviewed
        $alreadyReviewed = Review::where('session_request_id', $sessionRequest->id)
            ->where('reviewer_id', $user->id)
            ->exists();

        return ! $alreadyReviewed;
    }
}
