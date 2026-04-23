<?php

namespace App\Policies;

use App\Models\SessionRequest;
use App\Models\User;

class SessionRequestPolicy
{
    /**
     * Only mentees can create session requests (and only to mentors).
     */
    public function create(User $user): bool
    {
        return $user->isMentee();
    }

    /**
     * Only the mentor receiving the request can accept or decline it.
     */
    public function update(User $user, SessionRequest $sessionRequest): bool
    {
        return $user->id === $sessionRequest->mentor_id
            && $sessionRequest->isPending();
    }

    /**
     * Both parties can view their own requests.
     */
    public function view(User $user, SessionRequest $sessionRequest): bool
    {
        return $user->id === $sessionRequest->mentor_id
            || $user->id === $sessionRequest->mentee_id;
    }

    /**
     * Only the mentee who sent the request can cancel it (while still pending).
     */
    public function cancel(User $user, SessionRequest $sessionRequest): bool
    {
        return $user->id === $sessionRequest->mentee_id
            && $sessionRequest->isPending();
    }
}
