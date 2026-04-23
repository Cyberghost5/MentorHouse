@php
    $initials = collect(explode(' ', $user->name))
        ->take(2)
        ->map(fn ($w) => strtoupper($w[0]))
        ->implode('');

    // Reviews received by this mentor
    $reviews      = \App\Models\Review::where('reviewee_id', $user->id)
        ->with('reviewer')
        ->latest()
        ->take(5)
        ->get();
    $avgRating    = \App\Models\Review::where('reviewee_id', $user->id)->avg('rating');
    $reviewCount  = \App\Models\Review::where('reviewee_id', $user->id)->count();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm" style="color:#6b7a72;">
            <a href="{{ route('mentors.index') }}" class="transition" style="color:#4a5e55;" onmouseover="this.style.color='#1a3327'" onmouseout="this.style.color='#4a5e55'">Find a Mentor</a>
            <span>/</span>
            <span style="color:#1a3327;">{{ $user->name }}</span>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Profile hero --}}
            <div class="rounded-2xl p-8" style="background:white; border:1px solid #e6e0d0;">
                <div class="flex flex-col sm:flex-row items-start gap-6">
                    {{-- Avatar --}}
                    <div class="shrink-0">
                        @if ($user->profile_photo)
                            <img src="{{ Storage::url($user->profile_photo) }}" alt="{{ $user->name }}"
                                 class="w-24 h-24 rounded-full object-cover" style="border:3px solid #e6e0d0;" />
                        @else
                            <div class="w-24 h-24 rounded-full flex items-center justify-center text-3xl font-black" style="background:#1a3327; color:#f4f1e8;">
                                {{ $initials }}
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-2xl font-black" style="color:#1a3327;">{{ $user->name }}</h1>
                            @if ($profile?->isOpen())
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold" style="background:rgba(196,154,60,.12); color:#c49a3c;">Open to mentoring</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold" style="background:#ede9de; color:#6b7a72;">Currently unavailable</span>
                            @endif
                        </div>

                        @if ($reviewCount > 0)
                            <div class="mt-1 flex items-center gap-1.5">
                                @php $rounded = round($avgRating * 2) / 2; @endphp
                                @for ($s = 1; $s <= 5; $s++)
                                    <span class="text-lg" style="color:{{ $s <= $rounded ? '#c49a3c' : '#d6cfbe' }}">★</span>
                                @endfor
                                <span class="text-sm font-semibold" style="color:#1a3327;">{{ number_format($avgRating, 1) }}</span>
                                <span class="text-xs" style="color:#6b7a72;">({{ $reviewCount }} {{ Str::plural('review', $reviewCount) }})</span>
                            </div>
                        @endif

                        @if ($user->headline)
                            <p class="mt-1" style="color:#4a5e55;">{{ $user->headline }}</p>
                        @endif

                        @if ($profile)
                            <div class="mt-3 flex flex-wrap gap-4 text-sm" style="color:#4a5e55;">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="#c49a3c" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3 3 0 0 0-4.138 4.138L9 14.172l5.657-5.657a3 3 0 0 0-4.243-4.243L9 5.929l-.828-.828a3 3 0 0 0-4.243 0z"/>
                                    </svg>
                                    {{ $profile->years_of_experience }} yrs experience
                                </span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold"
                                      style="background:rgba(26,51,39,.08); color:#1a3327;">
                                    {{ $profile->sessionTypeLabel() }}
                                    @if ($profile->session_type === 'paid' && $profile->hourly_rate)
                                        &middot; ₦{{ number_format($profile->hourly_rate, 0) }}/hr
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Main column --}}
                <div class="md:col-span-2 space-y-6">
                    @if ($user->bio)
                        <div class="rounded-2xl p-6" style="background:white; border:1px solid #e6e0d0;">
                            <h2 class="font-black mb-3" style="color:#1a3327;">About</h2>
                            <p class="leading-relaxed whitespace-pre-line" style="color:#4a5e55;">{{ $user->bio }}</p>
                        </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    @if ($profile && !empty($profile->expertise))
                        <div class="rounded-2xl p-6" style="background:white; border:1px solid #e6e0d0;">
                            <h2 class="font-black mb-3" style="color:#1a3327;">Expertise</h2>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($profile->expertise as $skill)
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide" style="border:1px solid #d6cfbe; color:#6b7a72;">
                                        {{ $skill }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @auth
                        @if (auth()->user()->isMentee())
                            <div class="rounded-2xl p-6" style="background:#1a3327; border:1px solid #2d5240;">
                                <h3 class="font-black text-lg" style="color:#f4f1e8;">Ready to connect?</h3>
                                <p class="text-sm mt-1 mb-4" style="color:#8aab97;">Send {{ $user->name }} a session request.</p>
                                @include('session-requests.partials.request-modal')
                            </div>
                        @endif
                    @else
                        <div class="rounded-2xl p-6" style="background:#1a3327; border:1px solid #2d5240;">
                            <h3 class="font-black text-lg" style="color:#f4f1e8;">Interested?</h3>
                            <p class="text-sm mt-1 mb-4" style="color:#8aab97;">Create a free account to request a session.</p>
                            @include('session-requests.partials.request-modal')
                        </div>
                    @endauth
                </div>
            </div>

            {{-- Reviews --}}
            @if ($reviews->isNotEmpty())
                <div class="rounded-2xl p-6" style="background:white; border:1px solid #e6e0d0;">
                    <h2 class="font-black mb-4" style="color:#1a3327;">
                        Reviews <span class="text-sm font-normal" style="color:#6b7a72;">({{ $reviewCount }} total)</span>
                    </h2>
                    <div class="space-y-5">
                        @foreach ($reviews as $review)
                            @php
                                $ri = collect(explode(' ', $review->reviewer->name))
                                    ->take(2)->map(fn($w) => strtoupper($w[0]))->implode('');
                            @endphp
                            <div class="flex gap-4">
                                <div class="shrink-0 w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold" style="background:#ede9de; color:#1a3327;">
                                    {{ $ri }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-semibold" style="color:#1a3327;">{{ $review->reviewer->name }}</p>
                                        <span class="text-xs" style="color:#6b7a72;">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex gap-0.5 mt-0.5">
                                        @for ($s = 1; $s <= 5; $s++)
                                            <span class="text-sm" style="color:{{ $s <= $review->rating ? '#c49a3c' : '#d6cfbe' }}">★</span>
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
</x-app-layout>
                <div class="flex flex-col sm:flex-row items-start gap-6">
                    {{-- Avatar --}}
                    <div class="shrink-0">
                        @if ($user->profile_photo)
                            <img src="{{ Storage::url($user->profile_photo) }}"
                                 alt="{{ $user->name }}"
                                 class="w-24 h-24 rounded-full object-cover ring-4 ring-indigo-100" />
                        @else
                            <div class="w-24 h-24 rounded-full bg-indigo-600 flex items-center justify-center text-white text-3xl font-bold ring-4 ring-indigo-100">
                                {{ $initials }}
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                            @if ($profile?->isOpen())
                                <span class="px-2.5 py-0.5 bg-green-50 text-green-700 rounded-full text-xs font-semibold">
                                    Open to mentoring
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 bg-gray-100 text-gray-500 rounded-full text-xs font-semibold">
                                    Currently unavailable
                                </span>
                            @endif
                        </div>

                        {{-- Average rating --}}
                        @if ($reviewCount > 0)
                            <div class="mt-1 flex items-center gap-1.5">
                                @php $rounded = round($avgRating * 2) / 2; @endphp
                                @for ($s = 1; $s <= 5; $s++)
                                    <span class="text-lg {{ $s <= $rounded ? 'text-amber-400' : 'text-gray-200' }}">★</span>
                                @endfor
                                <span class="text-sm font-semibold text-gray-700">{{ number_format($avgRating, 1) }}</span>
                                <span class="text-xs text-gray-400">({{ $reviewCount }} {{ Str::plural('review', $reviewCount) }})</span>
                            </div>
                        @endif

                        @if ($user->headline)
                            <p class="mt-1 text-gray-500">{{ $user->headline }}</p>
                        @endif

                        @if ($profile)
                            <div class="mt-3 flex flex-wrap gap-4 text-sm text-gray-600">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12l2 2 4-4M7.835 4.697a3 3 0 0 0-4.138 4.138L9 14.172l5.657-5.657a3 3 0 0 0-4.243-4.243L9 5.929l-.828-.828a3 3 0 0 0-4.243 0z"/>
                                    </svg>
                                    {{ $profile->years_of_experience }} yrs experience
                                </span>

                                @php
                                    $typeColor = match($profile->session_type) {
                                        'free'          => 'bg-green-50 text-green-700',
                                        'paid'          => 'bg-amber-50 text-amber-700',
                                        'project_based' => 'bg-blue-50 text-blue-700',
                                        default         => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeColor }}">
                                    {{ $profile->sessionTypeLabel() }}
                                    @if ($profile->session_type === 'paid' && $profile->hourly_rate)
                                        · ${{ number_format($profile->hourly_rate, 0) }}/hr
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Main column --}}
                <div class="md:col-span-2 space-y-6">
                    @if ($user->bio)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h2 class="font-semibold text-gray-900 mb-3">About</h2>
                            <p class="text-gray-600 leading-relaxed whitespace-pre-line">{{ $user->bio }}</p>
                        </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    @if ($profile && !empty($profile->expertise))
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h2 class="font-semibold text-gray-900 mb-3">Expertise</h2>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($profile->expertise as $skill)
                                    <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full text-sm font-medium">
                                        {{ $skill }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @auth
                        @if (auth()->user()->isMentee())
                            <div class="bg-indigo-600 rounded-2xl p-6 text-white">
                                <h3 class="font-semibold text-lg">Ready to connect?</h3>
                                <p class="text-indigo-200 text-sm mt-1 mb-4">Send {{ $user->name }} a session request.</p>
                                @include('session-requests.partials.request-modal')
                            </div>
                        @endif
                    @else
                        <div class="bg-indigo-600 rounded-2xl p-6 text-white">
                            <h3 class="font-semibold text-lg">Interested?</h3>
                            <p class="text-indigo-200 text-sm mt-1 mb-4">Create a free account to request a session.</p>
                            @include('session-requests.partials.request-modal')
                        </div>
                    @endauth
                </div>
            </div>

            {{-- Reviews section --}}
            @if ($reviews->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="font-semibold text-gray-900 mb-4">
                        Reviews
                        <span class="text-sm font-normal text-gray-400 ml-1">({{ $reviewCount }} total)</span>
                    </h2>
                    <div class="space-y-5">
                        @foreach ($reviews as $review)
                            @php
                                $ri = collect(explode(' ', $review->reviewer->name))
                                    ->take(2)->map(fn($w) => strtoupper($w[0]))->implode('');
                            @endphp
                            <div class="flex gap-4">
                                <div class="shrink-0 w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">
                                    {{ $ri }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-semibold text-gray-800">{{ $review->reviewer->name }}</p>
                                        <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex gap-0.5 mt-0.5">
                                        @for ($s = 1; $s <= 5; $s++)
                                            <span class="text-sm {{ $s <= $review->rating ? 'text-amber-400' : 'text-gray-200' }}">★</span>
                                        @endfor
                                    </div>
                                    @if ($review->comment)
                                        <p class="mt-1 text-sm text-gray-600">{{ $review->comment }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
