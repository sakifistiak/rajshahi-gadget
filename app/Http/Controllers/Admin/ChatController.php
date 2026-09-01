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
        ChatConversation::autoCloseStale();

        $conversations = ChatConversation::with('assignedAgent')
            ->where('status', 'open')
            ->withCount(['messages as unread_count' => fn ($q) => $q->where('sender_type', 'customer')->whereNull('read_at')])
            ->latest('last_message_at')
            ->get();

        $openCount = $conversations->count();
        $closedCount = ChatConversation::where('status', 'closed')->count();

        return view('admin.live-chat.index', compact('conversations', 'openCount', 'closedCount'));
    }

    public function unreadCount()
    {
        $count = ChatMessage::where('sender_type', 'customer')->whereNull('read_at')->count();

        return response()->json(['count' => $count]);
    }

    public function list(Request $request)
    {
        ChatConversation::autoCloseStale();

        $status = $request->query('status') === 'closed' ? 'closed' : 'open';

        $conversations = ChatConversation::with('assignedAgent')
            ->where('status', $status)
            ->withCount(['messages as unread_count' => fn ($q) => $q->where('sender_type', 'customer')->whereNull('read_at')])
            ->when($status === 'open', fn ($q) => $q->orderByDesc('last_message_at'))
            ->when($status === 'closed', fn ($q) => $q->orderByDesc('closed_at'))
            ->get()
            ->map(fn (ChatConversation $c) => [
                'id' => $c->id,
                'customer_name' => $c->customer_name,
                'customer_phone' => $c->customer_phone,
                'unread_count' => $c->unread_count,
                'status' => $c->status,
                'closed_by' => $c->closed_by,
                'time' => ($status === 'closed' ? ($c->closed_at ?? $c->last_message_at ?? $c->created_at) : ($c->last_message_at ?? $c->created_at))->diffForHumans(),
                'url' => route('admin.live-chat.show', $c),
                'closeUrl' => route('admin.live-chat.close', $c),
                'deleteUrl' => route('admin.live-chat.destroy', $c),
            ]);

        return response()->json([
            'status' => $status,
            'conversations' => $conversations,
            'openCount' => $status === 'open' ? $conversations->count() : ChatConversation::where('status', 'open')->count(),
            'closedCount' => $status === 'closed' ? $conversations->count() : ChatConversation::where('status', 'closed')->count(),
        ]);
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

        return response()->json([
            'conversation' => $conversation->only(['status', 'closed_by']),
            'messages' => $conversation->messages()->with('sender:id,name')->oldest()->get(),
        ]);
    }

    public function send(Request $request, ChatConversation $conversation)
    {
        $data = $request->validate(['body' => 'required|string|max:2000']);

        if ($conversation->status !== 'open') {
            return response()->json(['error' => 'closed'], 422);
        }

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'agent',
            'sender_id' => $request->user()->id,
            'body' => trim($data['body']),
        ]);
        $conversation->update(['last_message_at' => now()]);

        if ($request->wantsJson()) {
            $message->load('sender:id,name');

            return response()->json(['message' => [
                'id' => $message->id,
                'body' => $message->body,
                'sender_type' => $message->sender_type,
                'sender' => $message->sender ? ['name' => $message->sender->name] : null,
                'created_at' => $message->created_at?->toIso8601String(),
            ]]);
        }

        return redirect()->route('admin.live-chat.show', $conversation);
    }

    public function close(Request $request, ChatConversation $conversation)
    {
        $conversation->close('agent');

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'status' => $conversation->status]);
        }

        return redirect()->route('admin.live-chat.index')->with('success', 'Conversation closed.');
    }

    public function destroy(ChatConversation $conversation)
    {
        $conversation->delete();

        return redirect()->route('admin.live-chat.index')->with('success', 'Conversation deleted.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:chat_conversations,id',
        ]);

        ChatConversation::whereIn('id', $request->input('ids'))->delete();

        return redirect()->route('admin.live-chat.index')->with('success', 'Selected conversations deleted.');
    }
}
