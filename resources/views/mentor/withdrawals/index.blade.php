<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl" style="color:#1a3327;">Withdrawals</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Flash messages --}}
            @if (session('status'))
                <div class="px-5 py-3 rounded-xl text-sm font-medium" style="background:rgba(196,154,60,.1); border:1px solid rgba(196,154,60,.3); color:#8a6a1a;">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="px-5 py-3 rounded-xl text-sm font-medium" style="background:#fff0f0; border:1px solid #fca5a5; color:#dc2626;">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Balance summary --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach ([
                    ['label' => 'Total Earned',    'value' => $totalEarnings,     'color' => '#1a3327'],
                    ['label' => 'Total Withdrawn',  'value' => $totalWithdrawn,    'color' => '#4a5e55'],
                    ['label' => 'Pending',          'value' => $pendingWithdrawal, 'color' => '#c49a3c'],
                    ['label' => 'Available',        'value' => $availableBalance,  'color' => '#1a3327'],
                ] as $stat)
                    <div class="rounded-2xl p-5 text-center" style="background:white; border:1px solid #e6e0d0;">
                        <p class="text-xs font-black uppercase tracking-widest mb-1" style="color:#8aab97;">{{ $stat['label'] }}</p>
                        <p class="text-xl font-black" style="color:{{ $stat['color'] }};">₦{{ number_format($stat['value'], 0) }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Request form --}}
            @php
                $hasPending = auth()->user()->withdrawals()->where('status', 'pending')->exists();
                $canRequest = $availableBalance >= $minBalance && !$hasPending;
            @endphp

            <div class="rounded-2xl p-8" style="background:white; border:1px solid #e6e0d0;">
                <h3 class="font-black text-lg mb-1" style="color:#1a3327;">Request Withdrawal</h3>
                <p class="text-sm mb-6" style="color:#6b7a72;">
                    Minimum balance to withdraw: <strong>₦{{ number_format($minBalance, 0) }}</strong>.
                    Withdrawals are processed manually by the admin.
                </p>

                @if ($hasPending)
                    <div class="px-4 py-3 rounded-xl text-sm" style="background:rgba(196,154,60,.08); border:1px solid rgba(196,154,60,.3); color:#8a6a1a;">
                        You have a pending withdrawal request. You can submit another once it has been processed.
                    </div>
                @elseif ($availableBalance < $minBalance)
                    <div class="px-4 py-3 rounded-xl text-sm" style="background:#f8f8f6; border:1px solid #e6e0d0; color:#6b7a72;">
                        Your available balance is below the ₦{{ number_format($minBalance, 0) }} minimum. Keep mentoring to unlock withdrawals!
                    </div>
                @else
                    <form method="POST" action="{{ route('mentor.withdrawals.store') }}" class="space-y-5">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            {{-- Amount --}}
                            <div class="sm:col-span-2">
                                <label for="amount" class="block text-sm font-semibold mb-1.5" style="color:#1a3327;">
                                    Amount (₦) <span class="text-red-500">*</span>
                                    <span class="font-normal text-xs ml-1" style="color:#8aab97;">max ₦{{ number_format($availableBalance, 0) }}</span>
                                </label>
                                <div class="relative w-56">
                                    <span class="absolute inset-y-0 left-3 flex items-center text-sm" style="color:#6b7a72;">₦</span>
                                    <input type="number" name="amount" id="amount"
                                           min="1000" max="{{ floor($availableBalance) }}" step="1"
                                           value="{{ old('amount') }}"
                                           class="w-full pl-7 pr-4 py-2.5 rounded-xl text-sm transition @error('amount') border-red-400 @enderror"
                                           style="border:1px solid #d6cfbe; color:#1a3327;"
                                           onfocus="this.style.borderColor='#1a3327'" onblur="this.style.borderColor='#d6cfbe'" />
                                </div>
                                @error('amount')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Bank Name --}}
                            <div>
                                <label for="bank_name" class="block text-sm font-semibold mb-1.5" style="color:#1a3327;">
                                    Bank Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="bank_name" id="bank_name"
                                       maxlength="100"
                                       value="{{ old('bank_name') }}"
                                       placeholder="e.g. First Bank"
                                       class="w-full rounded-xl px-4 py-2.5 text-sm transition @error('bank_name') border-red-400 @enderror"
                                       style="border:1px solid #d6cfbe; color:#1a3327;"
                                       onfocus="this.style.borderColor='#1a3327'" onblur="this.style.borderColor='#d6cfbe'" />
                                @error('bank_name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Account Number --}}
                            <div>
                                <label for="account_number" class="block text-sm font-semibold mb-1.5" style="color:#1a3327;">
                                    Account Number <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="account_number" id="account_number"
                                       maxlength="10" inputmode="numeric" pattern="\d{10}"
                                       value="{{ old('account_number') }}"
                                       placeholder="10-digit account number"
                                       class="w-full rounded-xl px-4 py-2.5 text-sm transition @error('account_number') border-red-400 @enderror"
                                       style="border:1px solid #d6cfbe; color:#1a3327;"
                                       onfocus="this.style.borderColor='#1a3327'" onblur="this.style.borderColor='#d6cfbe'" />
                                @error('account_number')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Account Name --}}
                            <div class="sm:col-span-2">
                                <label for="account_name" class="block text-sm font-semibold mb-1.5" style="color:#1a3327;">
                                    Account Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="account_name" id="account_name"
                                       maxlength="200"
                                       value="{{ old('account_name') }}"
                                       placeholder="Name on the bank account"
                                       class="w-full rounded-xl px-4 py-2.5 text-sm transition @error('account_name') border-red-400 @enderror"
                                       style="border:1px solid #d6cfbe; color:#1a3327;"
                                       onfocus="this.style.borderColor='#1a3327'" onblur="this.style.borderColor='#d6cfbe'" />
                                @error('account_name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                    class="px-6 py-2.5 rounded-xl text-sm font-bold transition"
                                    style="background:#1a3327; color:#f4f1e8;"
                                    onmouseover="this.style.background='#0f2219'" onmouseout="this.style.background='#1a3327'">
                                Submit Request
                            </button>
                        </div>
                    </form>
                @endif
            </div>

            {{-- Transaction history --}}
            <div class="rounded-2xl overflow-hidden" style="background:white; border:1px solid #e6e0d0;">
                <div class="px-6 py-4" style="border-bottom:1px solid #e6e0d0;">
                    <h3 class="font-black" style="color:#1a3327;">Transaction History</h3>
                </div>

                @if ($withdrawals->isEmpty())
                    <div class="px-6 py-10 text-center text-sm" style="color:#8aab97;">No withdrawal requests yet.</div>
                @else
                    <div class="divide-y" style="divide-color:#e6e0d0;">
                        @foreach ($withdrawals as $w)
                            @php
                                $badge = match($w->status) {
                                    'approved' => ['bg' => 'rgba(34,197,94,.1)',  'border' => 'rgba(34,197,94,.3)',  'color' => '#15803d', 'label' => 'Approved'],
                                    'rejected' => ['bg' => '#fff0f0',             'border' => '#fca5a5',             'color' => '#dc2626', 'label' => 'Rejected'],
                                    default    => ['bg' => 'rgba(196,154,60,.1)', 'border' => 'rgba(196,154,60,.3)', 'color' => '#8a6a1a', 'label' => 'Pending'],
                                };
                            @endphp
                            <div class="px-6 py-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="font-bold text-sm" style="color:#1a3327;">₦{{ number_format($w->amount, 0) }}</p>
                                        <p class="text-xs mt-0.5" style="color:#6b7a72;">
                                            {{ $w->bank_name }} · {{ $w->account_number }} · {{ $w->account_name }}
                                        </p>
                                        @if ($w->admin_note)
                                            <p class="text-xs mt-1 italic" style="color:#8aab97;">Admin: {{ $w->admin_note }}</p>
                                        @endif
                                        <p class="text-xs mt-1" style="color:#8aab97;">Requested {{ $w->created_at->diffForHumans() }}</p>
                                    </div>
                                    <span class="shrink-0 px-3 py-1 rounded-full text-xs font-bold"
                                          style="background:{{ $badge['bg'] }}; border:1px solid {{ $badge['border'] }}; color:{{ $badge['color'] }};">
                                        {{ $badge['label'] }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="px-6 py-4" style="border-top:1px solid #e6e0d0;">
                        {{ $withdrawals->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
