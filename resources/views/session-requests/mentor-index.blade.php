<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl" style="color:#1a3327;">Incoming Session Requests</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div class="px-5 py-3 rounded-xl text-sm font-medium" style="background:rgba(196,154,60,.1); border:1px solid rgba(196,154,60,.3); color:#8a6a1a;">
                    {{ session('status') }}
                </div>
            @endif

            @if ($requests->isEmpty())
                <div class="text-center py-20" style="color:#6b7a72;">
                    <svg class="mx-auto mb-4 w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M20 13V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v7m16 0v5a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-5m16 0H4"/>
                    </svg>
                    <p class="text-lg font-bold" style="color:#1a3327;">No requests yet</p>
                    <p class="text-sm mt-1">When mentees request sessions, they'll appear here.</p>
                </div>
            @else
                @foreach ($requests as $req)
                    <div class="rounded-2xl p-6" style="background:white; border:1px solid #e6e0d0;">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">

                            {{-- Left --}}
                            <div class="flex items-start gap-4 min-w-0">
                                @php
                                    $initials = collect(explode(' ', $req->mentee->name))
                                        ->take(2)->map(fn($w) => strtoupper($w[0]))->implode('');
                                @endphp
                                <div class="shrink-0 w-11 h-11 rounded-full flex items-center justify-center text-sm font-bold" style="background:#1a3327; color:#f4f1e8;">
                                    {{ $initials }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-black" style="color:#1a3327;">{{ $req->mentee->name }}</p>
                                    <p class="text-xs mt-0.5" style="color:#6b7a72;">{{ $req->mentee->headline }}</p>
                                    <div class="mt-2 flex flex-wrap gap-3 text-sm" style="color:#4a5e55;">
                                        <span class="flex items-center gap-1">
                                            📅 {{ $req->proposed_date->format('D, M j Y \a\t g:i A') }}
                                        </span>
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
                                            @if ($req->fee_amount)
                                                &middot; ₦{{ number_format($req->fee_amount, 0) }}/hr
                                            @endif
                                        </span>
                                    </div>

                                    @if ($req->message)
                                        <p class="mt-3 text-sm rounded-xl px-4 py-3 italic" style="color:#4a5e55; background:#ede9de;">
                                            &ldquo;{{ $req->message }}&rdquo;
                                        </p>
                                    @endif

                                    @if ($req->project_description)
                                        <div class="mt-3 text-sm rounded-xl px-4 py-3" style="background:rgba(26,51,39,.06); color:#1a3327;">
                                            <p class="font-bold mb-1" style="color:#1a3327;">Project</p>
                                            <p class="whitespace-pre-line">{{ $req->project_description }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions --}}
                            @if ($req->isPending())
                                <div class="flex shrink-0 gap-2 sm:flex-col">
                                    <form method="POST" action="{{ route('session-requests.update', $req) }}">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="accepted" />
                                        <button type="submit" class="px-5 py-2 rounded-xl text-sm font-bold transition"
                                                style="background:#1a3327; color:#f4f1e8;"
                                                onmouseover="this.style.background='#0f2219'" onmouseout="this.style.background='#1a3327'">
                                            Accept
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('session-requests.update', $req) }}">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="declined" />
                                        <button type="submit" class="px-5 py-2 rounded-xl text-sm font-bold transition"
                                                style="background:#fff0f0; border:1px solid #fca5a5; color:#dc2626;">
                                            Decline
                                        </button>
                                    </form>
                                </div>
                            @elseif ($req->isAccepted())
                                <div class="flex shrink-0 gap-2 sm:flex-col">
                                    <a href="{{ route('messages.show', $req->conversation) }}"
                                       class="px-5 py-2 rounded-xl text-sm font-bold transition text-center"
                                       style="background:rgba(26,51,39,.08); border:1px solid #2d5240; color:#1a3327;">
                                        💬 Chat
                                    </a>
                                    <form method="POST" action="{{ route('session-requests.complete', $req) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="w-full px-5 py-2 rounded-xl text-sm font-bold transition"
                                                style="background:rgba(196,154,60,.12); border:1px solid rgba(196,154,60,.4); color:#8a6a1a;"
                                                onclick="return confirm('Mark this session as completed?')">
                                            ✓ Mark Completed
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        <p class="mt-4 text-xs text-right" style="color:#6b7a72;">
                            Received {{ $req->created_at->diffForHumans() }}
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

            @if ($requests->isEmpty())
                <div class="text-center py-20 text-gray-400">
                    <svg class="mx-auto mb-4 w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M20 13V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v7m16 0v5a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-5m16 0H4"/>
                    </svg>
                    <p class="text-lg font-medium">No requests yet</p>
                    <p class="text-sm mt-1">When mentees request sessions, they'll appear here.</p>
                </div>
            @else
                @foreach ($requests as $req)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">

                            {{-- Left: mentee info + request details --}}
                            <div class="flex items-start gap-4 min-w-0">
                                @php
                                    $initials = collect(explode(' ', $req->mentee->name))
                                        ->take(2)->map(fn($w) => strtoupper($w[0]))->implode('');
                                @endphp
                                <div class="shrink-0 w-11 h-11 rounded-full bg-indigo-600 flex items-center justify-center text-white text-sm font-bold">
                                    {{ $initials }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900">{{ $req->mentee->name }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $req->mentee->headline }}</p>
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
                                        <p class="mt-3 text-sm text-gray-600 bg-gray-50 rounded-xl px-4 py-3 italic">
                                            "{{ $req->message }}"
                                        </p>
                                    @endif

                                    @if ($req->project_description)
                                        <div class="mt-3 text-sm text-gray-600 bg-blue-50 rounded-xl px-4 py-3">
                                            <p class="font-semibold text-blue-700 mb-1">Project</p>
                                            <p class="whitespace-pre-line">{{ $req->project_description }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Right: actions (only for pending / accepted) --}}
                            @if ($req->isPending())
                                <div class="flex shrink-0 gap-2 sm:flex-col">
                                    <form method="POST" action="{{ route('session-requests.update', $req) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="accepted" />
                                        <button type="submit"
                                                class="px-5 py-2 bg-green-600 text-white text-sm font-semibold rounded-xl hover:bg-green-700 transition focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                            Accept
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('session-requests.update', $req) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="declined" />
                                        <button type="submit"
                                                class="px-5 py-2 bg-red-50 text-red-700 border border-red-200 text-sm font-semibold rounded-xl hover:bg-red-100 transition focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2">
                                            Decline
                                        </button>
                                    </form>
                                </div>
                            @elseif ($req->isAccepted())
                                <div class="flex shrink-0 gap-2 sm:flex-col">
                                    <a href="{{ route('messages.show', $req->conversation) }}"
                                       class="px-5 py-2 bg-indigo-50 text-indigo-700 border border-indigo-200 text-sm font-semibold rounded-xl hover:bg-indigo-100 transition text-center">
                                        💬 Chat
                                    </a>
                                    <form method="POST" action="{{ route('session-requests.complete', $req) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="w-full px-5 py-2 bg-green-50 text-green-700 border border-green-200 text-sm font-semibold rounded-xl hover:bg-green-100 transition focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2"
                                                onclick="return confirm('Mark this session as completed?')">
                                            ✓ Mark Completed
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        <p class="mt-4 text-xs text-gray-400 text-right">
                            Received {{ $req->created_at->diffForHumans() }}
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
