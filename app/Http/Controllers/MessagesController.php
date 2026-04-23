<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessagesController extends Controller
{
    /**
     * List all conversations for the authenticated user.
     */
    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        $conversations = Conversation::with(['mentor', 'mentee', 'messages' => function ($q) {
            $q->latest()->limit(1);
        }])
            ->where('mentor_id', $userId)
            ->orWhere('mentee_id', $userId)
            ->latest()
            ->get();

        return view('messages.index', compact('conversations'));
    }

    /**
     * Show a single conversation (Livewire handles the chat UI).
     */
    public function show(Request $request, Conversation $conversation): View
    {
        $userId = $request->user()->id;

        abort_unless(
            $conversation->mentor_id === $userId || $conversation->mentee_id === $userId,
            403
        );

        return view('messages.show', compact('conversation'));
    }

    /**
     * Send a message in a conversation.
     */
    public function store(Request $request, Conversation $conversation): RedirectResponse
    {
        $userId = $request->user()->id;

        abort_unless(
            $conversation->mentor_id === $userId || $conversation->mentee_id === $userId,
            403
        );

        $request->validate([
            'body' => ['required', 'string', 'max:4000'],
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $userId,
            'body'            => $request->input('body'),
        ]);

        return back();
    }
}
