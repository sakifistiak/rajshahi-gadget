<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        // Closed conversations must not keep the global sidebar alert ringing.
        // Only unread customer messages in currently open conversations need
        // an admin/agent notification.
        $count = ChatMessage::where('sender_type', 'customer')
            ->whereNull('read_at')
            ->whereHas('conversation', fn ($query) => $query->where('status', 'open'))
            ->count();

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
        $conversation->load(['messages.sender', 'messages.replyTo.sender', 'assignedAgent']);

        return view('admin.live-chat.show', compact('conversation'));
    }

    public function messages(ChatConversation $conversation)
    {
        $conversation->messages()->where('sender_type', 'customer')->whereNull('read_at')->update(['read_at' => now()]);

        return response()->json([
            'conversation' => $conversation->only(['status', 'closed_by']),
            'messages' => $conversation->messages()->with(['sender:id,name', 'replyTo.sender:id,name'])->oldest()->get()->map(function ($message) use ($conversation) {
                $replyTo = null;
                if ($message->replyTo) {
                    $origSender = $message->replyTo->sender_type === 'customer'
                        ? ($conversation->customer_name ?: 'Customer')
                        : ($message->replyTo->sender?->name ?: 'Agent');
                    $replyTo = [
                        'id' => $message->replyTo->id,
                        'body' => Str::limit($message->replyTo->body, 90),
                        'sender_type' => $message->replyTo->sender_type,
                        'sender_name' => $origSender,
                    ];
                }

                return [
                    'id' => $message->id,
                    'body' => $message->body,
                    'sender_type' => $message->sender_type,
                    'sender' => $message->sender ? ['name' => $message->sender->name] : null,
                    'created_at' => $message->created_at?->toIso8601String(),
                    'reply_to' => $replyTo,
                ];
            }),
        ]);
    }

    public function send(Request $request, ChatConversation $conversation)
    {
        $data = $request->validate([
            'body' => 'required|string|max:2000',
            'reply_to_id' => 'nullable|integer|exists:chat_messages,id',
        ]);

        if ($conversation->status !== 'open') {
            return response()->json(['error' => 'closed'], 422);
        }

        $replyToId = null;
        if (! empty($data['reply_to_id'])) {
            $valid = ChatMessage::where('id', $data['reply_to_id'])
                ->where('conversation_id', $conversation->id)
                ->exists();
            if ($valid) {
                $replyToId = (int) $data['reply_to_id'];
            }
        }

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'reply_to_id' => $replyToId,
            'sender_type' => 'agent',
            'sender_id' => $request->user()->id,
            'body' => trim($data['body']),
        ]);
        $conversation->update(['last_message_at' => now()]);

        if ($request->wantsJson()) {
            $message->load(['sender:id,name', 'replyTo.sender:id,name']);

            $replyTo = null;
            if ($message->replyTo) {
                $origSender = $message->replyTo->sender_type === 'customer'
                    ? ($conversation->customer_name ?: 'Customer')
                    : ($message->replyTo->sender?->name ?: 'Agent');
                $replyTo = [
                    'id' => $message->replyTo->id,
                    'body' => Str::limit($message->replyTo->body, 90),
                    'sender_type' => $message->replyTo->sender_type,
                    'sender_name' => $origSender,
                ];
            }

            return response()->json(['message' => [
                'id' => $message->id,
                'body' => $message->body,
                'sender_type' => $message->sender_type,
                'sender' => $message->sender ? ['name' => $message->sender->name] : null,
                'created_at' => $message->created_at?->toIso8601String(),
                'reply_to' => $replyTo,
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
