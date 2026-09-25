<?php

namespace App\Support;

class GoogleForm
{
    /**
     * The embeddable URL for whatever the admin pasted: a form's share link or the whole
     * <iframe> embed code from Google Forms' "Send" dialog. Returns null for anything that
     * is not a public Google Form, so the page only ever frames docs.google.com/forms.
     *
     * The result always ends up as ".../viewform?embedded=true", which is the form without
     * Google's page chrome. Other query parameters (e.g. prefilled "entry.123=..." answers)
     * are kept.
     */
    public static function embedUrl(?string $input): ?string
    {
        $input = trim((string) $input);

        if ($input === '') {
            return null;
        }

        if (stripos($input, '<iframe') !== false) {
            if (! preg_match('/\ssrc\s*=\s*(["\'])(.*?)\1/i', $input, $match)) {
                return null;
            }
            $input = html_entity_decode($match[2], ENT_QUOTES | ENT_HTML5);
        }

        $parts = parse_url($input);

        if ($parts === false
            || strtolower($parts['scheme'] ?? '') !== 'https'
            || strtolower($parts['host'] ?? '') !== 'docs.google.com'
            || isset($parts['user']) || isset($parts['pass']) || isset($parts['port'])) {
            return null;
        }

        // Public links are /forms/d/e/{published id}/viewform (or the older /forms/d/{id}/viewform),
        // optionally under a /u/{n}/ account prefix. The /edit link is the owner's editor and would
        // show visitors a "request access" screen, so it is rejected.
        if (! preg_match('#^/forms(?:/u/\d+)?/d/(e/)?([A-Za-z0-9_-]+)(?:/(viewform|formResponse))?/?$#', $parts['path'] ?? '', $path)) {
            return null;
        }

        // Split by hand: parse_str() would turn the dot in "entry.123" into an underscore.
        $query = array_filter(
            explode('&', $parts['query'] ?? ''),
            fn (string $pair) => $pair !== '' && ! preg_match('/^(usp|embedded)(=|$)/i', $pair)
        );
        $query[] = 'embedded=true';

        return 'https://docs.google.com/forms/d/'.$path[1].$path[2].'/viewform?'.implode('&', $query);
    }
}
