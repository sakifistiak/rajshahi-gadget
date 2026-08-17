@props(['path'])
{{-- $path is a literal Alpine expression (e.g. "sec.style" or "flashStyle"), not user input --}}

{{-- Title font size — one size for the whole heading (highlight word included),
     not per base/highlight scope like the color/font/shadow options below,
     since a title reads as one size. Leave a box blank to use the theme's
     default size for that breakpoint. --}}
<div class="border border-slate-200 rounded-lg p-4 space-y-3 bg-slate-50/60">
    <label class="flex items-center gap-2 text-xs font-bold text-slate-700 uppercase tracking-wider">
        <i data-lucide="ruler" class="w-4 h-4"></i>
        Title Font Size
    </label>
    <p class="text-[11px] text-slate-500 -mt-1.5">Set a fixed size for mobile and desktop screens. Leave either box blank to keep the theme's default size for that screen.</p>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Mobile Size (px)</label>
            <input type="number" min="6" max="120" x-model.number="{{ $path }}.font_size.mobile" placeholder="Default" class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-xs font-semibold">
        </div>
        <div>
            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Desktop Size (px)</label>
            <input type="number" min="6" max="120" x-model.number="{{ $path }}.font_size.desktop" placeholder="Default" class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-xs font-semibold">
        </div>
    </div>
    <button type="button" @click="{{ $path }}.font_size = { mobile: null, desktop: null }" class="text-[11px] font-bold text-blue-600 hover:text-blue-800 inline-flex items-center gap-1">
        <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
        Reset to default size
    </button>
</div>

<template x-for="scope in ['highlight', 'base']" :key="scope">
    <div class="border border-slate-200 rounded-lg" x-data="{ open: false }">
        <button type="button" @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-xs font-bold text-slate-700 uppercase tracking-wider hover:bg-slate-50 transition-colors">
            <span class="flex items-center gap-2">
                <i data-lucide="palette" class="w-4 h-4"></i>
                <span x-text="scope === 'highlight' ? 'Customize Highlight Word Style' : 'Customize Rest-of-Title Style'"></span>
            </span>
            <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''"></i>
        </button>
        <div x-show="open" x-cloak class="p-4 pt-0 space-y-4 border-t border-slate-100">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Text Color</label>
                    <div class="flex items-center gap-2">
                        <input type="color" x-model="{{ $path }}[scope].text_color" class="w-10 h-9 rounded border border-slate-300 cursor-pointer shrink-0">
                        <input type="text" x-model="{{ $path }}[scope].text_color" maxlength="7" class="flex-1 min-w-0 px-3 py-2 rounded-lg border border-slate-300 text-xs font-mono">
                    </div>
                    <label class="flex items-center gap-1.5 mt-1.5 text-[11px] font-semibold text-slate-500 cursor-pointer w-fit">
                        <input type="checkbox" :checked="{{ $path }}[scope].text_color === 'inherit'" @change="{{ $path }}[scope].text_color = $event.target.checked ? 'inherit' : titleStyleDefaults.highlight.text_color" class="w-3.5 h-3.5 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                        Inherit theme color (no override)
                    </label>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Font</label>
                    <select x-model="{{ $path }}[scope].font" class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-xs font-semibold">
                        <template x-for="(font, key) in titleStyleFonts" :key="key">
                            <option :value="key" x-text="font.label"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Text Shadow</label>
                    <select x-model="{{ $path }}[scope].shadow" class="w-full px-3 py-2.5 rounded-lg border border-slate-300 text-xs font-semibold">
                        <template x-for="(shadow, key) in titleStyleShadows" :key="key">
                            <option :value="key" x-text="shadow.label"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Background Style</label>
                    <div class="flex items-center gap-3 text-xs font-semibold text-slate-700 h-9">
                        <label class="flex items-center gap-1.5 cursor-pointer"><input type="radio" value="none" x-model="{{ $path }}[scope].bg_type" class="text-blue-600 focus:ring-blue-500"> None</label>
                        <label class="flex items-center gap-1.5 cursor-pointer"><input type="radio" value="solid" x-model="{{ $path }}[scope].bg_type" class="text-blue-600 focus:ring-blue-500"> Solid</label>
                        <label class="flex items-center gap-1.5 cursor-pointer"><input type="radio" value="gradient" x-model="{{ $path }}[scope].bg_type" class="text-blue-600 focus:ring-blue-500"> Gradient</label>
                    </div>
                </div>
            </div>

            <div x-show="{{ $path }}[scope].bg_type === 'solid'" x-cloak class="max-w-xs">
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Background Color</label>
                <div class="flex items-center gap-2">
                    <input type="color" x-model="{{ $path }}[scope].bg_color" class="w-10 h-9 rounded border border-slate-300 cursor-pointer shrink-0">
                    <input type="text" x-model="{{ $path }}[scope].bg_color" maxlength="7" class="flex-1 min-w-0 px-3 py-2 rounded-lg border border-slate-300 text-xs font-mono">
                </div>
            </div>

            <div x-show="{{ $path }}[scope].bg_type === 'gradient'" x-cloak class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Gradient From</label>
                    <input type="color" x-model="{{ $path }}[scope].bg_gradient_from" class="w-full h-9 rounded border border-slate-300 cursor-pointer">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Gradient To</label>
                    <input type="color" x-model="{{ $path }}[scope].bg_gradient_to" class="w-full h-9 rounded border border-slate-300 cursor-pointer">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Angle (<span x-text="{{ $path }}[scope].bg_gradient_angle"></span>&deg;)</label>
                    <input type="range" min="0" max="360" x-model.number="{{ $path }}[scope].bg_gradient_angle" class="w-full accent-blue-600">
                </div>
            </div>
        </div>
    </div>
</template>
