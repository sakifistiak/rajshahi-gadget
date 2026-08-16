<x-app-layout>
<div class="w-full">
    <a href="{{ route('admin.live-chat.index') }}" class="text-xs font-semibold text-slate-500">← Back to inbox</a>

    <div class="mt-4 overflow-hidden rounded border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 p-5">
            <div>
                <h2 class="text-base font-bold text-slate-900">{{ $conversation->customer_name }}</h2>
                <p class="mt-1 text-xs text-slate-500">{{ $conversation->customer_phone ?: 'No phone provided' }}</p>
            </div>
            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">{{ ucfirst($conversation->status) }}</span>
        </div>

        <div id="admin-chat-messages" class="min-h-[360px] max-h-[60vh] space-y-3 overflow-y-auto bg-slate-50 p-5">
            @foreach($conversation->messages as $message)
                <div class="max-w-[75%] rounded-lg p-3 text-sm {{ $message->sender_type === 'agent' ? 'ml-auto bg-blue-600 text-white' : 'bg-white text-slate-800 border border-slate-200' }}">
                    <p>{{ $message->body }}</p>
                    <p class="mt-1 text-[10px] opacity-60">{{ $message->sender_type === 'agent' ? ($message->sender?->name ?? 'Agent') : $conversation->customer_name }} · {{ $message->created_at->format('M d, H:i') }}</p>
                </div>
            @endforeach
        </div>

        <form action="{{ route('admin.live-chat.messages.send', $conversation) }}" method="POST" class="flex gap-3 border-t border-slate-100 p-4">
            @csrf
            <input name="body" required maxlength="2000" placeholder="Write a reply..." class="min-w-0 flex-1 rounded border border-slate-200 px-3 py-3 text-sm">
            <button class="rounded bg-blue-600 px-5 py-2 text-xs font-bold text-white">Send Reply</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let box = document.getElementById('admin-chat-messages');
    let customerName = @json($conversation->customer_name);
    box.scrollTop = box.scrollHeight;

    function esc(s) { return String(s).replace(/[&<>]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[c])); }
    function fmt(iso) {
        let d = new Date(iso);
        let months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        let hh = String(d.getHours()).padStart(2, '0'), mm = String(d.getMinutes()).padStart(2, '0');
        return months[d.getMonth()] + ' ' + String(d.getDate()).padStart(2, '0') + ', ' + hh + ':' + mm;
    }

    setInterval(() => fetch('{{ route('admin.live-chat.messages', $conversation) }}').then(r => r.json()).then(d => {
        let html = d.messages.map(m =>
            '<div class="max-w-[75%] rounded-lg p-3 text-sm ' + (m.sender_type === 'agent' ? 'ml-auto bg-blue-600 text-white' : 'bg-white text-slate-800 border border-slate-200') + '">'
            + '<p>' + esc(m.body) + '</p>'
            + '<p class="mt-1 text-[10px] opacity-60">' + esc(m.sender_type === 'agent' ? (m.sender && m.sender.name ? m.sender.name : 'Agent') : customerName) + ' · ' + fmt(m.created_at) + '</p>'
            + '</div>'
        ).join('');
        box.innerHTML = html;
        box.scrollTop = box.scrollHeight;
    }), 4000);
});
</script>
</x-app-layout>
