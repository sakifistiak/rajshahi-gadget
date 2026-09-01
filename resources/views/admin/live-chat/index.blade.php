<x-app-layout>
<div class="w-full space-y-5">
    @if (session('success'))
        <div class="flex items-center gap-2 rounded border border-emerald-200 bg-emerald-50 p-3 text-xs font-semibold text-emerald-700 shadow-sm">
            <i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-600"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="flex items-center justify-between rounded border border-slate-200 bg-white p-5">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Live Chat Inbox</h2>
            <p class="mt-1 text-xs text-slate-500">Customer conversations shared by admins and chat agents.</p>
        </div>
        @if(Auth::user()->is_admin)
            <a href="{{ route('admin.live-chat.agents.index') }}" class="rounded bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700 transition-colors">Manage Agents</a>
        @endif
    </div>

    <div x-data="{ selected: [], tab: 'open' }" class="overflow-hidden rounded border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center gap-1 border-b border-slate-100 bg-slate-50/60 px-4 pt-3">
            <button type="button" @click="tab = 'open'; selected = []; window.liveChatSetTab('open')"
                    :class="tab === 'open' ? 'border-blue-600 text-blue-700' : 'border-transparent text-slate-500 hover:text-slate-700'"
                    class="flex items-center gap-1.5 border-b-2 px-3 pb-2.5 text-xs font-bold transition-colors">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                Active
                <span id="live-chat-open-count" class="rounded-full bg-slate-200 px-1.5 py-0.5 text-[10px] text-slate-600">{{ $openCount }}</span>
            </button>
            <button type="button" @click="tab = 'closed'; selected = []; window.liveChatSetTab('closed')"
                    :class="tab === 'closed' ? 'border-blue-600 text-blue-700' : 'border-transparent text-slate-500 hover:text-slate-700'"
                    class="flex items-center gap-1.5 border-b-2 px-3 pb-2.5 text-xs font-bold transition-colors">
                <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                Closed
                <span id="live-chat-closed-count" class="rounded-full bg-slate-200 px-1.5 py-0.5 text-[10px] text-slate-600">{{ $closedCount }}</span>
            </button>
        </div>

        @if(Auth::user()->is_admin)
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 bg-slate-50/60 px-4 py-3">
                <label class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                    <input type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                           :checked="selected.length > 0 && selected.length === window.liveChatIds.length"
                           @change="selected = $event.target.checked ? window.liveChatIds.slice() : []">
                    Select all
                </label>
                <form action="{{ route('admin.live-chat.bulk-destroy') }}" method="POST" onsubmit="return confirm('Delete the selected conversation(s)? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <button type="submit" x-show="selected.length > 0" x-cloak
                            class="inline-flex items-center gap-1.5 rounded bg-red-600 px-3 py-1.5 text-[11px] font-bold uppercase text-white shadow-sm transition-colors hover:bg-red-700">
                        <i data-lucide="trash-2" class="h-3.5 w-3.5"></i>
                        <span x-text="`Delete Selected (${selected.length})`"></span>
                    </button>
                </form>
            </div>
        @endif

        <div id="live-chat-list" class="divide-y divide-slate-100">
            @forelse($conversations as $conversation)
                <div class="flex items-center gap-3 p-4 hover:bg-slate-50 transition-colors" data-conversation-row>
                    @if(Auth::user()->is_admin)
                        <input type="checkbox" value="{{ $conversation->id }}" x-model.number="selected" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 shrink-0">
                    @endif
                    <a href="{{ route('admin.live-chat.show', $conversation) }}" class="flex min-w-0 flex-1 items-center gap-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold uppercase text-white">
                            {{ Str::substr($conversation->customer_name, 0, 2) }}
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-slate-900">
                                {{ $conversation->customer_name }}
                                @if($conversation->customer_phone)
                                    <span class="text-xs font-normal text-slate-400">· {{ $conversation->customer_phone }}</span>
                                @endif
                            </p>
                            <p class="mt-1 text-xs text-slate-500">{{ $conversation->last_message_at?->diffForHumans() ?? $conversation->created_at->diffForHumans() }}</p>
                        </div>
                    </a>
                    @if($conversation->unread_count)
                        <span class="shrink-0 rounded-full bg-rose-600 px-2 py-1 text-[10px] font-bold text-white">{{ $conversation->unread_count }} new</span>
                    @endif
                    <form action="{{ route('admin.live-chat.close', $conversation) }}" method="POST" onsubmit="return confirm('Close this conversation?');" class="shrink-0">
                        @csrf
                        <button type="submit" title="Close conversation" class="rounded p-1.5 text-slate-400 hover:bg-amber-50 hover:text-amber-600 transition-colors">
                            <i data-lucide="circle-x" class="h-4 w-4"></i>
                        </button>
                    </form>
                    @if(Auth::user()->is_admin)
                        <form action="{{ route('admin.live-chat.destroy', $conversation) }}" method="POST" onsubmit="return confirm('Delete this conversation? This cannot be undone.');" class="shrink-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Delete conversation" class="rounded p-1.5 text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-colors">
                                <i data-lucide="trash-2" class="h-4 w-4"></i>
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div id="live-chat-list-empty" class="p-10 text-center text-sm text-slate-400">No open conversations yet.</div>
            @endforelse
        </div>
    </div>
</div>

<script>
window.liveChatIds = @json($conversations->pluck('id'));

document.addEventListener('DOMContentLoaded', function () {
    var list = document.getElementById('live-chat-list');
    var isAdmin = {{ Auth::user()->is_admin ? 'true' : 'false' }};
    var currentTab = 'open';

    function esc(s) { return String(s).replace(/[&<>]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[c])); }

    function render(conversations, status) {
        window.liveChatIds = conversations.map(function (c) { return c.id; });

        if (!conversations.length) {
            list.innerHTML = '<div id="live-chat-list-empty" class="p-10 text-center text-sm text-slate-400">'
                + (status === 'closed' ? 'No closed conversations yet.' : 'No open conversations yet.') + '</div>';
            return;
        }
        list.innerHTML = conversations.map(function (c) {
            var phone = c.customer_phone ? '<span class="text-xs font-normal text-slate-400">· ' + esc(c.customer_phone) + '</span>' : '';
            var badge = c.unread_count ? '<span class="shrink-0 rounded-full bg-rose-600 px-2 py-1 text-[10px] font-bold text-white">' + c.unread_count + ' new</span>' : '';
            var checkbox = isAdmin ? '<input type="checkbox" value="' + c.id + '" x-model.number="selected" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 shrink-0">' : '';
            var closeBtn = status === 'open'
                ? '<form action="' + c.closeUrl + '" method="POST" onsubmit="return confirm(\'Close this conversation?\');" class="shrink-0">'
                    + '{!! csrf_field() !!}'
                    + '<button type="submit" title="Close conversation" class="rounded p-1.5 text-slate-400 hover:bg-amber-50 hover:text-amber-600 transition-colors"><i data-lucide="circle-x" class="h-4 w-4"></i></button>'
                  + '</form>'
                : '<span class="shrink-0 rounded-full bg-slate-100 px-2 py-1 text-[10px] font-bold uppercase text-slate-500">' + (c.closed_by === 'auto' ? 'Auto-closed' : 'Closed') + '</span>';
            var deleteBtn = isAdmin
                ? '<form action="' + c.deleteUrl + '" method="POST" onsubmit="return confirm(\'Delete this conversation? This cannot be undone.\');" class="shrink-0">'
                    + '{!! csrf_field() !!}{!! method_field("DELETE") !!}'
                    + '<button type="submit" title="Delete conversation" class="rounded p-1.5 text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-colors"><i data-lucide="trash-2" class="h-4 w-4"></i></button>'
                  + '</form>'
                : '';
            return '<div class="flex items-center gap-3 p-4 hover:bg-slate-50 transition-colors" data-conversation-row>'
                + checkbox
                + '<a href="' + c.url + '" class="flex min-w-0 flex-1 items-center gap-3">'
                    + '<span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold uppercase text-white">' + esc(c.customer_name.slice(0, 2)) + '</span>'
                    + '<div class="min-w-0"><p class="truncate text-sm font-bold text-slate-900">' + esc(c.customer_name) + ' ' + phone + '</p>'
                    + '<p class="mt-1 text-xs text-slate-500">' + esc(c.time) + '</p></div>'
                + '</a>'
                + badge
                + closeBtn
                + deleteBtn
                + '</div>';
        }).join('');
        if (window.lucide) { window.lucide.createIcons(); }
    }

    function poll() {
        fetch('{{ route('admin.live-chat.list') }}?status=' + currentTab).then(function (r) { return r.ok ? r.json() : null; }).then(function (d) {
            if (!d) return;
            render(d.conversations, d.status);
            var openCountEl = document.getElementById('live-chat-open-count');
            var closedCountEl = document.getElementById('live-chat-closed-count');
            if (openCountEl) openCountEl.textContent = d.openCount;
            if (closedCountEl) closedCountEl.textContent = d.closedCount;
        }).catch(function () {});
    }

    window.liveChatSetTab = function (tab) {
        currentTab = tab;
        poll();
    };

    setInterval(poll, 5000);
});
</script>
</x-app-layout>
