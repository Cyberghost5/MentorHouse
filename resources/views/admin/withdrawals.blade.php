@extends('admin.layout')

@section('title', 'Withdrawals')

@section('content')

    {{-- Flash --}}
    @if (session('status'))
        <div class="mb-4 px-4 py-3 rounded-xl text-sm" style="background:rgba(196,154,60,.1); border:1px solid rgba(196,154,60,.3); color:#8a6a1a;">
            {{ session('status') }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="rounded-2xl p-5" style="background:white; border:1px solid #e6e0d0;">
            <p class="text-xs font-black uppercase tracking-widest" style="color:#8aab97;">Pending Requests</p>
            <p class="text-2xl font-black mt-1" style="color:#c49a3c;">{{ $stats['pending'] }}</p>
        </div>
        <div class="rounded-2xl p-5" style="background:white; border:1px solid #e6e0d0;">
            <p class="text-xs font-black uppercase tracking-widest" style="color:#8aab97;">Total Approved (₦)</p>
            <p class="text-2xl font-black mt-1" style="color:#1a3327;">₦{{ number_format($stats['approved'], 0) }}</p>
        </div>
        <div class="rounded-2xl p-5" style="background:white; border:1px solid #e6e0d0;">
            <p class="text-xs font-black uppercase tracking-widest" style="color:#8aab97;">Rejected</p>
            <p class="text-2xl font-black mt-1" style="color:#dc2626;">{{ $stats['rejected'] }}</p>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" class="mb-4 flex items-center gap-3">
        <select name="status"
                onchange="this.form.submit()"
                class="rounded-xl px-4 py-2 text-sm"
                style="border:1px solid #d6cfbe; color:#1a3327; background:white;">
            <option value="">All statuses</option>
            @foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $val => $label)
                <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </form>

    {{-- Table --}}
    <div class="rounded-2xl overflow-hidden" style="background:white; border:1px solid #e6e0d0;">
        @if ($withdrawals->isEmpty())
            <div class="px-6 py-12 text-center text-sm" style="color:#8aab97;">No withdrawal requests found.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom:2px solid #e6e0d0; background:#f9f7f3;">
                            <th class="text-left px-5 py-3 font-black text-xs uppercase tracking-wider" style="color:#8aab97;">Mentor</th>
                            <th class="text-left px-5 py-3 font-black text-xs uppercase tracking-wider" style="color:#8aab97;">Amount</th>
                            <th class="text-left px-5 py-3 font-black text-xs uppercase tracking-wider" style="color:#8aab97;">Bank Details</th>
                            <th class="text-left px-5 py-3 font-black text-xs uppercase tracking-wider" style="color:#8aab97;">Status</th>
                            <th class="text-left px-5 py-3 font-black text-xs uppercase tracking-wider" style="color:#8aab97;">Requested</th>
                            <th class="text-left px-5 py-3 font-black text-xs uppercase tracking-wider" style="color:#8aab97;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="divide-color:#e6e0d0;">
                        @foreach ($withdrawals as $w)
                            @php
                                $badge = match($w->status) {
                                    'approved' => ['bg' => 'rgba(34,197,94,.1)',  'border' => 'rgba(34,197,94,.3)',  'color' => '#15803d', 'label' => 'Approved'],
                                    'rejected' => ['bg' => '#fff0f0',             'border' => '#fca5a5',             'color' => '#dc2626', 'label' => 'Rejected'],
                                    default    => ['bg' => 'rgba(196,154,60,.1)', 'border' => 'rgba(196,154,60,.3)', 'color' => '#8a6a1a', 'label' => 'Pending'],
                                };
                            @endphp
                            <tr>
                                <td class="px-5 py-4">
                                    <p class="font-semibold" style="color:#1a3327;">{{ $w->mentor->name }}</p>
                                    <p class="text-xs" style="color:#8aab97;">{{ $w->mentor->email }}</p>
                                </td>
                                <td class="px-5 py-4 font-bold" style="color:#1a3327;">₦{{ number_format($w->amount, 0) }}</td>
                                <td class="px-5 py-4">
                                    <p style="color:#1a3327;">{{ $w->bank_name }}</p>
                                    <p class="text-xs" style="color:#6b7a72;">{{ $w->account_number }} — {{ $w->account_name }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold"
                                          style="background:{{ $badge['bg'] }}; border:1px solid {{ $badge['border'] }}; color:{{ $badge['color'] }};">
                                        {{ $badge['label'] }}
                                    </span>
                                    @if ($w->admin_note)
                                        <p class="text-xs mt-1 italic" style="color:#8aab97;">{{ $w->admin_note }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-xs" style="color:#8aab97;">{{ $w->created_at->diffForHumans() }}</td>
                                <td class="px-5 py-4">
                                    @if ($w->isPending())
                                        {{-- Approve --}}
                                        <form method="POST" action="{{ route('admin.withdrawals.approve', $w) }}"
                                              x-data="{ note: '' }" class="inline"
                                              onsubmit="return confirm('Approve this withdrawal?')">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text" name="admin_note"
                                                   placeholder="Note (optional)"
                                                   maxlength="500"
                                                   class="mb-1 w-40 rounded-lg px-2 py-1 text-xs"
                                                   style="border:1px solid #d6cfbe; color:#1a3327;" />
                                            <br>
                                            <button type="submit"
                                                    class="px-3 py-1 rounded-lg text-xs font-bold transition mr-1"
                                                    style="background:rgba(34,197,94,.15); border:1px solid rgba(34,197,94,.3); color:#15803d;"
                                                    onmouseover="this.style.background='rgba(34,197,94,.3)'" onmouseout="this.style.background='rgba(34,197,94,.15)'">
                                                ✓ Approve
                                            </button>
                                        </form>
                                        {{-- Reject --}}
                                        <form method="POST" action="{{ route('admin.withdrawals.reject', $w) }}"
                                              class="inline mt-1"
                                              onsubmit="return confirm('Reject this withdrawal?')">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text" name="admin_note"
                                                   placeholder="Reason (optional)"
                                                   maxlength="500"
                                                   class="mb-1 w-40 rounded-lg px-2 py-1 text-xs"
                                                   style="border:1px solid #d6cfbe; color:#1a3327;" />
                                            <br>
                                            <button type="submit"
                                                    class="px-3 py-1 rounded-lg text-xs font-bold transition"
                                                    style="background:#fff0f0; border:1px solid #fca5a5; color:#dc2626;"
                                                    onmouseover="this.style.background='#ffd9d9'" onmouseout="this.style.background='#fff0f0'">
                                                ✗ Reject
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs" style="color:#8aab97;">
                                            Processed {{ $w->processed_at?->diffForHumans() }}
                                            @if ($w->processedBy) by {{ $w->processedBy->name }} @endif
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-4" style="border-top:1px solid #e6e0d0;">
                {{ $withdrawals->links() }}
            </div>
        @endif
    </div>

@endsection
