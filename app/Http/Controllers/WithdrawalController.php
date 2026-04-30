<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WithdrawalController extends Controller
{
    /** Minimum balance required to request a withdrawal */
    const MIN_BALANCE = 5000;

    public function index(Request $request): View
    {
        $user = $request->user();

        $withdrawals = $user->withdrawals()->latest()->paginate(15);

        return view('mentor.withdrawals.index', [
            'withdrawals'        => $withdrawals,
            'totalEarnings'      => $user->totalEarnings(),
            'totalWithdrawn'     => $user->totalWithdrawn(),
            'pendingWithdrawal'  => $user->pendingWithdrawal(),
            'availableBalance'   => $user->availableBalance(),
            'minBalance'         => self::MIN_BALANCE,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $available = $user->availableBalance();

        if ($available < self::MIN_BALANCE) {
            return back()->with('error', 'Your available balance must be at least ₦' . number_format(self::MIN_BALANCE, 0) . ' to request a withdrawal.');
        }

        // Block if there's already a pending request
        if ($user->withdrawals()->where('status', Withdrawal::STATUS_PENDING)->exists()) {
            return back()->with('error', 'You already have a pending withdrawal request. Please wait for it to be processed.');
        }

        $validated = $request->validate([
            'amount'         => ['required', 'numeric', 'min:1000', 'max:' . $available],
            'bank_name'      => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'regex:/^\d{10}$/', 'max:20'],
            'account_name'   => ['required', 'string', 'max:200'],
        ], [
            'account_number.regex' => 'Account number must be exactly 10 digits.',
            'amount.max'           => 'Amount cannot exceed your available balance of ₦' . number_format($available, 0) . '.',
        ]);

        $validated['mentor_id'] = $user->id;

        Withdrawal::create($validated);

        return back()->with('status', 'Withdrawal request submitted. The admin will process it shortly.');
    }
}
