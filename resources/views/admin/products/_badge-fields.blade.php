{{-- Badges on the product card: the custom one (top-left) is typed here, the
     condition one (top-right) comes from Condition Badges automatically. --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label for="badge" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Custom Badge</label>
        <input id="badge" type="text" name="badge" value="{{ old('badge', $badge) }}" maxlength="30" placeholder="e.g. HOT DEAL"
               class="w-full text-xs px-3.5 py-2.5 rounded-sm border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all uppercase">
        <p class="mt-1 text-[11px] text-slate-400">Optional. Shown in the card's top-left corner. Leave empty for none.</p>
    </div>
    <div>
        <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Condition Badge</span>
        <label class="flex items-center gap-2 text-xs text-slate-700 py-2.5">
            <input type="checkbox" name="hide_condition_badge" value="1" {{ (old('name') !== null ? old('hide_condition_badge') : $hideConditionBadge) ? 'checked' : '' }}
                   class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            Hide the condition badge on this product
        </label>
        <p class="text-[11px] text-slate-400">Shown in the top-right corner from the product's condition. <a href="{{ route('admin.condition-badges.index') }}" target="_blank" class="text-blue-600 hover:underline">Edit condition badges</a></p>
    </div>
</div>
