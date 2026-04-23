<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>MentorHouse  Find Your Perfect Mentor</title>
        <meta name="description" content="MentorHouse connects you with expert mentors for 1-on-1 sessions, career guidance, and skills growth.">
        <meta property="og:title" content="MentorHouse  Find Your Perfect Mentor">
        <meta property="og:description" content="Book sessions with vetted mentors in tech, design, business, and more.">
        <meta property="og:type" content="website">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Inter', sans-serif; }
        </style>
    </head>
    <body class="antialiased" style="background-color:#f4f1e8; color:#1a3327; font-family:'Inter',sans-serif;">

{{--  Navigation  --}}
<header class="sticky top-0 z-50" style="background-color:#f4f1e8; border-bottom:1px solid #e6e0d0;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 flex items-center justify-between h-16">
        {{-- Logo --}}
        <a href="/" class="flex items-center gap-2.5">
            <span class="w-9 h-9 rounded-xl flex items-center justify-center" style="background-color:#1a3327;">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" style="color:#c49a3c;">
                    <path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          d="M8 21h8M12 17v4M5 3h14l-1.5 7A5.5 5.5 0 0 1 12 15a5.5 5.5 0 0 1-5.5-5L5 3Z"/>
                </svg>
            </span>
            <span class="font-bold text-xl tracking-tight" style="color:#1a3327;">MentorHouse</span>
        </a>

        {{-- Center nav --}}
        <nav class="hidden md:flex items-center gap-8 text-xs font-semibold tracking-widest uppercase" style="color:#6b7a72;">
            <a href="{{ route('mentors.index') }}" class="transition-colors" style="color:#6b7a72;" onmouseover="this.style.color='#1a3327'" onmouseout="this.style.color='#6b7a72'">Mentors</a>
            @auth
                @if (auth()->user()->isMentor())
                    <a href="{{ route('mentor.dashboard') }}" class="transition-colors" style="color:#6b7a72;" onmouseover="this.style.color='#1a3327'" onmouseout="this.style.color='#6b7a72'">Dashboard</a>
                @elseif (auth()->user()->isMentee())
                    <a href="{{ route('mentee.dashboard') }}" class="transition-colors" style="color:#6b7a72;" onmouseover="this.style.color='#1a3327'" onmouseout="this.style.color='#6b7a72'">Dashboard</a>
                @endif
            @endauth
            <a href="{{ route('register') }}" class="transition-colors" style="color:#6b7a72;" onmouseover="this.style.color='#1a3327'" onmouseout="this.style.color='#6b7a72'">Become a Mentor</a>
        </nav>

        {{-- Auth CTA --}}
        <div class="flex items-center gap-3">
            @auth
                @if (auth()->user()->isMentor())
                    <a href="{{ route('mentor.dashboard') }}" class="text-sm font-semibold px-5 py-2 rounded-lg border-2 transition" style="border-color:#1a3327; color:#1a3327;" onmouseover="this.style.background='#1a3327'; this.style.color='#f4f1e8'" onmouseout="this.style.background='transparent'; this.style.color='#1a3327'">Dashboard</a>
                @else
                    <a href="{{ route('mentee.dashboard') }}" class="text-sm font-semibold px-5 py-2 rounded-lg border-2 transition" style="border-color:#1a3327; color:#1a3327;" onmouseover="this.style.background='#1a3327'; this.style.color='#f4f1e8'" onmouseout="this.style.background='transparent'; this.style.color='#1a3327'">Dashboard</a>
                @endif
            @else
                <a href="{{ route('login') }}" class="text-sm font-semibold px-5 py-2 rounded-lg border-2 transition" style="border-color:#1a3327; color:#1a3327;" onmouseover="this.style.background='#1a3327'; this.style.color='#f4f1e8'" onmouseout="this.style.background='transparent'; this.style.color='#1a3327'">Sign In</a>
            @endauth
        </div>
    </div>
</header>

{{--  Hero  --}}
<section class="max-w-4xl mx-auto px-4 sm:px-6 pt-20 pb-24 text-center">
    <span class="inline-block text-xs font-bold tracking-widest uppercase mb-8" style="color:#c49a3c; letter-spacing:0.15em;">
        Direct High-Value Mentorship
    </span>
    <h1 class="font-black leading-none tracking-tight" style="font-size: clamp(3rem, 8vw, 6rem); color:#1a3327; line-height:1.05;">
        Find a mentor.<br>
        Or prove you<br>deserve one.
    </h1>
    <p class="mt-8 text-lg max-w-xl mx-auto leading-relaxed" style="color:#4a5e55;">
        MentorHouse is a disciplined platform where you pay for direct access
        or complete challenging tasks to earn mentorship from industry titans.
    </p>
    <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
        <a href="{{ route('mentors.index') }}"
           class="inline-flex items-center gap-2 px-8 py-4 font-bold text-base rounded-xl transition"
           style="background-color:#1a3327; color:#f4f1e8;"
           onmouseover="this.style.background='#0f2219'" onmouseout="this.style.background='#1a3327'">
            Explore Mentors
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
        <a href="{{ route('register') }}"
           class="inline-flex items-center gap-2 px-8 py-4 font-bold text-base rounded-xl border-2 transition"
           style="border-color:#1a3327; color:#1a3327; background:transparent;"
           onmouseover="this.style.background='#1a3327'; this.style.color='#f4f1e8'" onmouseout="this.style.background='transparent'; this.style.color='#1a3327'">
            Become a Mentor
        </a>
    </div>
</section>

{{--  Stats bar  --}}
<section style="border-top:1px solid #d6cfbe; border-bottom:1px solid #d6cfbe; background-color:#ede9de;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10 grid grid-cols-2 sm:grid-cols-4 gap-6 text-center">
        @foreach ([
            ['500+', 'Expert mentors'],
            ['2,000+', 'Sessions completed'],
            ['4.8 / 5', 'Average rating'],
            ['15+', 'Skill categories'],
        ] as [$num, $label])
        <div>
            <p class="text-2xl font-black" style="color:#1a3327;">{{ $num }}</p>
            <p class="text-sm mt-1 font-medium" style="color:#6b7a72;">{{ $label }}</p>
        </div>
        @endforeach
    </div>
</section>

{{--  How it works  --}}
<section class="max-w-6xl mx-auto px-4 sm:px-6 py-24">
    <div class="text-center mb-14">
        <span class="inline-block text-xs font-bold tracking-widest uppercase mb-4" style="color:#c49a3c;">The Process</span>
        <h2 class="text-4xl font-black" style="color:#1a3327;">How it works</h2>
        <p class="mt-3 text-base font-medium" style="color:#4a5e55;">Get from zero to session in three simple steps.</p>
    </div>
    <div class="grid sm:grid-cols-3 gap-6">
        @foreach ([
            ['01', 'Find a mentor', 'Browse profiles by skill, availability, and rate. Read reviews from real mentees.', 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
            ['02', 'Request a session', 'Send a short description of what you need. Mentors respond within 24 hours.', 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
            ['03', 'Grow your skills', 'Join the live session, ask questions, and get actionable feedback tailored to you.', 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'],
        ] as [$step, $title, $desc, $icon])
        <div class="rounded-2xl p-8" style="background-color:#ede9de; border:1px solid #d6cfbe;">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-5" style="background-color:#1a3327;">
                <svg class="w-6 h-6" fill="none" stroke="#c49a3c" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                </svg>
            </div>
            <span class="text-xs font-black tracking-widest uppercase" style="color:#c49a3c;">Step {{ $step }}</span>
            <h3 class="mt-2 text-xl font-bold" style="color:#1a3327;">{{ $title }}</h3>
            <p class="mt-3 text-sm leading-relaxed" style="color:#4a5e55;">{{ $desc }}</p>
        </div>
        @endforeach
    </div>
</section>

{{--  Features  --}}
<section class="py-24" style="background-color:#1a3327;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-14">
            <span class="inline-block text-xs font-bold tracking-widest uppercase mb-4" style="color:#c49a3c;">Why MentorHouse</span>
            <h2 class="text-4xl font-black" style="color:#f4f1e8;">Everything you need to learn faster</h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ([
                ['Verified experts', 'Every mentor is reviewed by our team before going live on the platform.', 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                ['Real reviews', 'Honest ratings from mentees help you pick the right match for your goals.', 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
                ['In-platform messaging', 'Chat with your mentor before and after sessions  no external tools needed.', 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                ['Flexible scheduling', 'Sessions on your terms. Find mentors available weekdays, evenings, or weekends.', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ['Secure payments', 'Pay only for confirmed sessions via Paystack or Korapay  fast and safe.', 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                ['Track your progress', 'View all past sessions, reviews given, and your learning journey in one place.', 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
            ] as [$title, $desc, $icon])
            <div class="rounded-2xl p-6 flex gap-4" style="background-color:#213d2e; border:1px solid #2d5240;">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center mt-0.5" style="background-color:#c49a3c20;">
                    <svg class="w-5 h-5" fill="none" stroke="#c49a3c" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-sm" style="color:#f4f1e8;">{{ $title }}</h3>
                    <p class="mt-1 text-sm leading-relaxed" style="color:#8aab97;">{{ $desc }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{--  CTA  --}}
<section class="max-w-6xl mx-auto px-4 sm:px-6 py-24 text-center">
    <div class="rounded-3xl px-8 py-16" style="background-color:#1a3327;">
        <span class="inline-block text-xs font-bold tracking-widest uppercase mb-6" style="color:#c49a3c;">Get Started Today</span>
        <h2 class="text-4xl sm:text-5xl font-black" style="color:#f4f1e8;">Ready to level up?</h2>
        <p class="mt-4 text-lg max-w-md mx-auto" style="color:#8aab97;">
            Join thousands of learners already growing with MentorHouse.
        </p>
        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('mentors.index') }}"
               class="inline-flex items-center gap-2 px-8 py-4 font-bold rounded-xl transition"
               style="background-color:#c49a3c; color:#1a3327;"
               onmouseover="this.style.background='#b08930'" onmouseout="this.style.background='#c49a3c'">
                Explore Mentors
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
            @guest
            <a href="{{ route('register') }}"
               class="inline-flex items-center gap-2 px-8 py-4 font-bold rounded-xl border-2 transition"
               style="border-color:#f4f1e8; color:#f4f1e8; background:transparent;"
               onmouseover="this.style.background='#f4f1e8'; this.style.color='#1a3327'" onmouseout="this.style.background='transparent'; this.style.color='#f4f1e8'">
                Create Free Account
            </a>
            @endguest
        </div>
    </div>
</section>

{{--  Footer  --}}
<footer style="background-color:#111f17; border-top:1px solid #2d5240;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <span class="font-black text-lg" style="color:#f4f1e8;">MentorHouse</span>
            <span class="text-sm" style="color:#4a5e55;"> Find your perfect mentor</span>
        </div>
        <nav class="flex gap-6 text-xs font-semibold tracking-widest uppercase" style="color:#4a5e55;">
            <a href="{{ route('mentors.index') }}" style="color:#4a5e55;" onmouseover="this.style.color='#c49a3c'" onmouseout="this.style.color='#4a5e55'">Find Mentors</a>
            @auth
                <a href="{{ route('messages.index') }}" style="color:#4a5e55;" onmouseover="this.style.color='#c49a3c'" onmouseout="this.style.color='#4a5e55'">Messages</a>
                <a href="{{ route('session-requests.index') }}" style="color:#4a5e55;" onmouseover="this.style.color='#c49a3c'" onmouseout="this.style.color='#4a5e55'">Sessions</a>
            @endauth
        </nav>
        <p class="text-xs" style="color:#4a5e55;">&copy; {{ date('Y') }} MentorHouse. All rights reserved.</p>
    </div>
</footer>

    </body>
</html>
