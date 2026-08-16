<x-app-layout>
<div class="w-full space-y-5">
    <div class="rounded border border-slate-200 bg-white p-5">
        <h2 class="text-xl font-bold text-slate-900">Live Chat Agents</h2>
        <p class="mt-1 text-xs text-slate-500">Only administrators can create or disable chat agent access.</p>
    </div>

    @if(session('success'))
        <div class="rounded border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-700">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="rounded border border-rose-200 bg-rose-50 p-3 text-xs text-rose-700 space-y-1">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="grid gap-5 lg:grid-cols-[320px_1fr]">
        <form action="{{ route('admin.live-chat.agents.store') }}" method="POST" class="space-y-3 rounded border border-slate-200 bg-white p-5">
            @csrf
            <h3 class="text-sm font-bold">Create Agent</h3>
            <input name="name" required placeholder="Name" class="w-full rounded border-slate-200 text-sm">
            <input name="email" type="email" required placeholder="Email" class="w-full rounded border-slate-200 text-sm">
            <input name="password" type="password" required minlength="8" placeholder="Password" class="w-full rounded border-slate-200 text-sm">
            <button class="w-full rounded bg-blue-600 px-4 py-2 text-xs font-bold text-white">Create Agent</button>
        </form>

        <div class="rounded border border-slate-200 bg-white p-5">
            <h3 class="mb-3 text-sm font-bold">Agents</h3>
            <div class="divide-y divide-slate-100">
                @forelse($agents as $agent)
                    <div class="flex items-center justify-between py-3">
                        <div>
                            <p class="text-sm font-semibold">{{ $agent->name }}</p>
                            <p class="text-xs text-slate-400">{{ $agent->email }}</p>
                        </div>
                        <form action="{{ route('admin.live-chat.agents.toggle', $agent) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button class="rounded px-3 py-1.5 text-xs font-bold {{ $agent->is_live_chat_agent ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $agent->is_live_chat_agent ? 'Active' : 'Disabled' }}
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="py-5 text-sm text-slate-400">No agents yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
</x-app-layout>
