@php
    $user = auth()->user();
    $userId = $user->id;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-xl" style="color:#1a3327;">Messages</h2>
    </x-slot>

    <div class="py-10 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        @if ($conversations->isEmpty())
            <div class="rounded-2xl p-10 text-center" style="background:white; border:1px solid #e6e0d0;">
                <p class="text-sm" style="color:#6b7a72;">You have no conversations yet. Conversations are created automatically when a session request is accepted.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($conversations as $conversation)
                    @php
                        $other = $conversation->mentor_id === $userId
                            ? $conversation->mentee
                            : $conversation->mentor;
                        $lastMsg = $conversation->messages->first();
                        $unread  = $conversation->unreadCountFor($userId);
                        $initials = collect(explode(' ', $other->name))->take(2)->map(fn($w) => strtoupper($w[0]))->implode('');
                    @endphp
                    <a href="{{ route('messages.show', $conversation) }}"
                       class="flex items-center gap-4 rounded-2xl px-5 py-4 transition"
                       style="background:white; border:1px solid #e6e0d0;"
                       onmouseover="this.style.borderColor='#2d5240'" onmouseout="this.style.borderColor='#e6e0d0'">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold" style="background:#1a3327; color:#f4f1e8;">
                            {{ $initials }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold truncate" style="color:#1a3327;">{{ $other->name }}</p>
                            @if ($lastMsg)
                                <p class="text-sm truncate" style="color:#6b7a72;">{{ $lastMsg->body }}</p>
                            @else
                                <p class="text-sm italic" style="color:#6b7a72;">No messages yet</p>
                            @endif
                        </div>
                        @if ($unread > 0)
                            <span class="flex-shrink-0 inline-flex items-center justify-center h-5 w-5 rounded-full text-xs font-bold" style="background:#c49a3c; color:#1a3327;">
                                {{ $unread > 9 ? '9+' : $unread }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
