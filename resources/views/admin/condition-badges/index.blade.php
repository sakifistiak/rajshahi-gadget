<x-app-layout>
    <div class="w-full space-y-6">
        <!-- Page Header -->
        <div class="bg-white p-5 rounded-sm border border-gray-100 shadow-sm">
            <h1 class="text-xl font-bold text-gray-900">Condition Badges</h1>
            <p class="text-xs text-gray-500 mt-1">Every product card shows its condition's badge in the top-right corner automatically. A single product can hide it from its edit screen.</p>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold rounded-sm flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                    {{ session('success') }}
                </span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">Close</button>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold rounded-sm">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.condition-badges.update') }}" method="POST" class="bg-white rounded-sm border border-gray-100 shadow-sm overflow-hidden">
            @csrf
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-600">
                    <thead class="bg-gray-50/80 border-b border-gray-100 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-5 py-3.5">Condition</th>
                            <th class="px-5 py-3.5 text-center">Show Badge</th>
                            <th class="px-5 py-3.5">Badge Text</th>
                            <th class="px-5 py-3.5">Color</th>
                            <th class="px-5 py-3.5">Preview</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-medium">
                        @foreach ($conditions as $condition)
                            @php
                                $text = old("badges.{$condition->id}.badge_text", $condition->badge_text);
                                $color = old("badges.{$condition->id}.badge_color", $condition->badge_color ?: '#16a34a');
                                $active = old("badges.{$condition->id}") ? (bool) old("badges.{$condition->id}.badge_active") : $condition->badge_active;
                            @endphp
                            <tr x-data="{ text: @js((string) $text), color: @js($color), active: @js($active) }">
                                <td class="px-5 py-4 font-bold text-gray-900">{{ $condition->label }}</td>
                                <td class="px-5 py-4 text-center">
                                    <input type="checkbox" name="badges[{{ $condition->id }}][badge_active]" value="1" x-model="active"
                                           class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </td>
                                <td class="px-5 py-4">
                                    <input type="text" name="badges[{{ $condition->id }}][badge_text]" x-model="text" maxlength="30" placeholder="BRAND NEW"
                                           class="w-48 text-xs px-3 py-2 rounded-sm border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none uppercase">
                                </td>
                                <td class="px-5 py-4">
                                    <input type="color" name="badges[{{ $condition->id }}][badge_color]" x-model="color"
                                           class="h-9 w-14 cursor-pointer rounded-sm border border-slate-200 bg-white p-0.5">
                                </td>
                                <td class="px-5 py-4">
                                    <span x-show="active && text.trim()" x-text="text" class="inline-block rounded font-extrabold uppercase text-white"
                                          :style="'background-color:' + color + '; font-size:10px; line-height:12px; padding:2px 6px;'"></span>
                                    <span x-show="!active || !text.trim()" class="text-[11px] text-gray-400">Hidden</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end border-t border-gray-100 px-5 py-4">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-sm shadow-sm transition-all">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Save Badges
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
