{{-- Shared SEO block for the <head> of every public storefront template (SEO phases 1 and 2):
     a self-referencing canonical (absolute URL on the canonical host, query string never included)
     followed by JSON-LD structured data. The site-wide Organization block is always emitted.
       $canonicalPath   optional: pins slug-keyed pages to their stored slug, so a case/format variant
                        of the URL still points at the canonical one.
       $structuredData  optional: extra schema arrays for this page, e.g. Seo::productSchemas($product). --}}
<link rel="canonical" href="{{ \App\Support\Seo::canonicalUrl($canonicalPath ?? null) }}"/>
{!! \App\Support\Seo::jsonLd([
    \App\Support\Seo::organization($siteName ?? null, $siteLogo ?? null, [$socialFacebook ?? null, $socialInstagram ?? null, $socialYoutube ?? null, $socialDaraz ?? null]),
    ...($structuredData ?? []),
]) !!}
