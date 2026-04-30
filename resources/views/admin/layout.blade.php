<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" style="background:#f4f1e8;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased">

<div class="flex h-full">
    {{-- Sidebar --}}
    <aside class="hidden lg:flex flex-col w-56 shrink-0" style="background:#111f17; color:#8aab97;">
        <div class="px-5 py-5" style="border-bottom:1px solid #2d5240;">
            <a href="{{ route('admin.dashboard') }}" class="text-lg font-black" style="color:#f4f1e8;">
                🛡 Admin
            </a>
            <p class="text-xs mt-0.5" style="color:#4a5e55;">{{ config('app.name') }}</p>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
            @php
                $nav = [
                    ['route' => 'admin.dashboard',  'label' => '📊 Dashboard'],
                    ['route' => 'admin.users',       'label' => '👥 Users'],
                    ['route' => 'admin.sessions',    'label' => '📅 Sessions'],
                    ['route' => 'admin.reviews',     'label' => '⭐ Reviews'],
                    ['route' => 'admin.settings',    'label' => '⚙️ Settings'],
                ];
            @endphp
            @foreach ($nav as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center px-3 py-2 rounded-lg transition"
                   style="{{ request()->routeIs($item['route']) ? 'background:#c49a3c; color:#1a3327; font-weight:700;' : 'color:#8aab97;' }}"
                   onmouseover="if(!{{ request()->routeIs($item['route']) ? 'true' : 'false' }}) this.style.background='#1a3327'; this.style.color='#f4f1e8';"
                   onmouseout="if(!{{ request()->routeIs($item['route']) ? 'true' : 'false' }}) { this.style.background=''; this.style.color='#8aab97'; }">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="px-5 py-4 space-y-2 text-xs" style="border-top:1px solid #2d5240; color:#4a5e55;">
            <a href="{{ route('dashboard') }}" class="block transition" style="color:#4a5e55;"
               onmouseover="this.style.color='#f4f1e8'" onmouseout="this.style.color='#4a5e55'">← Back to app</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="transition" style="color:#4a5e55; background:none; border:none; padding:0; cursor:pointer; font-size:inherit;"
                        onmouseover="this.style.color='#f4f1e8'" onmouseout="this.style.color='#4a5e55'">
                    Log out
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col overflow-auto">
        <header class="px-6 py-4 flex items-center justify-between" style="background:white; border-bottom:1px solid #e6e0d0;">
            <h1 class="text-lg font-black" style="color:#1a3327;">
                @yield('title', 'Admin Panel')
            </h1>
            <div class="flex items-center gap-4">
                <span class="text-sm" style="color:#6b7a72;">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm font-semibold px-3 py-1 rounded-lg transition"
                            style="border:1.5px solid #1a3327; color:#1a3327; background:transparent;"
                            onmouseover="this.style.background='#1a3327'; this.style.color='#f4f1e8';"
                            onmouseout="this.style.background='transparent'; this.style.color='#1a3327';">
                        Log out
                    </button>
                </form>
            </div>
        </header>

        <main class="flex-1 p-6" style="background:#f4f1e8;">
            @if (session('status'))
                <div class="mb-4 px-4 py-3 rounded-xl text-sm" style="background:rgba(196,154,60,.1); border:1px solid rgba(196,154,60,.3); color:#8a6a1a;">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 px-4 py-3 rounded-xl text-sm" style="background:#fff0f0; border:1px solid #fca5a5; color:#dc2626;">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

</body>
</html>
