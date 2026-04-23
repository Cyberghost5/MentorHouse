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
                    Find more mentors →
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
                                   class="shrink-0 w-11 h-11 rounded-full flex items-center justify-center text-sm font-bold transition"
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
                                        <span>📅 {{ $req->proposed_date->format('D, M j Y \a\t g:i A') }}</span>
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
                                            @if ($req->fee_amount) &middot; ₦{{ number_format($req->fee_amount, 0) }}/hr @endif
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
                                            💳 Pay Now
                                        </a>
                                    @elseif ($req->requiresPayment() && $req->isPaidFor())
                                        <span class="px-4 py-2 rounded-xl text-sm font-bold text-center"
                                              style="background:rgba(196,154,60,.12); border:1px solid rgba(196,154,60,.4); color:#8a6a1a;">
                                            ✓ Paid
                                        </span>
                                    @endif
                                    <a href="{{ route('messages.show', $req->conversation) }}"
                                       class="px-5 py-2 rounded-xl text-sm font-bold transition text-center"
                                       style="background:rgba(26,51,39,.08); border:1px solid #2d5240; color:#1a3327;">
                                        💬 Chat
                                    </a>
                                @elseif ($req->isCompleted())
                                    @if ($req->conversation)
                                        <a href="{{ route('messages.show', $req->conversation) }}"
                                           class="px-5 py-2 rounded-xl text-sm font-bold transition text-center"
                                           style="border:1px solid #d6cfbe; color:#4a5e55;">
                                            💬 Chat
                                        </a>
                                    @endif
                                    @php $alreadyReviewed = $req->review && $req->review->reviewer_id === auth()->id(); @endphp
                                    @unless ($alreadyReviewed)
                                        <button onclick="document.getElementById('review-form-{{ $req->id }}').classList.toggle('hidden')"
                                                class="px-5 py-2 rounded-xl text-sm font-bold transition"
                                                style="background:rgba(196,154,60,.12); border:1px solid rgba(196,154,60,.4); color:#8a6a1a;">
                                            ⭐ Leave Review
                                        </button>
                                    @else
                                        <span class="px-4 py-2 rounded-xl text-sm font-bold text-center"
                                              style="background:rgba(196,154,60,.12); border:1px solid rgba(196,154,60,.4); color:#8a6a1a;">
                                            ✓ Reviewed
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
                                    <textarea name="comment" rows="3" placeholder="Share your experience (optional)…"
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
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div class="px-5 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500">{{ $requests->total() }} request(s) total</p>
                <a href="{{ route('mentors.index') }}"
                   class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition">
                    Find more mentors →
                </a>
            </div>

            @if ($requests->isEmpty())
                <div class="text-center py-20 text-gray-400">
                    <svg class="mx-auto mb-4 w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z"/>
                    </svg>
                    <p class="text-lg font-medium">No requests yet</p>
                    <p class="text-sm mt-1">Browse mentors and send your first session request.</p>
                    <a href="{{ route('mentors.index') }}"
                       class="mt-4 inline-block px-6 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-semibold hover:bg-indigo-700 transition">
                        Browse Mentors
                    </a>
                </div>
            @else
                @foreach ($requests as $req)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">

                            {{-- Left: mentor info + request details --}}
                            <div class="flex items-start gap-4 min-w-0">
                                @php
                                    $initials = collect(explode(' ', $req->mentor->name))
                                        ->take(2)->map(fn($w) => strtoupper($w[0]))->implode('');
                                @endphp
                                <a href="{{ route('mentors.show', $req->mentor->username) }}"
                                   class="shrink-0 w-11 h-11 rounded-full bg-indigo-600 flex items-center justify-center text-white text-sm font-bold hover:opacity-90 transition">
                                    {{ $initials }}
                                </a>
                                <div class="min-w-0">
                                    <a href="{{ route('mentors.show', $req->mentor->username) }}"
                                       class="font-semibold text-gray-900 hover:text-indigo-600 transition">
                                        {{ $req->mentor->name }}
                                    </a>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $req->mentor->headline }}</p>
                                    <div class="mt-2 flex flex-wrap gap-3 text-sm text-gray-600">
                                        <span class="flex items-center gap-1">
                                            📅 {{ $req->proposed_date->format('D, M j Y \a\t g:i A') }}
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $req->statusColor() }}">
                                            {{ $req->statusLabel() }}
                                        </span>
                                        @php
                                            $typeColor = match($req->session_type) {
                                                'free'          => 'bg-green-50 text-green-700',
                                                'paid'          => 'bg-amber-50 text-amber-700',
                                                'project_based' => 'bg-blue-50 text-blue-700',
                                                default         => 'bg-gray-100 text-gray-600',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeColor }}">
                                            {{ ucfirst(str_replace('_', '-', $req->session_type)) }}
                                            @if ($req->fee_amount)
                                                · ${{ number_format($req->fee_amount, 0) }}/hr
                                            @endif
                                        </span>
                                    </div>

                                    @if ($req->message)
                                        <p class="mt-3 text-sm text-gray-600 italic">
                                            "{{ $req->message }}"
                                        </p>
                                    @endif

                                    @if ($req->project_description)
                                        <p class="mt-2 text-sm text-gray-500 line-clamp-2">
                                            {{ $req->project_description }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Right: actions --}}
                            <div class="flex shrink-0 flex-col gap-2">
                                @if ($req->isPending())
                                    <form method="POST" action="{{ route('session-requests.cancel', $req) }}"
                                          onsubmit="return confirm('Cancel this request?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="w-full px-4 py-2 border border-gray-200 text-gray-500 text-sm rounded-xl hover:bg-gray-50 hover:border-gray-300 transition">
                                            Cancel Request
                                        </button>
                                    </form>
                                @elseif ($req->isAccepted() && $req->conversation)
                                    {{-- Pay Now button for unpaid paid sessions --}}
                                    @if ($req->requiresPayment() && $req->payment && $req->payment->isPending())
                                        <a href="{{ route('payments.pay', $req) }}"
                                           class="px-5 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition text-center">
                                            💳 Pay Now
                                        </a>
                                    @elseif ($req->requiresPayment() && $req->isPaidFor())
                                        <span class="px-4 py-2 bg-green-50 text-green-700 border border-green-200 text-sm rounded-xl font-medium text-center">
                                            ✓ Paid
                                        </span>
                                    @endif
                                    <a href="{{ route('messages.show', $req->conversation) }}"
                                       class="px-5 py-2 bg-indigo-50 text-indigo-700 border border-indigo-200 text-sm font-semibold rounded-xl hover:bg-indigo-100 transition text-center">
                                        💬 Chat
                                    </a>
                                @elseif ($req->isCompleted())
                                    @if ($req->conversation)
                                        <a href="{{ route('messages.show', $req->conversation) }}"
                                           class="px-5 py-2 bg-gray-50 text-gray-600 border border-gray-200 text-sm font-semibold rounded-xl hover:bg-gray-100 transition text-center">
                                            💬 Chat
                                        </a>
                                    @endif
                                    @php $alreadyReviewed = $req->review && $req->review->reviewer_id === auth()->id(); @endphp
                                    @unless ($alreadyReviewed)
                                        <button onclick="document.getElementById('review-form-{{ $req->id }}').classList.toggle('hidden')"
                                                class="px-5 py-2 bg-amber-50 text-amber-700 border border-amber-200 text-sm font-semibold rounded-xl hover:bg-amber-100 transition">
                                            ⭐ Leave Review
                                        </button>
                                    @else
                                        <span class="px-4 py-2 bg-green-50 text-green-700 text-sm rounded-xl border border-green-200 font-medium text-center">
                                            ✓ Reviewed
                                        </span>
                                    @endunless
                                @endif
                            </div>
                        </div>

                        {{-- Inline review form (hidden by default) --}}
                        @if ($req->isCompleted() && (!$req->review || $req->review->reviewer_id !== auth()->id()))
                            <div id="review-form-{{ $req->id }}" class="hidden mt-4 border-t border-gray-100 pt-4">
                                <form method="POST" action="{{ route('reviews.store', $req) }}">
                                    @csrf
                                    <p class="text-sm font-semibold text-gray-700 mb-2">Rate your session with {{ $req->mentor->name }}</p>
                                    <div class="flex gap-2 mb-3">
                                        @for ($star = 1; $star <= 5; $star++)
                                            <label class="cursor-pointer">
                                                <input type="radio" name="rating" value="{{ $star }}" class="sr-only" required>
                                                <span class="text-2xl hover:text-amber-400 transition" x-data x-bind:class="{ 'text-amber-400': $el.previousElementSibling.checked, 'text-gray-300': !$el.previousElementSibling.checked }">★</span>
                                            </label>
                                        @endfor
                                    </div>
                                    <textarea name="comment" rows="3" placeholder="Share your experience (optional)…"
                                              class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>
                                    <div class="mt-2 flex gap-2">
                                        <button type="submit"
                                                class="px-5 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition">
                                            Submit Review
                                        </button>
                                        <button type="button"
                                                onclick="document.getElementById('review-form-{{ $req->id }}').classList.add('hidden')"
                                                class="px-4 py-2 bg-gray-50 text-gray-600 border border-gray-200 text-sm rounded-xl hover:bg-gray-100 transition">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif

                        <p class="mt-4 text-xs text-gray-400 text-right">
                            Sent {{ $req->created_at->diffForHumans() }}
                        </p>
                    </div>
                @endforeach

                <div class="mt-6">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
