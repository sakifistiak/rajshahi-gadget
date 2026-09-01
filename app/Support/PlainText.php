<?php

namespace App\Support;

use Normalizer;

class PlainText
{
    /**
     * Flatten "fancy" Unicode text styling (𝗕𝗼𝗹𝗱, 𝘐𝘵𝘢𝘭𝘪𝘤, 𝓈𝒸𝓇𝒾𝓅𝓉, 𝔉𝔯𝔞𝔨𝔱𝔲𝔯, etc. —
     * the Mathematical Alphanumeric Symbols block, U+1D400–U+1D7FF) back to
     * plain ASCII letters/digits, e.g. "𝗘𝗹𝗲𝗽𝗵𝗮𝗻𝘁 𝗥𝗼𝗮𝗱" → "Elephant Road".
     *
     * Admins sometimes paste store/section names copied from a "bold text
     * generator" — those aren't a font-weight, they're different Unicode
     * codepoints that render pre-styled everywhere and can't be un-bolded
     * with CSS. Use this wherever a specific spot on the site needs to
     * force plain text regardless of how the value was styled elsewhere
     * (e.g. a <select><option> list, which can't render bold/italic markup
     * anyway and would otherwise show the raw stylized glyphs).
     *
     * Leaves ordinary text (including Bangla and other non-Latin scripts)
     * completely untouched — NFKD only decomposes the styled Latin/digit
     * range, nothing else.
     */
    public static function flatten(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        return Normalizer::normalize($text, Normalizer::FORM_KD) ?: $text;
    }
}
