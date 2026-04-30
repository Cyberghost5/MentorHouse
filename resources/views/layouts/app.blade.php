<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- SEO — override per-page with @section('seo_*', '...') in child views --}}
        <title>@yield('seo_title', config('app.name', 'MentorHouse'))</title>
        <meta name="description" content="@yield('seo_description', 'MentorHouse — connect with expert mentors for 1-on-1 sessions, career guidance, and project-based learning.')">
        <link rel="canonical" href="@yield('seo_canonical', url()->current())" />
        <meta name="robots" content="index, follow">
        <meta property="og:site_name"   content="{{ config('app.name') }}">
        <meta property="og:title"       content="@yield('seo_og_title', config('app.name'))">
        <meta property="og:description" content="@yield('seo_og_description', 'Find your perfect mentor on MentorHouse.')">
        <meta property="og:type"        content="@yield('seo_og_type', 'website')">
        <meta property="og:url"         content="@yield('seo_og_url', url()->current())">
        @hasSection('seo_og_image')
            <meta property="og:image" content="@yield('seo_og_image')">
            <meta name="twitter:image"   content="@yield('seo_og_image')">
        @endif
        <meta name="twitter:card"        content="summary_large_image">
        <meta name="twitter:title"       content="@yield('seo_og_title', config('app.name'))">
        <meta name="twitter:description" content="@yield('seo_og_description', 'Find your perfect mentor on MentorHouse.')">
        @stack('seo_extra')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
        <style>body { font-family: 'Inter', sans-serif; }</style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased" style="background-color:#f4f1e8;">
        <div class="min-h-screen flex flex-col">
            <livewire:layout.navigation />

            <!-- Page Heading -->
            @if (isset($header))
                <header style="background:#f4f1e8; border-bottom:1px solid #e6e0d0;">
                    <div class="max-w-6xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer style="background-color:#111f17; border-top:1px solid #2d5240; margin-top:3rem;">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-2">
                            <span class="font-black text-lg" style="color:#f4f1e8;">MentorHouse</span>
                            <span class="text-sm" style="color:#4a5e55;">— Find your perfect mentor</span>
                        </div>
                        <nav class="flex flex-wrap gap-6 text-xs font-semibold tracking-widest uppercase" style="color:#4a5e55;">
                            <a href="{{ route('mentors.index') }}" style="color:#4a5e55;" onmouseover="this.style.color='#c49a3c'" onmouseout="this.style.color='#4a5e55'">Find Mentors</a>
                            @auth
                                <a href="{{ route('messages.index') }}" style="color:#4a5e55;" onmouseover="this.style.color='#c49a3c'" onmouseout="this.style.color='#4a5e55'">Messages</a>
                                <a href="{{ route('session-requests.index') }}" style="color:#4a5e55;" onmouseover="this.style.color='#c49a3c'" onmouseout="this.style.color='#4a5e55'">Sessions</a>
                                @if (auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" style="color:#4a5e55;" onmouseover="this.style.color='#c49a3c'" onmouseout="this.style.color='#4a5e55'">Admin</a>
                                @endif
                            @endauth
                        </nav>
                        <p class="text-xs" style="color:#4a5e55;">&copy; {{ date('Y') }} MentorHouse. All rights reserved.</p>
                    </div>
                </div>
            </footer>
        </div>

        @livewireScripts
    </body>
</html>
