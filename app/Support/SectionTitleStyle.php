<?php

namespace App\Support;

class SectionTitleStyle
{
    public const FONTS = [
        'inherit'     => ['label' => 'Default (Theme Font)', 'stack' => null],
        'inter'       => ['label' => 'Inter',            'stack' => "'Inter', sans-serif",            'google' => 'Inter:wght@700'],
        'poppins'     => ['label' => 'Poppins',           'stack' => "'Poppins', sans-serif",          'google' => 'Poppins:wght@700'],
        'playfair'    => ['label' => 'Playfair Display',  'stack' => "'Playfair Display', serif",      'google' => 'Playfair+Display:wght@700'],
        'roboto-slab' => ['label' => 'Roboto Slab',       'stack' => "'Roboto Slab', serif",           'google' => 'Roboto+Slab:wght@700'],
        'oswald'      => ['label' => 'Oswald',            'stack' => "'Oswald', sans-serif",           'google' => 'Oswald:wght@700'],
    ];

    public const SHADOWS = [
        'none' => ['label' => 'None', 'css' => null],
        'soft' => ['label' => 'Soft', 'css' => '0 1px 3px rgba(0,0,0,0.2)'],
        'hard' => ['label' => 'Hard', 'css' => '2px 2px 0 rgba(0,0,0,0.25)'],
        'glow' => ['label' => 'Glow', 'css' => '0 0 12px currentColor'],
    ];

    public const BG_TYPES = ['none', 'solid', 'gradient'];

    /** Defaults for the highlighted word/phrase — matches the original hardcoded blue pill look. */
    public const DEFAULTS = [
        'text_color'        => '#2563eb',
        'font'              => 'inherit',
        'shadow'            => 'none',
        'bg_type'           => 'solid',
        'bg_color'          => '#dbeafe',
        'bg_gradient_from'  => '#2563eb',
        'bg_gradient_to'    => '#9333ea',
        'bg_gradient_angle' => 90,
    ];

    /** Defaults for the rest of the title — "inherit" means untouched (no style change from today). */
    public const BASE_DEFAULTS = [
        'text_color'        => 'inherit',
        'font'              => 'inherit',
        'shadow'            => 'none',
        'bg_type'           => 'none',
        'bg_color'          => '#dbeafe',
        'bg_gradient_from'  => '#2563eb',
        'bg_gradient_to'    => '#9333ea',
        'bg_gradient_angle' => 90,
    ];

    private const HEX_PATTERN = '/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/';

    /**
     * Validate/coerce an arbitrary (client-supplied) style blob into a safe
     * {base, highlight} pair. Every field falls back to its scope's default
     * rather than accepting unvalidated input. Also migrates the old flat
     * (highlight-only) shape saved by earlier versions of this feature.
     */
    public static function sanitizeFull(?array $input): array
    {
        $input ??= [];

        // Legacy flat format: style was saved directly as the highlight style, no base/highlight split.
        if (!isset($input['base']) && !isset($input['highlight']) && !empty($input)) {
            return [
                'base'      => self::sanitizeScope(null, self::BASE_DEFAULTS),
                'highlight' => self::sanitizeScope($input, self::DEFAULTS),
            ];
        }

        return [
            'base'      => self::sanitizeScope($input['base'] ?? null, self::BASE_DEFAULTS),
            'highlight' => self::sanitizeScope($input['highlight'] ?? null, self::DEFAULTS),
        ];
    }

    private static function sanitizeScope(?array $input, array $defaults): array
    {
        $input ??= [];

        return [
            'text_color'        => self::colorOrInherit($input['text_color'] ?? null, $defaults['text_color']),
            'font'              => array_key_exists($input['font'] ?? null, self::FONTS) ? $input['font'] : $defaults['font'],
            'shadow'            => array_key_exists($input['shadow'] ?? null, self::SHADOWS) ? $input['shadow'] : $defaults['shadow'],
            'bg_type'           => in_array($input['bg_type'] ?? null, self::BG_TYPES, true) ? $input['bg_type'] : $defaults['bg_type'],
            'bg_color'          => self::hex($input['bg_color'] ?? null, $defaults['bg_color']),
            'bg_gradient_from'  => self::hex($input['bg_gradient_from'] ?? null, $defaults['bg_gradient_from']),
            'bg_gradient_to'    => self::hex($input['bg_gradient_to'] ?? null, $defaults['bg_gradient_to']),
            'bg_gradient_angle' => max(0, min(360, (int) ($input['bg_gradient_angle'] ?? $defaults['bg_gradient_angle']))),
        ];
    }

    private static function hex(mixed $value, string $default): string
    {
        return is_string($value) && preg_match(self::HEX_PATTERN, $value) ? $value : $default;
    }

    private static function colorOrInherit(mixed $value, string $default): string
    {
        if (is_string($value) && ($value === 'inherit' || preg_match(self::HEX_PATTERN, $value))) {
            return $value;
        }

        return $default;
    }

    /**
     * Build the inline `style` attribute value for a single sanitized scope
     * (the output of sanitizeScope/sanitizeFull()['base'|'highlight']).
     * Never call this on unsanitized input.
     */
    public static function toInlineCss(array $style): string
    {
        $rules = [];

        if ($style['text_color'] !== 'inherit') {
            $rules[] = 'color: ' . $style['text_color'];
        }

        $hasBackground = $style['bg_type'] !== 'none';
        if ($style['bg_type'] === 'solid') {
            $rules[] = 'background-color: ' . $style['bg_color'];
        } elseif ($style['bg_type'] === 'gradient') {
            $rules[] = "background-image: linear-gradient({$style['bg_gradient_angle']}deg, {$style['bg_gradient_from']}, {$style['bg_gradient_to']})";
        }

        // Only pad into a "pill" shape when there's a background to contain — plain colored
        // text (no background) shouldn't get box-like padding forced onto it.
        if ($hasBackground) {
            $rules[] = 'padding: 2px 8px';
            $rules[] = 'border-radius: 6px';
            $rules[] = 'display: inline-block';
            $rules[] = 'font-weight: 700';
        }

        $fontStack = self::FONTS[$style['font']]['stack'] ?? null;
        if ($fontStack) {
            $rules[] = 'font-family: ' . $fontStack;
        }

        $shadowCss = self::SHADOWS[$style['shadow']]['css'] ?? null;
        if ($shadowCss) {
            $rules[] = 'text-shadow: ' . $shadowCss;
        }

        return empty($rules) ? '' : implode('; ', $rules) . ';';
    }

    /**
     * Combined Google Fonts URL for every non-default font in FONTS, loaded once
     * regardless of which sections actually use them (small fixed set, cheap to load).
     */
    public static function googleFontsUrl(): string
    {
        $families = array_filter(array_column(self::FONTS, 'google'));

        return 'https://fonts.googleapis.com/css2?' . implode('&', array_map(
            fn ($f) => 'family=' . $f,
            $families
        )) . '&display=swap';
    }
}
