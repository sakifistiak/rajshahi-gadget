<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function start(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:30',
        ]);

        $conversation = ChatConversation::where('customer_token', $request->session()->get('chat_token'))
            ->where('status', 'open')
            ->first();

        if (! $conversation) {
            $conversation = ChatConversation::create([
                'customer_token' => (string) Str::uuid(),
                'customer_name' => $data['name'],
                'customer_phone' => $data['phone'] ?? null,
                'status' => 'open',
            ]);
            $request->session()->put('chat_token', $conversation->customer_token);
        }

        return response()->json($this->payload($conversation));
    }

    public function messages(Request $request)
    {
        $conversation = ChatConversation::where('customer_token', $request->session()->get('chat_token'))->firstOrFail();

        return response()->json($this->payload($conversation));
    }

    public function send(Request $request)
    {
        $data = $request->validate(['body' => 'required|string|max:2000']);

        $conversation = ChatConversation::where('customer_token', $request->session()->get('chat_token'))
            ->where('status', 'open')
            ->firstOrFail();

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'customer',
            'body' => trim($data['body']),
        ]);
        $conversation->update(['last_message_at' => now()]);

        return $this->messages($request);
    }

    private function payload(ChatConversation $conversation): array
    {
        return [
            'conversation' => $conversation->only(['customer_name', 'status']),
            'messages' => $conversation->messages()->with('sender:id,name')->oldest()->get()->map(fn ($message) => [
                'id' => $message->id,
                'body' => $message->body,
                'sender_type' => $message->sender_type,
                'sender_name' => $message->sender?->name,
                'created_at' => $message->created_at?->toIso8601String(),
            ]),
        ];
    }
}
