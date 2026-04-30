<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WithdrawalAdminController extends Controller
{
    public function index(Request $request): View
    {
        $query = Withdrawal::with(['mentor', 'processedBy'])->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $withdrawals = $query->paginate(20)->withQueryString();

        $stats = [
            'pending'  => Withdrawal::where('status', 'pending')->count(),
            'approved' => Withdrawal::where('status', 'approved')->sum('amount'),
            'rejected' => Withdrawal::where('status', 'rejected')->count(),
        ];

        return view('admin.withdrawals', compact('withdrawals', 'stats'));
    }

    public function approve(Request $request, Withdrawal $withdrawal): RedirectResponse
    {
        abort_if(! $withdrawal->isPending(), 422, 'This withdrawal has already been processed.');

        $request->validate([
            'admin_note' => ['nullable', 'string', 'max:500'],
        ]);

        $withdrawal->update([
            'status'       => Withdrawal::STATUS_APPROVED,
            'admin_note'   => $request->input('admin_note'),
            'processed_by' => $request->user()->id,
            'processed_at' => now(),
        ]);

        return back()->with('status', "Withdrawal of ₦" . number_format($withdrawal->amount, 0) . " for {$withdrawal->mentor->name} approved.");
    }

    public function reject(Request $request, Withdrawal $withdrawal): RedirectResponse
    {
        abort_if(! $withdrawal->isPending(), 422, 'This withdrawal has already been processed.');

        $request->validate([
            'admin_note' => ['nullable', 'string', 'max:500'],
        ]);

        $withdrawal->update([
            'status'       => Withdrawal::STATUS_REJECTED,
            'admin_note'   => $request->input('admin_note'),
            'processed_by' => $request->user()->id,
            'processed_at' => now(),
        ]);

        return back()->with('status', "Withdrawal for {$withdrawal->mentor->name} rejected.");
    }
}
