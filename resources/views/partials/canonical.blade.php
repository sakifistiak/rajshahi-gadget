{{-- Self-referencing canonical (SEO): absolute URL on the canonical host, query string never included.
     Slug-keyed pages pass $canonicalPath so a case/format variant of the URL still points at the stored slug. --}}
<link rel="canonical" href="{{ \App\Support\Seo::canonicalUrl($canonicalPath ?? null) }}"/>
