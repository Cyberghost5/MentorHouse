<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }

    public function unreadCount(): int
    {
        if (! auth()->check()) {
            return 0;
        }
        $userId = auth()->id();
        return \App\Models\Message::query()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $userId)
            ->whereHas('conversation', function ($q) use ($userId) {
                $q->where('mentor_id', $userId)->orWhere('mentee_id', $userId);
            })
            ->count();
    }
}; ?>

<nav x-data="{ open: false }" style="background-color:#f4f1e8; border-bottom:1px solid #e6e0d0;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 flex items-center justify-between h-16">

        {{-- Logo --}}
        <a href="{{ url('/') }}" wire:navigate class="flex items-center gap-2.5 shrink-0">
            <span class="w-9 h-9 rounded-xl flex items-center justify-center" style="background-color:#1a3327;">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" style="color:#c49a3c;">
                    <path stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          d="M8 21h8M12 17v4M5 3h14l-1.5 7A5.5 5.5 0 0 1 12 15a5.5 5.5 0 0 1-5.5-5L5 3Z"/>
                </svg>
            </span>
            <span class="font-bold text-xl tracking-tight" style="color:#1a3327;">MentorHouse</span>
        </a>

        {{-- Desktop center nav --}}
        <div class="hidden md:flex items-center gap-8 text-xs font-semibold tracking-widest uppercase">
            <a href="{{ route('mentors.index') }}" wire:navigate
               class="transition-colors"
               style="color:{{ request()->routeIs('mentors.index') ? '#1a3327' : '#6b7a72' }};"
               onmouseover="this.style.color='#1a3327'" onmouseout="this.style.color='{{ request()->routeIs('mentors.index') ? '#1a3327' : '#6b7a72' }}'">
                Mentors
            </a>
            @auth
            @php
                $dashRoute = auth()->user()->isMentor() ? 'mentor.dashboard'
                    : (auth()->user()->isMentee() ? 'mentee.dashboard' : 'admin.dashboard');
                $isDash = request()->routeIs('mentor.dashboard') || request()->routeIs('mentee.dashboard') || request()->routeIs('admin.*');
            @endphp
            <a href="{{ route($dashRoute) }}" wire:navigate
               class="transition-colors"
               style="color:{{ $isDash ? '#1a3327' : '#6b7a72' }};"
               onmouseover="this.style.color='#1a3327'" onmouseout="this.style.color='{{ $isDash ? '#1a3327' : '#6b7a72' }}'">
                Dashboard
            </a>
            <a href="{{ route('session-requests.index') }}" wire:navigate
               class="transition-colors"
               style="color:{{ request()->routeIs('session-requests.*') ? '#1a3327' : '#6b7a72' }};"
               onmouseover="this.style.color='#1a3327'" onmouseout="this.style.color='{{ request()->routeIs('session-requests.*') ? '#1a3327' : '#6b7a72' }}'">
                Sessions
            </a>
            <a href="{{ route('messages.index') }}" wire:navigate
               class="transition-colors"
               style="color:{{ request()->routeIs('messages.*') ? '#1a3327' : '#6b7a72' }};"
               onmouseover="this.style.color='#1a3327'" onmouseout="this.style.color='{{ request()->routeIs('messages.*') ? '#1a3327' : '#6b7a72' }}'">
                <span class="inline-flex items-center gap-1.5">
                    Messages
                    @php $unread = $this->unreadCount(); @endphp
                    @if ($unread > 0)
                        <span wire:poll.5000ms class="inline-flex items-center justify-center h-4 w-4 rounded-full text-xs font-bold leading-none" style="background:#c49a3c; color:#1a3327;">
                            {{ $unread > 9 ? '9+' : $unread }}
                        </span>
                    @endif
                </span>
            </a>
            @endauth
        </div>

        {{-- Desktop right --}}
        <div class="hidden md:flex items-center gap-3">
            @auth
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold transition"
                            style="border:2px solid #1a3327; color:#1a3327; background:transparent;"
                            onmouseover="this.style.background='#1a3327'; this.style.color='#f4f1e8';"
                            onmouseout="this.style.background='transparent'; this.style.color='#1a3327';">
                        <span x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></span>
                        <svg class="ms-1 h-4 w-4 fill-current" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </x-slot>
                <x-slot name="content">
                    <x-dropdown-link :href="route('profile')" wire:navigate>Profile</x-dropdown-link>
                    <button wire:click="logout" class="w-full text-start">
                        <x-dropdown-link>Log Out</x-dropdown-link>
                    </button>
                </x-slot>
            </x-dropdown>
            @else
            <a href="{{ route('login') }}" class="text-sm font-semibold px-5 py-2 rounded-lg border-2 transition"
               style="border-color:#1a3327; color:#1a3327;"
               onmouseover="this.style.background='#1a3327'; this.style.color='#f4f1e8';"
               onmouseout="this.style.background='transparent'; this.style.color='#1a3327';">Sign In</a>
            <a href="{{ route('register') }}" class="text-sm font-bold px-5 py-2 rounded-lg transition"
               style="background:#1a3327; color:#f4f1e8;"
               onmouseover="this.style.background='#0f2219';" onmouseout="this.style.background='#1a3327';">Get started</a>
            @endauth
        </div>

        {{-- Hamburger --}}
        <div class="-me-2 flex items-center md:hidden">
            <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md transition" style="color:#4a5e55;">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden md:hidden" style="border-top:1px solid #e6e0d0; background:#f4f1e8;">
        <div class="px-4 pt-3 pb-2 space-y-1">
            <a href="{{ route('mentors.index') }}" wire:navigate class="block px-3 py-2 rounded-lg text-sm font-semibold transition" style="color:#4a5e55;" onmouseover="this.style.color='#1a3327'" onmouseout="this.style.color='#4a5e55'">Mentors</a>
            @auth
            @php
                $dashRouteMobile = auth()->user()->isMentor() ? 'mentor.dashboard'
                    : (auth()->user()->isMentee() ? 'mentee.dashboard' : 'admin.dashboard');
            @endphp
            <a href="{{ route($dashRouteMobile) }}" wire:navigate class="block px-3 py-2 rounded-lg text-sm font-semibold transition" style="color:#4a5e55;" onmouseover="this.style.color='#1a3327'" onmouseout="this.style.color='#4a5e55'">Dashboard</a>
            <a href="{{ route('session-requests.index') }}" wire:navigate class="block px-3 py-2 rounded-lg text-sm font-semibold transition" style="color:#4a5e55;" onmouseover="this.style.color='#1a3327'" onmouseout="this.style.color='#4a5e55'">Sessions</a>
            <a href="{{ route('messages.index') }}" wire:navigate class="block px-3 py-2 rounded-lg text-sm font-semibold transition" style="color:#4a5e55;" onmouseover="this.style.color='#1a3327'" onmouseout="this.style.color='#4a5e55'">Messages</a>
            @endauth
        </div>

        @auth
        <div class="px-4 pt-3 pb-3 space-y-1" style="border-top:1px solid #e6e0d0;">
            <div class="px-3 py-1">
                <p class="text-sm font-semibold" style="color:#1a3327;" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></p>
                <p class="text-xs" style="color:#6b7a72;">{{ auth()->user()->email }}</p>
            </div>
            <a href="{{ route('profile') }}" wire:navigate class="block px-3 py-2 rounded-lg text-sm font-semibold transition" style="color:#4a5e55;" onmouseover="this.style.color='#1a3327'" onmouseout="this.style.color='#4a5e55'">Profile</a>
            <button wire:click="logout" class="w-full text-start block px-3 py-2 rounded-lg text-sm font-semibold transition" style="color:#4a5e55;" onmouseover="this.style.color='#1a3327'" onmouseout="this.style.color='#4a5e55'">Log Out</button>
        </div>
        @else
        <div class="px-4 pt-3 pb-3 flex gap-3" style="border-top:1px solid #e6e0d0;">
            <a href="{{ route('login') }}" wire:navigate class="text-sm font-semibold px-5 py-2 rounded-lg border-2 transition"
               style="border-color:#1a3327; color:#1a3327;">Sign In</a>
            <a href="{{ route('register') }}" wire:navigate class="text-sm font-bold px-5 py-2 rounded-lg"
               style="background:#1a3327; color:#f4f1e8;">Get started</a>
        </div>
        @endauth
    </div>
</nav>
