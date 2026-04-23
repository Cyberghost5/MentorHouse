<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Review;
use App\Models\SessionRequest;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /** Stats dashboard */
    public function index(): View
    {
        $stats = [
            'total_users'    => User::count(),
            'total_mentors'  => User::where('role', 'mentor')->count(),
            'total_mentees'  => User::where('role', 'mentee')->count(),
            'total_sessions' => SessionRequest::count(),
            'completed'      => SessionRequest::where('status', 'completed')->count(),
            'pending'        => SessionRequest::where('status', 'pending')->count(),
            'total_revenue'  => Payment::where('status', 'paid')->sum('amount'),
            'total_reviews'  => Review::count(),
        ];

        $recentSessions = SessionRequest::with(['mentor', 'mentee'])
            ->latest()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recentSessions'));
    }

    /** Users list */
    public function users(Request $request): View
    {
        $query = User::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function suspend(User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 403, 'Cannot suspend another admin.');

        $user->update(['is_active' => false]);

        return back()->with('status', "{$user->name} has been suspended.");
    }

    public function activate(User $user): RedirectResponse
    {
        $user->update(['is_active' => true]);

        return back()->with('status', "{$user->name} has been activated.");
    }

    /** All session requests */
    public function sessions(Request $request): View
    {
        $query = SessionRequest::with(['mentor', 'mentee']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $sessions = $query->latest()->paginate(20)->withQueryString();

        return view('admin.sessions', compact('sessions'));
    }

    /** All reviews */
    public function reviews(Request $request): View
    {
        $reviews = Review::with(['reviewer', 'reviewee', 'sessionRequest'])
            ->latest()
            ->paginate(20);

        return view('admin.reviews', compact('reviews'));
    }

    public function deleteReview(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('status', 'Review deleted.');
    }

    /** Admin settings */
    public function settings(): View
    {
        $gateway = Setting::get('payment_gateway', 'paystack');

        return view('admin.settings', compact('gateway'));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $request->validate([
            'payment_gateway' => ['required', 'in:paystack,korapay'],
        ]);

        Setting::set('payment_gateway', $request->input('payment_gateway'));

        return back()->with('status', 'Settings saved.');
    }
}
