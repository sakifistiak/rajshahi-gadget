<x-app-layout>
<div class="w-full">
    <a href="{{ route('admin.live-chat.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 hover:text-slate-700">
        <i data-lucide="arrow-left" class="h-3.5 w-3.5"></i>
        Back to inbox
    </a>

    @php
        $initialMessages = $conversation->messages->map(fn ($m) => [
            'id' => $m->id,
            'body' => $m->body,
            'sender_type' => $m->sender_type,
            'sender' => $m->sender ? ['name' => $m->sender->name] : null,
            'created_at' => $m->created_at?->toIso8601String(),
        ]);
    @endphp

    <div class="mt-4 flex h-[75vh] flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="flex shrink-0 items-center justify-between border-b border-slate-100 bg-white px-5 py-4">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold uppercase text-white ring-4 ring-blue-50">
                    {{ Str::substr($conversation->customer_name, 0, 2) }}
                </span>
                <div>
                    <h2 class="text-base font-bold text-slate-900">{{ $conversation->customer_name }}</h2>
                    <p class="mt-0.5 flex items-center gap-1.5 text-xs text-slate-500">
                        <i data-lucide="phone" class="h-3 w-3"></i>
                        {{ $conversation->customer_phone ?: 'No phone provided' }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span id="admin-chat-status" class="inline-flex items-center gap-1.5 rounded-full {{ $conversation->status === 'open' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }} px-3 py-1 text-xs font-bold">
                    <span class="h-1.5 w-1.5 rounded-full {{ $conversation->status === 'open' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                    {{ ucfirst($conversation->status) }}
                </span>
                @if($conversation->status === 'open')
                    <form id="admin-chat-close-form" action="{{ route('admin.live-chat.close', $conversation) }}" method="POST" onsubmit="return confirm('Close this conversation?');">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 px-3 py-1 text-xs font-bold text-slate-500 transition-colors hover:border-amber-300 hover:text-amber-600">
                            <i data-lucide="circle-x" class="h-3.5 w-3.5"></i>
                            Close
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div id="admin-chat-messages" class="flex-1 space-y-4 overflow-y-auto bg-[#f4f6fb] p-5"></div>

        <form id="admin-chat-form" class="flex shrink-0 items-end gap-2 border-t border-slate-100 bg-white p-3 sm:p-4" @if($conversation->status !== 'open') style="display:none" @endif>
            @csrf
            <textarea id="admin-chat-input" name="body" required maxlength="2000" rows="1" placeholder="Write a reply... (Enter to send, Shift+Enter for new line)" class="min-w-0 flex-1 resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm leading-relaxed focus:border-blue-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-400 max-h-36 overflow-y-auto"></textarea>
            <button id="admin-chat-submit" type="submit" title="Send reply (Enter)" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-600 text-white transition-colors hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60 mb-0.5">
                <i data-lucide="send" class="h-4 w-4"></i>
            </button>
        </form>
        <div id="admin-chat-closed-note" class="shrink-0 border-t border-slate-100 bg-slate-50 p-3 text-center text-xs font-semibold text-slate-400 sm:p-4" @if($conversation->status === 'open') style="display:none" @endif>
            This conversation is closed.
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let box = document.getElementById('admin-chat-messages');
    let form = document.getElementById('admin-chat-form');
    let input = document.getElementById('admin-chat-input');
    let submitBtn = document.getElementById('admin-chat-submit');
    let customerName = @json($conversation->customer_name);
    let sendUrl = '{{ route('admin.live-chat.messages.send', $conversation) }}';
    let messagesUrl = '{{ route('admin.live-chat.messages', $conversation) }}';
    let token = document.querySelector('input[name="_token"]').value;
    let messages = @json($initialMessages);
    let status = @json($conversation->status);

    function applyClosedState() {
        let statusEl = document.getElementById('admin-chat-status');
        let closeForm = document.getElementById('admin-chat-close-form');
        let note = document.getElementById('admin-chat-closed-note');
        if (statusEl) {
            statusEl.className = 'inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold ' + (status === 'open' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500');
            statusEl.innerHTML = '<span class="h-1.5 w-1.5 rounded-full ' + (status === 'open' ? 'bg-emerald-500' : 'bg-slate-400') + '"></span>' + (status.charAt(0).toUpperCase() + status.slice(1));
        }
        if (closeForm) closeForm.style.display = status === 'open' ? '' : 'none';
        form.style.display = status === 'open' ? '' : 'none';
        if (note) note.style.display = status === 'open' ? 'none' : '';
    }

    function esc(s) { return String(s).replace(/[&<>]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[c])); }
    function fmt(iso) {
        let d = new Date(iso);
        let hh = String(d.getHours()).padStart(2, '0'), mm = String(d.getMinutes()).padStart(2, '0');
        return hh + ':' + mm;
    }

    function autoResize() {
        if (!input) return;
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 140) + 'px';
    }

    input.addEventListener('input', autoResize);

    // Enter sends the message; Shift+Enter creates a new line (line break)
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.requestSubmit ? form.requestSubmit() : form.dispatchEvent(new Event('submit', { cancelable: true }));
        }
    });

    function groupMessages(list) {
        let groups = [];
        list.forEach(function (m) {
            let name = m.sender_type === 'agent' ? (m.sender && m.sender.name ? m.sender.name : 'Agent') : customerName;
            let last = groups[groups.length - 1];
            let sameSender = last && last.sender_type === m.sender_type && last.name === name;
            let lastItem = last ? last.items[last.items.length - 1] : null;
            let closeInTime = lastItem && (new Date(m.created_at) - new Date(lastItem.created_at)) < 5 * 60 * 1000;
            if (sameSender && closeInTime) {
                last.items.push(m);
            } else {
                groups.push({ sender_type: m.sender_type, name: name, items: [m] });
            }
        });
        return groups;
    }

    function render() {
        if (!messages.length) {
            box.innerHTML = '<p class="py-10 text-center text-xs text-slate-400">No messages yet. Say hello!</p>';
            return;
        }
        let groups = groupMessages(messages);
        box.innerHTML = groups.map(function (g) {
            let isAgent = g.sender_type === 'agent';
            let bubbles = g.items.map(function (m, idx) {
                let isLast = idx === g.items.length - 1;
                return '<div class="max-w-[70%] rounded-2xl px-4 py-2.5 text-sm shadow-sm ' + (isAgent ? 'rounded-br-sm bg-blue-600 text-white' : 'rounded-bl-sm border border-slate-200 bg-white text-slate-800') + '">'
                    + '<p class="whitespace-pre-wrap break-words leading-relaxed">' + esc(m.body) + '</p>'
                    + (isLast ? '<p class="mt-1 text-[10px] ' + (isAgent ? 'text-blue-100' : 'text-slate-400') + '">' + fmt(m.created_at) + '</p>' : '')
                    + '</div>';
            }).join('<div class="h-1"></div>');
            return '<div class="flex flex-col gap-1 ' + (isAgent ? 'items-end' : 'items-start') + '">'
                + '<span class="px-1 text-[10px] font-semibold text-slate-400">' + esc(g.name) + '</span>'
                + bubbles
                + '</div>';
        }).join('');
        box.scrollTop = box.scrollHeight;
    }

    render();

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        let body = input.value.trim();
        if (!body) return;

        submitBtn.disabled = true;
        input.disabled = true;

        fetch(sendUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({ body: body }),
        })
            .then(function (r) { return r.ok ? r.json() : Promise.reject(r); })
            .then(function (d) {
                messages.push(d.message);
                render();
                input.value = '';
                autoResize();
            })
            .catch(function (r) {
                if (r && r.status === 422) {
                    status = 'closed';
                    applyClosedState();
                } else {
                    alert('Could not send the message. Please try again.');
                }
            })
            .finally(function () {
                submitBtn.disabled = false;
                input.disabled = false;
                input.focus();
            });
    });

    setInterval(function () {
        fetch(messagesUrl).then(function (r) { return r.json(); }).then(function (d) {
            messages = d.messages;
            render();
            if (d.conversation && d.conversation.status !== status) {
                status = d.conversation.status;
                applyClosedState();
            }
        });
    }, 4000);
});
</script>
</x-app-layout>
