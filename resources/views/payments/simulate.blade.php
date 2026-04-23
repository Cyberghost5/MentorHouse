<x-app-layout>
    <div style="background:#f4f1e8; min-height:100vh;">
        <div class="max-w-lg mx-auto px-4 py-16">

            {{-- Card --}}
            <div class="rounded-2xl overflow-hidden" style="background:white; border:1px solid #e6e0d0; box-shadow:0 4px 24px rgba(0,0,0,.07);">

                {{-- Header --}}
                <div class="px-8 py-6" style="background:#1a3327;">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center" style="background:rgba(196,154,60,.2);">
                            <svg class="w-5 h-5" fill="none" stroke="#c49a3c" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2zm10-10V7a4 4 0 0 0-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-black uppercase tracking-widest" style="color:#c49a3c;">MentorHouse Pay</p>
                            <p class="text-xs" style="color:#8aab97;">Secure Checkout</p>
                        </div>
                    </div>
                    <p class="text-3xl font-black" style="color:#c49a3c;">&#8358;{{ number_format($payment->amount, 0) }}</p>
                    <p class="text-sm mt-1" style="color:#8aab97;">Session with {{ $sessionRequest->mentor->name }}</p>
                </div>

                {{-- Body --}}
                <div class="px-8 py-6 space-y-4">

                    {{-- Dev notice --}}
                    <div class="flex items-start gap-3 rounded-xl px-4 py-3" style="background:rgba(196,154,60,.08); border:1px solid rgba(196,154,60,.25);">
                        <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="#c49a3c" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                        </svg>
                        <p class="text-xs leading-relaxed" style="color:#8a6a1a;">
                            <strong>Test mode.</strong> No real payment will be charged.
                            Add <code style="background:rgba(196,154,60,.15); padding:1px 5px; border-radius:4px;">PAYSTACK_SECRET_KEY</code>
                            to <code style="background:rgba(196,154,60,.15); padding:1px 5px; border-radius:4px;">.env</code> to enable live payments.
                        </p>
                    </div>

                    {{-- Order summary --}}
                    <div class="rounded-xl px-4 py-4 space-y-2" style="background:#f4f1e8;">
                        <div class="flex justify-between text-sm">
                            <span style="color:#6b7a72;">Mentor</span>
                            <span class="font-semibold" style="color:#1a3327;">{{ $sessionRequest->mentor->name }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span style="color:#6b7a72;">Session date</span>
                            <span class="font-semibold" style="color:#1a3327;">{{ $sessionRequest->proposed_date->format('D, M j Y') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span style="color:#6b7a72;">Amount</span>
                            <span class="font-black" style="color:#1a3327;">&#8358;{{ number_format($payment->amount, 0) }}</span>
                        </div>
                        <div class="flex justify-between text-sm" style="border-top:1px solid #e6e0d0; padding-top:0.5rem; margin-top:0.25rem;">
                            <span style="color:#6b7a72;">Reference</span>
                            <span class="text-xs font-mono" style="color:#6b7a72;">{{ $payment->gateway_reference ?? 'Pending' }}</span>
                        </div>
                    </div>

                    {{-- Confirm button --}}
                    <form method="POST" action="{{ route('payments.confirm-simulate', $sessionRequest) }}">
                        @csrf
                        <button type="submit"
                                class="w-full py-3.5 rounded-xl font-black text-sm tracking-wide transition"
                                style="background:#1a3327; color:#f4f1e8;"
                                onmouseover="this.style.background='#0f2219'" onmouseout="this.style.background='#1a3327'">
                            &#10003;&nbsp; Confirm Payment &mdash; &#8358;{{ number_format($payment->amount, 0) }}
                        </button>
                    </form>

                    {{-- Cancel --}}
                    <a href="{{ route('session-requests.index') }}"
                       class="block w-full text-center py-2.5 rounded-xl text-sm font-semibold transition"
                       style="border:1px solid #d6cfbe; color:#4a5e55;"
                       onmouseover="this.style.background='#ede9de'" onmouseout="this.style.background='transparent'">
                        Cancel
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
