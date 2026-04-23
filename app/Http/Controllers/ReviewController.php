<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Review;
use App\Models\SessionRequest;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    /**
     * Store a new review for a completed session.
     * Mentee reviews mentor; mentor optionally reviews mentee.
     */
    public function store(StoreReviewRequest $request, SessionRequest $sessionRequest): RedirectResponse
    {
        $this->authorize('create', [Review::class, $sessionRequest]);

        $user = $request->user();

        // Determine the reviewee (the other party)
        $revieweeId = $user->id === $sessionRequest->mentor_id
            ? $sessionRequest->mentee_id
            : $sessionRequest->mentor_id;

        Review::create([
            'session_request_id' => $sessionRequest->id,
            'reviewer_id'        => $user->id,
            'reviewee_id'        => $revieweeId,
            'rating'             => $request->validated('rating'),
            'comment'            => $request->validated('comment'),
        ]);

        return redirect()
            ->route('session-requests.index')
            ->with('status', 'Review submitted. Thank you!');
    }
}
