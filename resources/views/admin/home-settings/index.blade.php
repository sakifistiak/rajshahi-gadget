<x-app-layout>
<div class="w-full space-y-6" x-data="{
    sections: {{ json_encode($sectionsList) }},
    flashTitle: {{ json_encode($settings['home_flash_title'] ?? 'Limited time deals') }},
    flashHighlight: {{ json_encode($settings['home_flash_highlight'] ?? 'deals') }},
    flashStyle: {{ json_encode($flashTitleStyle) }},
    flashBadgeActive: {{ ($settings['home_flash_badge_active'] ?? '1') == '1' ? 'true' : 'false' }},
    flashBadgeIcon: {{ json_encode($settings['home_flash_badge_icon'] ?? '') }},
    flashBadgeText: {{ json_encode($settings['home_flash_badge_text'] ?? 'Flash Deals') }},
    flashSubtitleActive: {{ ($settings['home_flash_subtitle_active'] ?? '1') == '1' ? 'true' : 'false' }},
    flashSubtitleText: {{ json_encode($settings['home_flash_subtitle_text'] ?? 'Limited stock · 0% EMI up to 12 months · Free Dhaka delivery') }},
    preorderBadgeActive: {{ ($settings['home_preorder_badge_active'] ?? '1') == '1' ? 'true' : 'false' }},
    preorderBadgeIcon: {{ json_encode($settings['home_preorder_badge_icon'] ?? '') }},
    preorderBadgeText: {{ json_encode($settings['home_preorder_badge_text'] ?? 'Pre-Order') }},
    preorderSubtitleActive: {{ ($settings['home_preorder_subtitle_active'] ?? '1') == '1' ? 'true' : 'false' }},
    preorderSubtitleText: {{ json_encode($settings['home_preorder_subtitle_text'] ?? 'Reserve now, get it as soon as it launches') }},
    titleStyleDefaults: {{ json_encode($titleStyleDefaults) }},
    titleStyleFonts: {{ json_encode($titleStyleFonts) }},
    titleStyleShadows: {{ json_encode($titleStyleShadows) }},
    addSection() {
        this.sections.push({
            id: 'sec_' + Date.now(),
            title: 'New Product Section',
            highlight: 'Product',
            filter: 'all',
            limit: '4',
            active: true,
            style: {
                base: { ...this.titleStyleDefaults.base },
                highlight: { ...this.titleStyleDefaults.highlight }
            }
        });
    },
    removeSection(index) {
        if (confirm('Are you sure you want to remove this section?')) {
            this.sections.splice(index, 1);
        }
    },
    styleToCss(style) {
        if (!style) return '';
        const rules = [];
        if (style.text_color !== 'inherit') {
            rules.push('color: ' + style.text_color);
        }
        const hasBg = style.bg_type !== 'none';
        if (style.bg_type === 'solid') {
            rules.push('background-color: ' + style.bg_color);
        } else if (style.bg_type === 'gradient') {
            rules.push('background-image: linear-gradient(' + style.bg_gradient_angle + 'deg, ' + style.bg_gradient_from + ', ' + style.bg_gradient_to + ')');
        }
        if (hasBg) {
            rules.push('padding: 2px 8px');
            rules.push('border-radius: 6px');
            rules.push('display: inline-block');
            rules.push('font-weight: 700');
        }
        const font = this.titleStyleFonts[style.font];
        if (font && font.stack) {
            rules.push('font-family: ' + font.stack);
        }
        const shadow = this.titleStyleShadows[style.shadow];
        if (shadow && shadow.css) {
            rules.push('text-shadow: ' + shadow.css);
        }
        return rules.length ? rules.join('; ') + ';' : '';
    },
    formatTitle(fullText, highlightWord, style) {
        if (!fullText) return '';
        style = style || this.titleStyleDefaults;
        const baseCss = this.styleToCss(style.base);
        if (!highlightWord || !fullText.toLowerCase().includes(highlightWord.toLowerCase())) {
            return baseCss ? '<span style=&quot;' + baseCss + '&quot;>' + fullText + '</span>' : fullText;
        }
        const escapedHl = highlightWord.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp('(' + escapedHl + ')', 'gi');
        const hlCss = this.styleToCss(style.highlight);
        const inner = fullText.replace(regex, '<span style=&quot;' + hlCss + '&quot;>$1</span>');
        return baseCss ? '<span style=&quot;' + baseCss + '&quot;>' + inner + '</span>' : inner;
    }
}">

    <!-- Header -->
    <div class="flex items-center justify-between p-5 bg-white rounded-sm border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Homepage Settings</h2>
            <p class="text-xs text-slate-500 mt-1">Control section visibility, create custom category sections, and customize title styling for the homepage.</p>
        </div>
        <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-lg transition-colors">
            <i data-lucide="external-link" class="w-4 h-4"></i>
            <span>View Homepage</span>
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-sm text-xs font-bold flex items-center gap-2">
            <i data-lucide="check-circle" class="h-4 w-4 text-emerald-600"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-rose-50 text-rose-800 border border-rose-200 rounded-sm text-xs font-bold space-y-1">
            @foreach($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <i data-lucide="alert-circle" class="h-4 w-4 text-rose-600"></i>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('admin.home-settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <input type="hidden" name="home_sections_json" :value="JSON.stringify(sections)">

        <!-- 1. Section Visibility Toggles -->
        <div class="bg-white rounded-sm border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                        <i data-lucide="eye" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">1. Global Section Toggles</h3>
                        <p class="text-xs text-slate-500">Toggle static homepage components like hero slider and testimonials.</p>
                    </div>
                </div>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <label class="flex items-center justify-between p-4 rounded-lg border border-slate-200 hover:border-blue-300 bg-slate-50/30 cursor-pointer transition-all">
                    <span class="text-sm font-bold text-slate-800">Hero Slider</span>
                    <input type="checkbox" name="home_hero_active" value="1" {{ ($settings['home_hero_active'] ?? '1') == '1' ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                </label>
                <label class="flex items-center justify-between p-4 rounded-lg border border-slate-200 hover:border-blue-300 bg-slate-50/30 cursor-pointer transition-all">
                    <span class="text-sm font-bold text-slate-800">Flash Deals / Offers</span>
                    <input type="checkbox" name="home_flash_active" value="1" {{ ($settings['home_flash_active'] ?? '1') == '1' ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                </label>
                <label class="flex items-center justify-between p-4 rounded-lg border border-slate-200 hover:border-blue-300 bg-slate-50/30 cursor-pointer transition-all">
                    <span class="text-sm font-bold text-slate-800">Pre-Order Widget</span>
                    <input type="checkbox" name="home_preorder_active" value="1" {{ ($settings['home_preorder_active'] ?? '0') == '1' ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                </label>
                <label class="flex items-center justify-between p-4 rounded-lg border border-slate-200 hover:border-blue-300 bg-slate-50/30 cursor-pointer transition-all">
                    <span class="text-sm font-bold text-slate-800">Customer Testimonials</span>
                    <input type="checkbox" name="home_testimonials_active" value="1" {{ ($settings['home_testimonials_active'] ?? '1') == '1' ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                </label>
            </div>
        </div>

        <!-- Flash Deals Title -->
        <input type="hidden" name="home_flash_title_style" :value="JSON.stringify(flashStyle)">
        <div class="bg-white rounded-sm border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-orange-100 text-orange-600 rounded-lg">
                        <i data-lucide="flame" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Flash Deals Title</h3>
                        <p class="text-xs text-slate-500">Title and highlight styling for the Flash Deals section heading.</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Section Title</label>
                        <input type="text" name="home_flash_title" x-model="flashTitle" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Today's best prices in Bangladesh">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Highlight Word</label>
                        <input type="text" name="home_flash_highlight" x-model="flashHighlight" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. best prices">
                    </div>
                </div>

                <!-- Live Title Preview -->
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Live Title Preview:</span>
                    <h2 class="text-xl font-bold text-slate-900" x-html="formatTitle(flashTitle, flashHighlight, flashStyle)"></h2>
                </div>

                <x-title-style-editor path="flashStyle" />

                <!-- Badge pill ("🔥 Flash Deals") -->
                <div class="pt-4 border-t border-slate-100">
                    <label class="flex items-center justify-between p-4 rounded-lg border border-slate-200 hover:border-blue-300 bg-slate-50/30 cursor-pointer transition-all mb-4">
                        <div>
                            <span class="text-sm font-bold text-slate-800 block">Badge Pill</span>
                            <span class="text-xs text-slate-500">The small "Flash Deals" label shown above the title.</span>
                        </div>
                        <input type="checkbox" name="home_flash_badge_active" x-model="flashBadgeActive" value="1" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5" x-show="flashBadgeActive">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Icon / Emoji <span class="normal-case font-normal text-slate-400">(leave blank for default flame icon)</span></label>
                            <input type="text" name="home_flash_badge_icon" x-model="flashBadgeIcon" maxlength="8" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. ⚡ or 🔥">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Badge Text</label>
                            <input type="text" name="home_flash_badge_text" x-model="flashBadgeText" maxlength="40" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Flash Deals">
                        </div>
                    </div>
                    <div class="mt-3 p-3 bg-slate-50 rounded-lg border border-slate-200" x-show="flashBadgeActive">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Preview:</span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-orange-50 px-3 py-1 text-xs font-medium text-orange-600" x-text="(flashBadgeIcon || '🔥') + ' ' + flashBadgeText"></span>
                    </div>
                </div>

                <!-- Subtitle line under the title -->
                <div class="pt-4 border-t border-slate-100">
                    <label class="flex items-center justify-between p-4 rounded-lg border border-slate-200 hover:border-blue-300 bg-slate-50/30 cursor-pointer transition-all mb-4">
                        <div>
                            <span class="text-sm font-bold text-slate-800 block">Subtitle Line</span>
                            <span class="text-xs text-slate-500">The small description line under the title (e.g. delivery/EMI perks).</span>
                        </div>
                        <input type="checkbox" name="home_flash_subtitle_active" x-model="flashSubtitleActive" value="1" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                    </label>
                    <div x-show="flashSubtitleActive">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Subtitle Text</label>
                        <input type="text" name="home_flash_subtitle_text" x-model="flashSubtitleText" maxlength="150" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Limited stock · 0% EMI up to 12 months · Free Dhaka delivery">
                    </div>
                </div>
            </div>
        </div>

        <!-- Pre-Order Widget -->
        <div class="bg-white rounded-sm border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Pre-Order Widget</h3>
                        <p class="text-xs text-slate-500">Shows products marked "Pre-Order" (set per-product in Products &gt; Edit). Position it above or below Flash Deals.</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Section Title</label>
                        <input type="text" name="home_preorder_title" value="{{ $settings['home_preorder_title'] ?? 'Pre-Order Now' }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Pre-Order Now">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Highlight Word</label>
                        <input type="text" name="home_preorder_highlight" value="{{ $settings['home_preorder_highlight'] ?? 'Pre-Order' }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Pre-Order">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Position</label>
                        <div class="flex gap-4 pt-1">
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                                <input type="radio" name="home_preorder_position" value="above_flash" {{ ($settings['home_preorder_position'] ?? 'below_flash') === 'above_flash' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500">
                                Show above Flash Deals
                            </label>
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                                <input type="radio" name="home_preorder_position" value="below_flash" {{ ($settings['home_preorder_position'] ?? 'below_flash') === 'below_flash' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500">
                                Show below Flash Deals
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Products to Show</label>
                        <input type="number" name="home_preorder_limit" min="1" max="12" value="{{ $settings['home_preorder_limit'] ?? '4' }}" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Badge pill ("Pre-Order") -->
                <div class="pt-4 border-t border-slate-100">
                    <label class="flex items-center justify-between p-4 rounded-lg border border-slate-200 hover:border-blue-300 bg-slate-50/30 cursor-pointer transition-all mb-4">
                        <div>
                            <span class="text-sm font-bold text-slate-800 block">Badge Pill</span>
                            <span class="text-xs text-slate-500">The small "Pre-Order" label shown above the title.</span>
                        </div>
                        <input type="checkbox" name="home_preorder_badge_active" x-model="preorderBadgeActive" value="1" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5" x-show="preorderBadgeActive">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Icon <span class="normal-case font-normal text-slate-400">(leave blank for default clock icon)</span></label>
                            <input type="text" name="home_preorder_badge_icon" x-model="preorderBadgeIcon" maxlength="8" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Badge Text</label>
                            <input type="text" name="home_preorder_badge_text" x-model="preorderBadgeText" maxlength="40" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Pre-Order">
                        </div>
                    </div>
                    <div class="mt-3 p-3 bg-slate-50 rounded-lg border border-slate-200" x-show="preorderBadgeActive">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Preview:</span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-600" x-text="(preorderBadgeIcon ? preorderBadgeIcon + ' ' : '') + preorderBadgeText"></span>
                    </div>
                </div>

                <!-- Subtitle line under the title -->
                <div class="pt-4 border-t border-slate-100">
                    <label class="flex items-center justify-between p-4 rounded-lg border border-slate-200 hover:border-blue-300 bg-slate-50/30 cursor-pointer transition-all mb-4">
                        <div>
                            <span class="text-sm font-bold text-slate-800 block">Subtitle Line</span>
                            <span class="text-xs text-slate-500">The small description line under the title.</span>
                        </div>
                        <input type="checkbox" name="home_preorder_subtitle_active" x-model="preorderSubtitleActive" value="1" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                    </label>
                    <div x-show="preorderSubtitleActive">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Subtitle Text</label>
                        <input type="text" name="home_preorder_subtitle_text" x-model="preorderSubtitleText" maxlength="150" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Reserve now, get it as soon as it launches">
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Notice Ticker Announcements -->
        <div class="bg-white rounded-sm border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-100 text-amber-600 rounded-lg">
                        <i data-lucide="megaphone" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">2. Notice Ticker Announcements</h3>
                        <p class="text-xs text-slate-500">Manage marquee announcements shown on the homepage.</p>
                    </div>
                </div>
                <label class="flex items-center gap-2 text-xs font-bold text-slate-700 cursor-pointer select-none">
                    <input type="checkbox" name="home_ticker_active" value="1" {{ ($settings['home_ticker_active'] ?? '1') == '1' ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                    <span>Enable Notice Ticker</span>
                </label>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Notice Items (One announcement per line)</label>
                    <textarea name="home_ticker_text" rows="5" class="w-full px-4 py-3 rounded-lg border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500 leading-relaxed" placeholder="Type announcements here, one per line...">{{ $settings['home_ticker_text'] ?? '' }}</textarea>
                    <p class="text-[11px] text-slate-400 mt-2">Each line will be displayed as a scrolling announcement in the marquee bar.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Notice Animation Style</label>
                    <select name="home_ticker_effect" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="fade" {{ ($settings['home_ticker_effect'] ?? 'fade') === 'fade' ? 'selected' : '' }}>Effect 1 — Fade &amp; Slide (quick, fades in/out)</option>
                        <option value="scroll" {{ ($settings['home_ticker_effect'] ?? 'fade') === 'scroll' ? 'selected' : '' }}>Effect 2 — Slow Scroll (glides across, one at a time)</option>
                    </select>
                    <p class="text-[11px] text-slate-400 mt-2">Effect 1: each notice fades in from the right and fades out. Effect 2: each notice slowly glides all the way across, like a scroller, before the next one starts.</p>
                </div>

                <div x-data="{ speed: {{ (float) ($settings['home_ticker_speed'] ?? 6) }} }">
                    <label class="flex items-center justify-between text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        <span>Scrolling Speed</span>
                        <span class="text-blue-600 font-mono normal-case" x-text="speed + 's'"></span>
                    </label>
                    <input type="range" name="home_ticker_speed" min="2" max="20" step="0.5" x-model.number="speed" class="w-full accent-blue-600">
                    <div class="flex justify-between text-[11px] text-slate-400 mt-1">
                        <span>Fast (2s)</span>
                        <span>Slow (20s)</span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-2">Controls how long each notice stays on screen (Effect 1) or how long it takes to glide across (Effect 2). Lower = faster.</p>
                </div>
            </div>
        </div>

        <!-- Stock & Price Disclaimer Notice Card -->
        <div class="bg-white rounded-sm border border-slate-200 shadow-sm overflow-hidden" x-data="{ noticeLength: '{{ mb_strlen($settings['stock_price_notice_text'] ?? 'অর্ডার করার পূর্বে স্টক ও প্রাইজ কমতে বাড়তে পারে') }}' }">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                        <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Stock & Price Disclaimer Notice</h3>
                        <p class="text-xs text-slate-500">Configure disclaimer notice displayed on Product Detail and Cart pages.</p>
                    </div>
                </div>
                <label class="flex items-center gap-2 text-xs font-bold text-slate-700 cursor-pointer select-none">
                    <input type="checkbox" name="stock_price_notice_active" value="1" {{ ($settings['stock_price_notice_active'] ?? '1') == '1' ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                    <span>Enable Notice Banner</span>
                </label>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Notice Text Message (Bengali / English)</label>
                        <span class="text-[11px] text-slate-400 font-mono"><span x-text="noticeLength"></span>/255</span>
                    </div>
                    <textarea name="stock_price_notice_text" 
                              rows="2" 
                              maxlength="255"
                              @input="noticeLength = $event.target.value.length"
                              class="w-full px-4 py-3 rounded-lg border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500 leading-relaxed" 
                              placeholder="e.g. অর্ডার করার পূর্বে স্টক ও প্রাইজ কমতে বাড়তে পারে">{{ $settings['stock_price_notice_text'] ?? 'অর্ডার করার পূর্বে স্টক ও প্রাইজ কমতে বাড়তে পারে' }}</textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Shown below price on Product detail page and near Order Summary on Cart page.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Banner Color & Style</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition-colors">
                            <input type="radio" name="stock_price_notice_type" value="warning" {{ ($settings['stock_price_notice_type'] ?? 'warning') === 'warning' ? 'checked' : '' }} class="text-amber-600 focus:ring-amber-500">
                            <div class="flex items-center gap-2 text-xs font-bold text-amber-800">
                                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                                Warning (Amber / 🟡)
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition-colors">
                            <input type="radio" name="stock_price_notice_type" value="info" {{ ($settings['stock_price_notice_type'] ?? 'warning') === 'info' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500">
                            <div class="flex items-center gap-2 text-xs font-bold text-blue-800">
                                <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                                Info (Blue / 🔵)
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition-colors">
                            <input type="radio" name="stock_price_notice_type" value="danger" {{ ($settings['stock_price_notice_type'] ?? 'warning') === 'danger' ? 'checked' : '' }} class="text-rose-600 focus:ring-rose-500">
                            <div class="flex items-center gap-2 text-xs font-bold text-rose-800">
                                <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                                Danger (Red / 🔴)
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Detail Page - Delivery/Replacement Info Box -->
        <div class="bg-white rounded-sm border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                        <i data-lucide="truck" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Product Page — Delivery / Warranty / Replacement Box</h3>
                        <p class="text-xs text-slate-500">Controls the "Fast delivery / Warranty / Easy replacement" info box shown below the Buy Now button on the product detail page.</p>
                    </div>
                </div>
                <label class="flex items-center gap-2 text-xs font-bold text-slate-700 cursor-pointer select-none">
                    <input type="checkbox" name="product_trust_badges_active" value="1" {{ ($settings['product_trust_badges_active'] ?? '1') == '1' ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                    <span>Show Info Box</span>
                </label>
            </div>
        </div>

        <!-- 2. Dynamic Product Sections List -->
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Dynamic Product Sections</h3>
                    <p class="text-xs text-slate-500">Add, remove, or reorder custom category sections for your homepage.</p>
                </div>
                <button type="button" @click="addSection()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg transition-all shadow-sm">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Add New Product Section</span>
                </button>
            </div>

            <!-- Loop over sections -->
            <template x-for="(sec, index) in sections" :key="sec.id">
                <div class="bg-white rounded-sm border border-slate-200 shadow-sm overflow-hidden transition-all">
                    <div class="p-4 border-b border-slate-100 bg-slate-50/70 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-blue-100 text-blue-700 font-extrabold text-xs" x-text="index + 1"></span>
                            <span class="text-sm font-bold text-slate-800" x-text="sec.title || 'Product Section'"></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <!-- Toggle Active -->
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-600 cursor-pointer select-none">
                                <input type="checkbox" x-model="sec.active" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                                <span>Show Section</span>
                            </label>
                            <!-- Delete Button -->
                            <button type="button" @click="removeSection(index)" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition-colors" title="Delete Section">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Section Title</label>
                                <input type="text" x-model="sec.title" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Intact Box Laptops">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Highlight Word</label>
                                <input type="text" x-model="sec.highlight" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Laptops">
                            </div>
                        </div>

                        <!-- Live Title Preview -->
                        <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Live Title Preview:</span>
                            <h2 class="text-xl font-bold text-slate-900" x-html="formatTitle(sec.title, sec.highlight, sec.style)"></h2>
                        </div>

                        <!-- Highlight Word + Rest-of-Title Style Customization -->
                        <x-title-style-editor path="sec.style" />

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Category / Filter Source</label>
                                <select x-model="sec.filter" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <optgroup label="Product Conditions">
                                        <option value="cond_intact">Brand New Intact Box</option>
                                        <option value="cond_without-box">Brand New Without Box</option>
                                        <option value="cond_pre-owned">Certified Pre-Owned</option>
                                    </optgroup>
                                    <optgroup label="Product Categories">
                                        @foreach($categories as $cat)
                                            <option value="cat_{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="General">
                                        <option value="all">All Latest Products</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Product Limit</label>
                                <select x-model="sec.limit" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="4">4 Items</option>
                                    <option value="8">8 Items</option>
                                    <option value="12">12 Items</option>
                                    <option value="16">16 Items</option>
                                    <option value="20">20 Items</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Bottom Add Section Button -->
            <div class="flex justify-center pt-2">
                <button type="button" @click="addSection()" class="inline-flex items-center gap-2 px-6 py-3 border-2 border-dashed border-slate-300 hover:border-blue-500 text-slate-700 hover:text-blue-600 font-bold text-xs rounded-xl transition-all w-full justify-center bg-white shadow-xs">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Add Another Product Section</span>
                </button>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-4 border-t border-slate-200">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-sm transition-all">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Save Homepage Settings</span>
            </button>
        </div>
    </form>
</div>
</x-app-layout>
