<x-app-layout>
<div class="w-full space-y-5">
    <div class="flex items-center justify-between rounded border border-slate-200 bg-white p-5">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Live Chat Inbox</h2>
            <p class="mt-1 text-xs text-slate-500">Customer conversations shared by admins and chat agents.</p>
        </div>
        @if(Auth::user()->is_admin)
            <a href="{{ route('admin.live-chat.agents.index') }}" class="rounded bg-blue-600 px-4 py-2 text-xs font-bold text-white">Manage Agents</a>
        @endif
    </div>

    <div class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm">
        <div id="live-chat-list" class="divide-y divide-slate-100">
            @forelse($conversations as $conversation)
                <a href="{{ route('admin.live-chat.show', $conversation) }}" class="flex items-center justify-between gap-4 p-4 hover:bg-slate-50">
                    <div>
                        <p class="text-sm font-bold text-slate-900">
                            {{ $conversation->customer_name }}
                            @if($conversation->customer_phone)
                                <span class="text-xs font-normal text-slate-400">· {{ $conversation->customer_phone }}</span>
                            @endif
                        </p>
                        <p class="mt-1 text-xs text-slate-500">{{ $conversation->last_message_at?->diffForHumans() ?? $conversation->created_at->diffForHumans() }}</p>
                    </div>
                    @if($conversation->unread_count)
                        <span class="rounded-full bg-rose-600 px-2 py-1 text-[10px] font-bold text-white">{{ $conversation->unread_count }} new</span>
                    @endif
                </a>
            @empty
                <div id="live-chat-list-empty" class="p-10 text-center text-sm text-slate-400">No open conversations yet.</div>
            @endforelse
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var list = document.getElementById('live-chat-list');

    function esc(s) { return String(s).replace(/[&<>]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[c])); }

    function render(conversations) {
        if (!conversations.length) {
            list.innerHTML = '<div id="live-chat-list-empty" class="p-10 text-center text-sm text-slate-400">No open conversations yet.</div>';
            return;
        }
        list.innerHTML = conversations.map(function (c) {
            var phone = c.customer_phone ? '<span class="text-xs font-normal text-slate-400">· ' + esc(c.customer_phone) + '</span>' : '';
            var badge = c.unread_count ? '<span class="rounded-full bg-rose-600 px-2 py-1 text-[10px] font-bold text-white">' + c.unread_count + ' new</span>' : '';
            return '<a href="' + c.url + '" class="flex items-center justify-between gap-4 p-4 hover:bg-slate-50">'
                + '<div><p class="text-sm font-bold text-slate-900">' + esc(c.customer_name) + ' ' + phone + '</p>'
                + '<p class="mt-1 text-xs text-slate-500">' + esc(c.time) + '</p></div>'
                + badge + '</a>';
        }).join('');
    }

    function poll() {
        fetch('{{ route('admin.live-chat.list') }}').then(function (r) { return r.ok ? r.json() : null; }).then(function (d) {
            if (d) render(d.conversations);
        }).catch(function () {});
    }
    setInterval(poll, 5000);
});
</script>
</x-app-layout>
