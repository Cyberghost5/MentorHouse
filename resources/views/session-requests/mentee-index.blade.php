<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl" style="color:#1a3327;">My Session Requests</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div class="px-5 py-3 rounded-xl text-sm font-medium" style="background:rgba(196,154,60,.1); border:1px solid rgba(196,154,60,.3); color:#8a6a1a;">
                    {{ session('status') }}
                </div>
            @endif

            <div class="flex items-center justify-between">
                <p class="text-sm" style="color:#6b7a72;">{{ $requests->total() }} request(s) total</p>
                <a href="{{ route('mentors.index') }}" class="text-sm font-semibold transition" style="color:#1a3327;"
                   onmouseover="this.style.color='#c49a3c'" onmouseout="this.style.color='#1a3327'">
                    Find more mentors &rarr;
                </a>
            </div>

            @if ($requests->isEmpty())
                <div class="text-center py-20" style="color:#6b7a72;">
                    <svg class="mx-auto mb-4 w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z"/>
                    </svg>
                    <p class="text-lg font-bold" style="color:#1a3327;">No requests yet</p>
                    <p class="text-sm mt-1">Browse mentors and send your first session request.</p>
                    <a href="{{ route('mentors.index') }}"
                       class="mt-4 inline-block px-6 py-2.5 rounded-xl text-sm font-bold transition"
                       style="background:#1a3327; color:#f4f1e8;"
                       onmouseover="this.style.background='#0f2219'" onmouseout="this.style.background='#1a3327'">
                        Browse Mentors
                    </a>
                </div>
            @else
                @foreach ($requests as $req)
                    <div class="rounded-2xl p-6" style="background:white; border:1px solid #e6e0d0;">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">

                            {{-- Left --}}
                            <div class="flex items-start gap-4 min-w-0">
                                @php
                                    $initials = collect(explode(' ', $req->mentor->name))
                                        ->take(2)->map(fn($w) => strtoupper($w[0]))->implode('');
                                @endphp
                                <a href="{{ route('mentors.show', $req->mentor->username) }}"
                                   class="shrink-0 w-11 h-11 rounded-full flex items-center justify-center text-sm font-bold"
                                   style="background:#1a3327; color:#f4f1e8;">
                                    {{ $initials }}
                                </a>
                                <div class="min-w-0">
                                    <a href="{{ route('mentors.show', $req->mentor->username) }}"
                                       class="font-black transition" style="color:#1a3327;"
                                       onmouseover="this.style.color='#c49a3c'" onmouseout="this.style.color='#1a3327'">
                                        {{ $req->mentor->name }}
                                    </a>
                                    <p class="text-xs mt-0.5" style="color:#6b7a72;">{{ $req->mentor->headline }}</p>
                                    <div class="mt-2 flex flex-wrap gap-3 text-sm" style="color:#4a5e55;">
                                        <span>&#128197; {{ $req->proposed_date->format('D, M j Y \a\t g:i A') }}</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $req->statusColor() }}">
                                            {{ $req->statusLabel() }}
                                        </span>
                                        @php
                                            $typeStyle = match($req->session_type) {
                                                'paid'          => 'background:rgba(196,154,60,.12); color:#8a6a1a;',
                                                'project_based' => 'background:rgba(26,51,39,.08); color:#1a3327;',
                                                default         => 'background:#ede9de; color:#4a5e55;',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold" style="{{ $typeStyle }}">
                                            {{ ucfirst(str_replace('_', '-', $req->session_type)) }}
                                            @if ($req->fee_amount) &middot; &#8358;{{ number_format($req->fee_amount, 0) }} @endif
                                        </span>
                                    </div>

                                    @if ($req->message)
                                        <p class="mt-3 text-sm italic" style="color:#4a5e55;">&ldquo;{{ $req->message }}&rdquo;</p>
                                    @endif
                                    @if ($req->project_description)
                                        <p class="mt-2 text-sm" style="color:#6b7a72; display:-webkit-box; -webkit-line-clamp:2; line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">{{ $req->project_description }}</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex shrink-0 flex-col gap-2">
                                @if ($req->isPending())
                                    <form method="POST" action="{{ route('session-requests.cancel', $req) }}"
                                          onsubmit="return confirm('Cancel this request?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="w-full px-4 py-2 rounded-xl text-sm font-semibold transition"
                                                style="border:1px solid #d6cfbe; color:#4a5e55;"
                                                onmouseover="this.style.background='#ede9de'" onmouseout="this.style.background='transparent'">
                                            Cancel Request
                                        </button>
                                    </form>
                                @elseif ($req->isAccepted() && $req->conversation)
                                    @if ($req->requiresPayment() && $req->payment && $req->payment->isPending())
                                        <a href="{{ route('payments.pay', $req) }}"
                                           class="px-5 py-2 rounded-xl text-sm font-bold transition text-center"
                                           style="background:#1a3327; color:#f4f1e8;"
                                           onmouseover="this.style.background='#0f2219'" onmouseout="this.style.background='#1a3327'">
                                            &#128179; Pay Now
                                        </a>
                                    @elseif ($req->requiresPayment() && $req->isPaidFor())
                                        <span class="px-4 py-2 rounded-xl text-sm font-bold text-center"
                                              style="background:rgba(196,154,60,.12); border:1px solid rgba(196,154,60,.4); color:#8a6a1a;">
                                            &#10004; Paid
                                        </span>
                                    @endif
                                    <a href="{{ route('messages.show', $req->conversation) }}"
                                       class="px-5 py-2 rounded-xl text-sm font-bold transition text-center"
                                       style="background:rgba(26,51,39,.08); border:1px solid #2d5240; color:#1a3327;">
                                        &#128172; Chat
                                    </a>
                                @elseif ($req->isCompleted())
                                    @if ($req->conversation)
                                        <a href="{{ route('messages.show', $req->conversation) }}"
                                           class="px-5 py-2 rounded-xl text-sm font-bold transition text-center"
                                           style="border:1px solid #d6cfbe; color:#4a5e55;">
                                            &#128172; Chat
                                        </a>
                                    @endif
                                    @php $alreadyReviewed = $req->review && $req->review->reviewer_id === auth()->id(); @endphp
                                    @unless ($alreadyReviewed)
                                        <button onclick="document.getElementById('review-form-{{ $req->id }}').classList.toggle('hidden')"
                                                class="px-5 py-2 rounded-xl text-sm font-bold transition"
                                                style="background:rgba(196,154,60,.12); border:1px solid rgba(196,154,60,.4); color:#8a6a1a;">
                                            &#9733; Leave Review
                                        </button>
                                    @else
                                        <span class="px-4 py-2 rounded-xl text-sm font-bold text-center"
                                              style="background:rgba(196,154,60,.12); border:1px solid rgba(196,154,60,.4); color:#8a6a1a;">
                                            &#10004; Reviewed
                                        </span>
                                    @endunless
                                @endif
                            </div>
                        </div>

                        {{-- Review form --}}
                        @if ($req->isCompleted() && (!$req->review || $req->review->reviewer_id !== auth()->id()))
                            <div id="review-form-{{ $req->id }}" class="hidden mt-4 pt-4" style="border-top:1px solid #e6e0d0;">
                                <form method="POST" action="{{ route('reviews.store', $req) }}">
                                    @csrf
                                    <p class="text-sm font-bold mb-2" style="color:#1a3327;">Rate your session with {{ $req->mentor->name }}</p>
                                    <div class="flex gap-2 mb-3">
                                        @for ($star = 1; $star <= 5; $star++)
                                            <label class="cursor-pointer">
                                                <input type="radio" name="rating" value="{{ $star }}" class="sr-only" required>
                                                <span class="text-2xl transition" style="color:#d6cfbe;"
                                                      onmouseover="this.style.color='#c49a3c'" onmouseout="this.style.color='#d6cfbe'">&#9733;</span>
                                            </label>
                                        @endfor
                                    </div>
                                    <textarea name="comment" rows="3" placeholder="Share your experience (optional)..."
                                              class="w-full rounded-xl px-3 py-2 text-sm resize-none"
                                              style="border:1px solid #d6cfbe; color:#1a3327;"
                                              onfocus="this.style.borderColor='#1a3327'" onblur="this.style.borderColor='#d6cfbe'"></textarea>
                                    <div class="mt-2 flex gap-2">
                                        <button type="submit" class="px-5 py-2 rounded-xl text-sm font-bold transition"
                                                style="background:#1a3327; color:#f4f1e8;"
                                                onmouseover="this.style.background='#0f2219'" onmouseout="this.style.background='#1a3327'">
                                            Submit Review
                                        </button>
                                        <button type="button" onclick="document.getElementById('review-form-{{ $req->id }}').classList.add('hidden')"
                                                class="px-4 py-2 rounded-xl text-sm transition"
                                                style="border:1px solid #d6cfbe; color:#4a5e55;">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif

                        <p class="mt-4 text-xs text-right" style="color:#6b7a72;">
                            Sent {{ $req->created_at->diffForHumans() }}
                        </p>
                    </div>
                @endforeach

                <div class="mt-6">{{ $requests->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>