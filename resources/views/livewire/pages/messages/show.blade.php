<?php

use App\Models\Conversation;
use App\Models\Message;
use Livewire\Volt\Component;

new class extends Component
{
    public Conversation $conversation;
    public string $body = '';

    public function mount(Conversation $conversation): void
    {
        $userId = auth()->id();
        abort_unless(
            $conversation->mentor_id === $userId || $conversation->mentee_id === $userId,
            403
        );
        $this->conversation = $conversation;
        $this->markRead();
    }

    public function chatMessages(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->conversation->messages()->with('sender')->oldest()->get();
    }

    public function send(): void
    {
        $this->validate(['body' => 'required|string|max:4000']);

        Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id'       => auth()->id(),
            'body'            => trim($this->body),
        ]);

        $this->body = '';
        $this->markRead();
    }

    public function refresh(): void
    {
        $this->markRead();
    }

    private function markRead(): void
    {
        $this->conversation->markReadFor(auth()->id());
    }
}; ?>

<div wire:poll.3000ms="refresh" class="flex flex-col rounded-2xl overflow-hidden" style="background:white; border:1px solid #e6e0d0; height:70vh;">

    {{-- Message list --}}
    <div
        id="chat-scroll"
        class="flex-1 overflow-y-auto p-5 space-y-4"
        x-data
        x-init="() => { const el = document.getElementById('chat-scroll'); el.scrollTop = el.scrollHeight; }"
        wire:ignore.self
    >
        @php $userId = auth()->id(); @endphp
        @forelse ($this->chatMessages() as $message)
            @php $isMine = $message->sender_id === $userId; @endphp
            <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-xs lg:max-w-md rounded-2xl px-4 py-2.5 text-sm"
                     style="{{ $isMine ? 'background:#1a3327; color:#f4f1e8;' : 'background:#ede9de; color:#1a3327;' }}">
                    @unless($isMine)
                        <p class="text-xs font-semibold mb-1" style="color:#c49a3c;">{{ $message->sender->name }}</p>
                    @endunless
                    <p class="break-words whitespace-pre-wrap">{{ $message->body }}</p>
                    <p class="text-xs mt-1 text-right" style="color:{{ $isMine ? '#8aab97' : '#6b7a72' }};">
                        {{ $message->created_at->format('H:i') }}
                        @if ($isMine && $message->isRead())
                            · <span>Read</span>
                        @endif
                    </p>
                </div>
            </div>
        @empty
            <p class="text-center text-sm pt-10" style="color:#6b7a72;">No messages yet. Say hello!</p>
        @endforelse
    </div>

    {{-- Auto-scroll on new messages --}}
    <script>
        document.addEventListener('livewire:updated', () => {
            const el = document.getElementById('chat-scroll');
            if (el) el.scrollTop = el.scrollHeight;
        });
    </script>

    <div class="px-4 py-3" style="border-top:1px solid #e6e0d0;">
        @if (session('status'))
            <div class="mb-2 text-sm" style="color:#1a3327;">{{ session('status') }}</div>
        @endif
        <form wire:submit.prevent="send" class="flex gap-3">
            <textarea
                wire:model="body"
                rows="2"
                placeholder="Type a message…"
                class="flex-1 rounded-xl px-3 py-2 text-sm resize-none transition"
                style="border:1px solid #d6cfbe; color:#1a3327;"
                onfocus="this.style.borderColor='#1a3327'" onblur="this.style.borderColor='#d6cfbe'"
            ></textarea>
            <button
                type="submit"
                class="self-end px-5 py-2 rounded-xl text-sm font-bold transition"
                style="background:#1a3327; color:#f4f1e8;"
                onmouseover="this.style.background='#0f2219'" onmouseout="this.style.background='#1a3327'"
            >
                Send
            </button>
        </form>
    </div>
</div>
