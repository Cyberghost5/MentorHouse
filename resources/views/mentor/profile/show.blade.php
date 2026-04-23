@php
    $initials = collect(explode(' ', $user->name))
        ->take(2)
        ->map(fn ($w) => strtoupper($w[0]))
        ->implode('');

    $reviews     = \App\Models\Review::where('reviewee_id', $user->id)
        ->with('reviewer')
        ->latest()
        ->take(5)
        ->get();
    $avgRating   = \App\Models\Review::where('reviewee_id', $user->id)->avg('rating') ?? 0;
    $reviewCount = \App\Models\Review::where('reviewee_id', $user->id)->count();

    $menteesCount = \App\Models\SessionRequest::where('mentor_id', $user->id)
        ->where('status', 'accepted')
        ->count();
@endphp

<x-app-layout>
    <div style="background:#f4f1e8; min-height:100vh;">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

            @if (session('status'))
                <div class="mb-6 px-5 py-3 rounded-xl text-sm font-medium" style="background:rgba(26,51,39,.08); border:1px solid #2d5240; color:#1a3327;">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 px-5 py-3 rounded-xl text-sm" style="background:#fff0f0; border:1px solid #fca5a5; color:#dc2626;">
                    <p class="font-bold mb-1">Please fix the following:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-start">

                {{-- ── Left column ── --}}
                <div class="lg:col-span-3">

                    {{-- Hero banner --}}
                    <div class="relative rounded-2xl" style="background:#1a3327; height:190px;">
                        <a href="{{ route('mentors.index') }}"
                           class="absolute top-4 left-4 w-9 h-9 rounded-full flex items-center justify-center"
                           style="background:rgba(255,255,255,0.12);"
                           onmouseover="this.style.background='rgba(255,255,255,0.22)'"
                           onmouseout="this.style.background='rgba(255,255,255,0.12)'">
                            <svg class="w-4 h-4" fill="none" stroke="#f4f1e8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                        {{-- Avatar overlapping bottom-left --}}
                        <div class="absolute -bottom-12 left-6">
                            @if ($user->profile_photo)
                                <img src="{{ Storage::url($user->profile_photo) }}"
                                     alt="{{ $user->name }}"
                                     class="w-24 h-24 rounded-full object-cover"
                                     style="border:4px solid #f4f1e8; box-shadow:0 4px 16px rgba(0,0,0,.18);" />
                            @else
                                <div class="w-24 h-24 rounded-full flex items-center justify-center text-2xl font-black"
                                     style="background:#2d5240; color:#f4f1e8; border:4px solid #f4f1e8; box-shadow:0 4px 16px rgba(0,0,0,.18);">
                                    {{ $initials }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Profile info --}}
                    <div class="mt-16 px-1">

                        {{-- Name + badge --}}
                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-3xl font-black" style="color:#1a3327;">{{ $user->name }}</h1>
                            @if ($profile?->isOpen())
                                <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest"
                                      style="background:rgba(196,154,60,.15); color:#c49a3c; border:1px solid rgba(196,154,60,.35);">
                                    Top Mentor
                                </span>
                            @endif
                        </div>

                        {{-- Headline --}}
                        @if ($user->headline)
                            <p class="mt-1 text-xs font-black uppercase tracking-widest" style="color:#c49a3c;">
                                {{ $user->headline }}
                            </p>
                        @endif

                        {{-- Experience badge --}}
                        @if ($profile && $profile->years_of_experience)
                            <div class="mt-3 flex items-center gap-2">
                                <span class="text-sm font-medium" style="color:#6b7a72;">
                                    {{ $profile->years_of_experience }}+ yrs experience
                                </span>
                            </div>
                        @endif

                        {{-- Biography --}}
                        @if ($user->bio)
                            <div class="mt-8">
                                <p class="text-xs font-black uppercase tracking-widest mb-3" style="color:#6b7a72;">Biography</p>
                                <p class="leading-relaxed" style="color:#4a5e55;">{{ $user->bio }}</p>
                            </div>
                        @endif

                        {{-- Core Outcomes (Expertise) --}}
                        @if ($profile && !empty($profile->expertise))
                            <div class="mt-8">
                                <p class="text-xs font-black uppercase tracking-widest mb-4" style="color:#6b7a72;">Core Outcomes</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach ($profile->expertise as $skill)
                                        <div class="flex items-center gap-3 rounded-xl px-4 py-3"
                                             style="background:white; border:1px solid #e6e0d0;">
                                            <span class="shrink-0 w-6 h-6 rounded-full flex items-center justify-center"
                                                  style="border:1.5px solid #c49a3c;">
                                                <svg class="w-3 h-3" fill="none" stroke="#c49a3c" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </span>
                                            <span class="text-sm font-medium" style="color:#1a3327;">{{ $skill }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Reviews --}}
                        @if ($reviews->isNotEmpty())
                            <div class="mt-10">
                                <p class="text-xs font-black uppercase tracking-widest mb-4" style="color:#6b7a72;">Community Reviews</p>
                                <div class="space-y-4">
                                    @foreach ($reviews as $review)
                                        @php
                                            $ri = collect(explode(' ', $review->reviewer->name))
                                                ->take(2)->map(fn($w) => strtoupper($w[0]))->implode('');
                                        @endphp
                                        <div class="flex gap-4 rounded-xl p-4" style="background:white; border:1px solid #e6e0d0;">
                                            <div class="shrink-0 w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold"
                                                 style="background:#ede9de; color:#1a3327;">
                                                {{ $ri }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <p class="text-sm font-semibold" style="color:#1a3327;">{{ $review->reviewer->name }}</p>
                                                    <span class="text-xs" style="color:#6b7a72;">{{ $review->created_at->diffForHumans() }}</span>
                                                </div>
                                                <div class="flex gap-0.5 mt-0.5">
                                                    @for ($s = 1; $s <= 5; $s++)
                                                        <span class="text-sm" style="color:{{ $s <= $review->rating ? '#c49a3c' : '#d6cfbe' }}">&#9733;</span>
                                                    @endfor
                                                </div>
                                                @if ($review->comment)
                                                    <p class="mt-1 text-sm" style="color:#4a5e55;">{{ $review->comment }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

                {{-- ── Right column ── --}}
                <div class="lg:col-span-2 space-y-4" style="position:sticky; top:1.5rem;">

                    {{-- Stats card --}}
                    <div class="rounded-2xl p-6" style="background:white; border:1px solid #e6e0d0;">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-xs font-black uppercase tracking-widest mb-1" style="color:#6b7a72;">Mentees</p>
                                <p class="text-3xl font-black" style="color:#1a3327;">
                                    {{ $menteesCount > 0 ? $menteesCount . '+' : '0' }}
                                </p>
                            </div>
                            <div style="border-left:1px solid #e6e0d0; padding-left:1.5rem;">
                                <p class="text-xs font-black uppercase tracking-widest mb-1" style="color:#6b7a72;">Response</p>
                                <p class="text-3xl font-black" style="color:#1a3327;">24h</p>
                            </div>
                        </div>
                    </div>

                    @if ($profile)
                        @if ($profile->session_type === 'paid' && $profile->hourly_rate)

                            {{-- Premium Access card --}}
                            <div class="rounded-2xl p-6" style="background:#1a3327;">
                                <p class="text-xs font-black uppercase tracking-widest mb-2" style="color:#c49a3c;">Premium Access</p>
                                <p class="text-4xl font-black mb-5" style="color:#c49a3c;">
                                    &#8358;{{ number_format($profile->hourly_rate, 0) }}
                                </p>
                                @auth
                                    @if (auth()->user()->isMentee())
                                        @include('session-requests.partials.request-modal')
                                    @else
                                        <p class="text-sm" style="color:#8aab97;">Only mentees can request sessions.</p>
                                    @endif
                                @else
                                    <a href="{{ route('register') }}"
                                       class="block w-full text-center py-3 rounded-xl font-bold text-sm transition"
                                       style="background:#c49a3c; color:#1a3327;"
                                       onmouseover="this.style.background='#d4a94d'"
                                       onmouseout="this.style.background='#c49a3c'">
                                        Start Paid Mentorship
                                    </a>
                                @endauth
                            </div>

                            {{-- Scholarship Path card --}}
                            <div class="rounded-2xl p-6" style="background:white; border:1px solid #e6e0d0;">
                                <p class="text-xs font-black uppercase tracking-widest mb-2" style="color:#6b7a72;">Scholarship Path</p>
                                <p class="text-sm mb-4" style="color:#4a5e55;">
                                    Highly competitive. Only for those with <strong style="color:#1a3327;">extreme technical discipline</strong>.
                                </p>
                                @auth
                                    @if (auth()->user()->isMentee())
                                        <button type="button"
                                                class="w-full py-3 rounded-xl text-sm font-bold transition"
                                                style="border:2px solid #1a3327; color:#1a3327; background:transparent;"
                                                onmouseover="this.style.background='#1a3327';this.style.color='#f4f1e8'"
                                                onmouseout="this.style.background='transparent';this.style.color='#1a3327'">
                                            Apply via Qualification
                                        </button>
                                    @endif
                                @else
                                    <a href="{{ route('register') }}"
                                       class="block w-full text-center py-3 rounded-xl text-sm font-bold"
                                       style="border:2px solid #1a3327; color:#1a3327;">
                                        Apply via Qualification
                                    </a>
                                @endauth
                            </div>

                        @else

                            {{-- Free Access card --}}
                            <div class="rounded-2xl p-6" style="background:#1a3327;">
                                <p class="text-xs font-black uppercase tracking-widest mb-2" style="color:#c49a3c;">Free Access</p>
                                <p class="text-4xl font-black mb-1" style="color:#f4f1e8;">&#8358;0</p>
                                <p class="text-sm mb-5" style="color:#8aab97;">No cost. Just commitment.</p>
                                @auth
                                    @if (auth()->user()->isMentee())
                                        @include('session-requests.partials.request-modal')
                                    @else
                                        <p class="text-sm" style="color:#8aab97;">Only mentees can request sessions.</p>
                                    @endif
                                @else
                                    <a href="{{ route('register') }}"
                                       class="block w-full text-center py-3 rounded-xl font-bold text-sm transition"
                                       style="background:#c49a3c; color:#1a3327;"
                                       onmouseover="this.style.background='#d4a94d'"
                                       onmouseout="this.style.background='#c49a3c'">
                                        Connect for Free
                                    </a>
                                @endauth
                            </div>

                        @endif
                    @endif

                    {{-- Community Rating card --}}
                    @if ($reviewCount > 0)
                        <div class="rounded-2xl p-6" style="background:#c49a3c;">
                            <p class="text-xs font-black uppercase tracking-widest mb-3" style="color:rgba(255,255,255,0.7);">Community Rating</p>
                            <div class="flex items-end gap-2">
                                <span style="color:white; font-size:1.4rem; line-height:1;">&#9733;</span>
                                <span class="font-black" style="color:white; font-size:2.5rem; line-height:1;">{{ number_format($avgRating, 2) }}</span>
                                <span class="font-semibold mb-1" style="color:rgba(255,255,255,0.7); font-size:1.1rem;">/ 5.0</span>
                            </div>
                        </div>
                    @endif

                </div>

            </div>
        </div>
    </div>
</x-app-layout>