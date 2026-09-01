@if(($stockPriceNoticeActive ?? '0') == '1' && !empty(trim($stockPriceNoticeText ?? '')))
    @php
        $type = $stockPriceNoticeType ?? 'warning';
        // The bg-amber-50/border-amber-200/text-amber-900 (and blue/rose)
        // Tailwind classes below aren't in the harvested static CSS bundle at
        // all (checked: zero rules for any of them), so this box has always
        // rendered with no color — plain white/transparent — wherever it's
        // used. Inline styles instead, matching the same Tailwind palette
        // values, so it actually shows the intended amber/blue/rose card in
        // both light and dark mode (a status color like this stays fixed
        // either way, same as the site's other semantic colors).
        [$bg, $border, $text, $icon] = match ($type) {
            'info' => ['#eff6ff', '#bfdbfe', '#1e3a8a', '#2563eb'],
            'danger' => ['#fff1f2', '#fecdd3', '#881337', '#e11d48'],
            default => ['#fffbeb', '#fde68a', '#78350f', '#d97706'],
        };
    @endphp

    <div class="my-3 p-3 rounded-lg border text-xs font-medium leading-relaxed flex items-start gap-2.5 shadow-xs transition-all" style="background-color: {{ $bg }}; border-color: {{ $border }}; color: {{ $text }};">
        <div class="shrink-0 mt-0.5" style="color: {{ $icon }};">
            @if($type === 'info')
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-info"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
            @elseif($type === 'danger')
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-alert-circle"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-alert-triangle"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
            @endif
        </div>
        <div class="flex-1 break-words">
            {{ $stockPriceNoticeText }}
        </div>
    </div>
@endif
