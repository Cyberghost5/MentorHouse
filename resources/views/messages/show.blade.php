@php
    $userId = auth()->id();
    $other  = $conversation->mentor_id === $userId
        ? $conversation->mentee
        : $conversation->mentor;
    $initials = collect(explode(' ', $other->name))->take(2)->map(fn($w) => strtoupper($w[0]))->implode('');
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold" style="background:#1a3327; color:#f4f1e8;">
                {{ $initials }}
            </div>
            <h2 class="font-black text-xl" style="color:#1a3327;">{{ $other->name }}</h2>
            <a href="{{ route('messages.index') }}" class="ml-auto text-sm transition" style="color:#4a5e55;"
               onmouseover="this.style.color='#1a3327'" onmouseout="this.style.color='#4a5e55'">← All conversations</a>
        </div>
    </x-slot>

    <div class="py-10 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <livewire:pages.messages.show :conversation="$conversation" />
    </div>
</x-app-layout>
