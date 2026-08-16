<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $conversations = ChatConversation::with('assignedAgent')
            ->where('status', 'open')
            ->withCount(['messages as unread_count' => fn ($q) => $q->where('sender_type', 'customer')->whereNull('read_at')])
            ->latest('last_message_at')
            ->get();

        return view('admin.live-chat.index', compact('conversations'));
    }

    public function unreadCount()
    {
        $count = ChatMessage::where('sender_type', 'customer')->whereNull('read_at')->count();

        return response()->json(['count' => $count]);
    }

    public function list()
    {
        $conversations = ChatConversation::with('assignedAgent')
            ->where('status', 'open')
            ->withCount(['messages as unread_count' => fn ($q) => $q->where('sender_type', 'customer')->whereNull('read_at')])
            ->latest('last_message_at')
            ->get()
            ->map(fn (ChatConversation $c) => [
                'id' => $c->id,
                'customer_name' => $c->customer_name,
                'customer_phone' => $c->customer_phone,
                'unread_count' => $c->unread_count,
                'time' => ($c->last_message_at ?? $c->created_at)->diffForHumans(),
                'url' => route('admin.live-chat.show', $c),
            ]);

        return response()->json(['conversations' => $conversations]);
    }

    public function show(ChatConversation $conversation)
    {
        $conversation->messages()->where('sender_type', 'customer')->whereNull('read_at')->update(['read_at' => now()]);
        $conversation->load('messages.sender', 'assignedAgent');

        return view('admin.live-chat.show', compact('conversation'));
    }

    public function messages(ChatConversation $conversation)
    {
        $conversation->messages()->where('sender_type', 'customer')->whereNull('read_at')->update(['read_at' => now()]);

        return response()->json(['messages' => $conversation->messages()->with('sender:id,name')->oldest()->get()]);
    }

    public function send(Request $request, ChatConversation $conversation)
    {
        $data = $request->validate(['body' => 'required|string|max:2000']);

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'agent',
            'sender_id' => $request->user()->id,
            'body' => trim($data['body']),
        ]);
        $conversation->update(['last_message_at' => now()]);

        return redirect()->route('admin.live-chat.show', $conversation);
    }
}
