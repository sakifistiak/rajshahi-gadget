<x-app-layout>
    <div class="w-full max-w-4xl space-y-6">
        <div class="flex items-center justify-between p-5 bg-white rounded-sm border border-slate-200 shadow-sm">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Live Chat Settings</h2>
                <p class="text-xs text-slate-500 mt-1">Control the left-side live chat toggle and its contact options.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-sm text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.live-chat-settings.update') }}" method="POST" class="space-y-5">
            @csrf

            <div class="bg-white rounded-sm border border-slate-200 p-5 shadow-sm space-y-5">
                <div class="flex items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Live Chat Widget</h3>
                        <p class="text-xs text-slate-500 mt-1">Show or hide the floating toggle across the public site.</p>
                    </div>
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" name="live_chat_enabled" value="1" class="peer sr-only" {{ old('live_chat_enabled', $settings['live_chat_enabled']) === '1' ? 'checked' : '' }}>
                        <span class="h-6 w-11 rounded-full bg-slate-200 transition peer-checked:bg-blue-600 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5"></span>
                    </label>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div class="rounded-md border border-slate-200 p-4 space-y-3">
                        <div class="flex items-center justify-between"><h3 class="text-sm font-bold text-slate-800">WhatsApp</h3><input type="checkbox" name="live_chat_whatsapp_enabled" value="1" {{ old('live_chat_whatsapp_enabled', $settings['live_chat_whatsapp_enabled']) === '1' ? 'checked' : '' }}></div>
                        <label class="block text-xs font-bold text-slate-700">WhatsApp Number</label>
                        <input type="text" name="live_chat_whatsapp_number" value="{{ old('live_chat_whatsapp_number', $settings['live_chat_whatsapp_number']) }}" inputmode="numeric" placeholder="8801XXXXXXXXX" class="w-full rounded-sm border border-slate-200 px-3 py-2 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <p class="text-[11px] text-slate-400">Use English digits with country code.</p>
                    </div>

                    <div class="rounded-md border border-slate-200 p-4 space-y-3">
                        <div class="flex items-center justify-between"><h3 class="text-sm font-bold text-slate-800">Messenger</h3><input type="checkbox" name="live_chat_messenger_enabled" value="1" {{ old('live_chat_messenger_enabled', $settings['live_chat_messenger_enabled']) === '1' ? 'checked' : '' }}></div>
                        <label class="block text-xs font-bold text-slate-700">Messenger Link</label>
                        <input type="url" name="live_chat_messenger_url" value="{{ old('live_chat_messenger_url', $settings['live_chat_messenger_url']) }}" placeholder="https://m.me/your-page" class="w-full rounded-sm border border-slate-200 px-3 py-2 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <p class="text-[11px] text-slate-400">Example: https://m.me/your-page</p>
                    </div>

                    <div class="rounded-md border border-slate-200 p-4 space-y-3">
                        <div class="flex items-center justify-between"><h3 class="text-sm font-bold text-slate-800">Call</h3><input type="checkbox" name="live_chat_call_enabled" value="1" {{ old('live_chat_call_enabled', $settings['live_chat_call_enabled']) === '1' ? 'checked' : '' }}></div>
                        <label class="block text-xs font-bold text-slate-700">Call Number</label>
                        <input type="text" name="live_chat_call_number" value="{{ old('live_chat_call_number', $settings['live_chat_call_number']) }}" placeholder="+8801XXXXXXXXX" class="w-full rounded-sm border border-slate-200 px-3 py-2 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <p class="text-[11px] text-slate-400">This opens the visitor's phone dialer.</p>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-5">
                    <h3 class="text-sm font-bold text-slate-800">Widget Colors</h3>
                    <p class="mt-1 text-xs text-slate-500">Choose the colors for the new left-side live chat widget.</p>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <label class="text-xs font-bold text-slate-700">Toggle Button<input type="color" name="live_chat_toggle_color" value="{{ old('live_chat_toggle_color', $settings['live_chat_toggle_color']) }}" class="mt-2 block h-10 w-full cursor-pointer rounded border border-slate-200 p-1"></label>
                        <label class="text-xs font-bold text-slate-700">WhatsApp<input type="color" name="live_chat_whatsapp_color" value="{{ old('live_chat_whatsapp_color', $settings['live_chat_whatsapp_color']) }}" class="mt-2 block h-10 w-full cursor-pointer rounded border border-slate-200 p-1"></label>
                        <label class="text-xs font-bold text-slate-700">Messenger<input type="color" name="live_chat_messenger_color" value="{{ old('live_chat_messenger_color', $settings['live_chat_messenger_color']) }}" class="mt-2 block h-10 w-full cursor-pointer rounded border border-slate-200 p-1"></label>
                        <label class="text-xs font-bold text-slate-700">Call<input type="color" name="live_chat_call_color" value="{{ old('live_chat_call_color', $settings['live_chat_call_color']) }}" class="mt-2 block h-10 w-full cursor-pointer rounded border border-slate-200 p-1"></label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end"><button class="rounded-sm bg-blue-600 px-5 py-2.5 text-xs font-bold text-white hover:bg-blue-700">Save Live Chat Settings</button></div>
        </form>
    </div>
</x-app-layout>
