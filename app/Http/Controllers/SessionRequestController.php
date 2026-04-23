<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSessionRequestRequest;
use App\Http\Requests\UpdateSessionRequestRequest;
use App\Models\Conversation;
use App\Models\Payment;
use App\Models\SessionRequest;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\NewSessionRequestNotification;
use App\Notifications\SessionRequestStatusChangedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SessionRequestController extends Controller
{
    /**
     * Show the authenticated user's request list.
     * Mentors see incoming requests; mentees see outgoing requests.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isMentor()) {
            $requests = SessionRequest::where('mentor_id', $user->id)
                ->with(['mentee', 'conversation', 'payment'])
                ->latest()
                ->paginate(15);

            return view('session-requests.mentor-index', compact('requests'));
        }

        $requests = SessionRequest::where('mentee_id', $user->id)
            ->with(['mentor', 'conversation', 'review', 'payment'])
            ->latest()
            ->paginate(15);

        return view('session-requests.mentee-index', compact('requests'));
    }

    /**
     * Mentee submits a session request to a mentor.
     */
    public function store(StoreSessionRequestRequest $request, User $mentor): RedirectResponse
    {
        $this->authorize('create', SessionRequest::class);

        $data = $request->validated();
        $data['mentee_id'] = $request->user()->id;
        $data['mentor_id'] = $mentor->id;
        $data['session_type'] = $mentor->mentorProfile->session_type;

        // Carry the fee from the mentor profile for paid sessions
        if ($data['session_type'] === 'paid') {
            $data['fee_amount'] = $mentor->mentorProfile->hourly_rate;
        }

        $sessionRequest = SessionRequest::create($data);

        $mentor->notify(new NewSessionRequestNotification($sessionRequest));

        return redirect()
            ->route('mentors.show', $mentor->username)
            ->with('status', 'Session request sent! The mentor will be in touch soon.');
    }

    /**
     * Mentor accepts or declines a request.
     */
    public function update(UpdateSessionRequestRequest $request, SessionRequest $sessionRequest): RedirectResponse
    {
        $sessionRequest->update(['status' => $request->validated('status')]);

        // Auto-create conversation when accepted
        if ($sessionRequest->isAccepted()) {
            Conversation::firstOrCreate([
                'session_request_id' => $sessionRequest->id,
            ], [
                'mentor_id' => $sessionRequest->mentor_id,
                'mentee_id' => $sessionRequest->mentee_id,
            ]);

            // For paid sessions, create a pending payment record
            if ($sessionRequest->requiresPayment() && ! $sessionRequest->payment) {
                Payment::create([
                    'session_request_id' => $sessionRequest->id,
                    'gateway'            => Setting::get('payment_gateway', 'paystack'),
                    'amount'             => $sessionRequest->fee_amount ?? 0,
                    'currency'           => 'NGN',
                    'status'             => 'pending',
                ]);
            }
        }

        $sessionRequest->mentee->notify(
            new SessionRequestStatusChangedNotification($sessionRequest)
        );

        return redirect()
            ->route('session-requests.index')
            ->with('status', 'Request ' . $sessionRequest->status . '.');
    }

    /**
     * Mentor marks an accepted session as completed.
     */
    public function complete(Request $request, SessionRequest $sessionRequest): RedirectResponse
    {
        abort_unless(
            $request->user()->id === $sessionRequest->mentor_id
                && $sessionRequest->isAccepted(),
            403
        );

        $sessionRequest->update(['status' => SessionRequest::STATUS_COMPLETED]);

        $sessionRequest->mentee->notify(
            new SessionRequestStatusChangedNotification($sessionRequest)
        );

        return redirect()
            ->route('session-requests.index')
            ->with('status', 'Session marked as completed.');
    }

    /**
     * Mentee cancels their own pending request.
     */
    public function cancel(Request $request, SessionRequest $sessionRequest): RedirectResponse
    {
        $this->authorize('cancel', $sessionRequest);

        $sessionRequest->update(['status' => SessionRequest::STATUS_CANCELLED]);

        return redirect()
            ->route('session-requests.index')
            ->with('status', 'Request cancelled.');
    }
}
