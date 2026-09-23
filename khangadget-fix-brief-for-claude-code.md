# Khan Gadget — SEO Fix Brief (for Claude Code / VS Code)

> **How to use this file:** open the actual `khangadget.com` project (frontend
> and/or backend repo) in VS Code, drop this file in the repo root, then in the
> Claude Code chat panel say:
> `Read khangadget-fix-brief-for-claude-code.md and implement Phase 1. Show me
> a diff before committing anything.`
> Work through phases in order — later phases assume earlier ones are done.
> This brief was written from an **external black-box audit** (HTTP responses
> + rendered HTML only, no repo access), so file paths below are *best-guess
> pointers*, not confirmed locations — the agent should locate the real files
> before editing and ask if something doesn't match.

## Site facts (confirmed by the audit, 29 Aug 2026)

- Frontend: server-rendered React via **TanStack Start/Router** (evidence:
  `data-tsd-source="/src/routes/__root.tsx"`, `/src/components/site/Navbar.tsx`
  attributes leaking into production HTML — see Phase 4, item M4).
- Backend: **Laravel on PHP 8.2**, LiteSpeed web server (evidence:
  `x-powered-by: PHP/8.2.30`, `XSRF-TOKEN` + `khan_gadget_session` cookies,
  `server: LiteSpeed`).
- No sitemap, no canonical tags, no JSON-LD anywhere on the live site as of
  the audit date. Full findings: `khangadget-seo-report.html` /
  `Khan-Gadget-SEO-Audit.docx` in this folder — this brief only contains the
  parts that are actionable as code/config changes.

---

## Phase 1 — Canonicalisation & discovery (do first, unblocks everything else)

### 1.1 Force one canonical host (`C2`)
**Problem:** `http://www.khangadget.com`, `https://khangadget.com` (no `www`)
and the `https://www.` version all return `200` directly — no redirect, no
HSTS header.

**Fix (server/infra layer, likely LiteSpeed vhost config, `.htaccess`, or a
Laravel `TrustProxies`/middleware + web server rewrite):**
- 301 redirect `http://*` → `https://www.khangadget.com` (or drop `www` if
  that's the brand's real preference — pick ONE and be consistent everywhere
  else in this brief).
- 301 redirect the non-canonical host → the canonical host.
- Add response header: `Strict-Transport-Security: max-age=31536000; includeSubDomains; preload`.

**Acceptance test:**
```bash
curl -I http://khangadget.com          # expect 301 -> https://www.khangadget.com/
curl -I https://khangadget.com         # expect 301 -> https://www.khangadget.com/
curl -I https://www.khangadget.com     # expect 200 + Strict-Transport-Security header
```

### 1.2 Kill the `/public/` duplicate path (`C3`)
**Problem:** `/shop/` (trailing slash) 301-redirects to `/public/shop`, and
`/public/shop` itself returns `200` — the Laravel `public/` directory is
exposed as a live URL segment, doubling every URL.

**Fix:**
- Confirm the web server document root points *at* `public/`, not at the
  project root (so `public/` never appears in a URL).
- Add a redirect rule: any request to `/public/*` → `/*` (301, strip the
  `/public` prefix).
- Fix whatever generates the `/shop/` → `/public/shop` redirect so it goes to
  `/shop` (no trailing slash, no `/public`).

**Acceptance test:**
```bash
curl -I https://www.khangadget.com/public/shop   # expect 301 -> /shop
curl -I https://www.khangadget.com/shop/         # expect 301 -> /shop (not /public/shop)
```

### 1.3 Add self-referencing canonical tags (`C1`)
**Problem:** every page returns `Canonical: None`.

**Fix:** in the shared `<head>` (TanStack Start `head()`/`meta()` export on
`__root.tsx` or per-route), emit:
```html
<link rel="canonical" href="https://www.khangadget.com/CURRENT-PATH" />
```
- Must be the **absolute** URL, on the canonical host from 1.1.
- Must **strip query params** (filters, sort, utm, etc.) unless that specific
  param combination is deliberately meant to be indexable (see Phase 5, `M1`).
- Product pages: canonical = the product's own clean `/product/<slug>` URL.

**Acceptance test:**
```bash
for u in / /shop /blog /product/<any-slug>; do
  curl -s "https://www.khangadget.com$u" | grep -o '<link rel="canonical"[^>]*>'
done
# every URL must print exactly one canonical, pointing at itself
```

### 1.4 Generate an XML sitemap (`C4`)
**Problem:** `/sitemap.xml` is 404; nothing referenced in `robots.txt`.

**Fix:**
- Add a Laravel route/controller (or scheduled artisan command writing a
  static file) that outputs `/sitemap.xml`, built from the product table +
  category/condition listing pages + CMS pages (`/page/*`) + blog posts.
  - Use a sitemap index (`/sitemap.xml` linking to `/sitemap-products.xml`,
    `/sitemap-pages.xml`, `/sitemap-blog.xml`) once product count grows past
    a few thousand URLs.
  - Each `<url>` needs `<loc>` (canonical URL, same host as 1.1) and
    `<lastmod>` from the real `updated_at` timestamp.
  - Only include URLs that return `200` and are the canonical version (don't
    list `/public/shop` or filtered param URLs).
- Regenerate on product create/update/delete (queue job or on-save hook), not
  just once.

**Acceptance test:**
```bash
curl -s https://www.khangadget.com/sitemap.xml | xmllint --noout -   # validates
curl -s https://www.khangadget.com/sitemap.xml | grep -c '<loc>'      # roughly matches product count
```

### 1.5 Fix `robots.txt` (`M7`, pulled into Phase 1 since it's one line)
**Current file (entire contents):**
```
User-agent: *
Disallow:
```
**Replace with:**
```
User-agent: *
Disallow: /cart
Disallow: /checkout
Disallow: /compare
Disallow: /*?*sort=
Disallow: /*?*page=

Sitemap: https://www.khangadget.com/sitemap.xml
```
(Adjust the disallowed paths to match whatever the real cart/checkout/compare
routes are — verify in the router, don't guess blindly.)

**Phase 1 sign-off checklist**
- [ ] One canonical host, everything else 301s to it
- [ ] HSTS header present
- [ ] No `/public/` URLs reachable except via redirect
- [ ] Every page has exactly one self-referencing canonical
- [ ] `/sitemap.xml` live, valid, submitted to Search Console + Bing Webmaster Tools
- [ ] `robots.txt` references the sitemap

---

## Phase 2 — Structured data (`C5`, `L2`)

**Problem:** zero JSON-LD anywhere. Product pages already display price,
strike-through original price, stock status and condition (Intact Box /
Without Box / Pre-Owned) — that data exists in the page, it's just not marked
up.

**Fix — add JSON-LD `<script type="application/ld+json">` blocks:**

**Product page** (one block per product page):
```json
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "<exact product title, plain text — no decorative unicode>",
  "image": ["<product image URLs>"],
  "sku": "<internal SKU/model if available>",
  "brand": { "@type": "Brand", "name": "<Acer / MSI / Dell / etc — parse from title>" },
  "itemCondition": "https://schema.org/NewCondition | RefurbishedCondition | UsedCondition",
  "offers": {
    "@type": "Offer",
    "url": "<canonical product URL>",
    "priceCurrency": "BDT",
    "price": "<numeric price, e.g. 320000>",
    "availability": "https://schema.org/InStock | OutOfStock",
    "itemCondition": "<same mapping as above>"
  }
}
```
Condition mapping (based on the site's own filter labels):
- "Brand New Intact Box" → `https://schema.org/NewCondition`
- "Brand New Without Box" → `https://schema.org/NewCondition` (or
  `RefurbishedCondition` if the business actually means open-box — confirm
  with the client before shipping)
- "Pre-Owned" → `https://schema.org/UsedCondition`

**Breadcrumbs** (product + category pages):
```json
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    { "@type": "ListItem", "position": 1, "name": "Home", "item": "https://www.khangadget.com/" },
    { "@type": "ListItem", "position": 2, "name": "Shop", "item": "https://www.khangadget.com/shop" },
    { "@type": "ListItem", "position": 3, "name": "<Product name>", "item": "<canonical product URL>" }
  ]
}
```

**Site-wide `Organization`** (in `__root.tsx` head, every page):
```json
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "Khan Gadget",
  "url": "https://www.khangadget.com/",
  "logo": "https://www.khangadget.com/media/b3ca13-kg-lockup-v2.png",
  "sameAs": ["<Facebook page>", "<YouTube channel>", "<any other real social profile>"]
}
```
Only include `sameAs` URLs that actually exist — don't fabricate profiles.

**Blog posts:** `BlogPosting` with `headline`, `datePublished`,
`dateModified`, `author`, `image`.

**Acceptance test:**
- Paste 3 product URLs, the homepage, and a blog post into
  https://search.google.com/test/rich-results — zero errors.
- `curl -s <product-url> | grep -c 'application/ld+json'` returns ≥ 2
  (Product + BreadcrumbList) on product pages.

---

## Phase 3 — On-page text fixes (`H1`–`H5`)

### 3.1 Fix product meta descriptions (`H1`)
**Problem:** descriptions use decorative Unicode "mathematical bold" and
small-caps glyphs (e.g. `𝐀𝐜𝐞𝐫 𝐏𝐫𝐞𝐝𝐚𝐭𝐨𝐫...`, `Dɪsᴄᴏᴜɴᴛ Pʀɪcᴇ`) plus a
double-encoded `&amp;nbsp;` artifact. Find wherever this string is built
(likely a product-admin "marketing description" field or a template that
concatenates model + spec + price) and:
- Strip/ban the styled-Unicode character ranges at input or output time.
- Fix the double-encoding — the description is being HTML-entity-encoded
  twice somewhere in the pipeline (encode once, at render time only).
- Keep it under ~155 plain-text characters: model, 2-3 headline specs,
  condition, price.

### 3.2 Add a real `<h1>` to Home, `/shop`, `/blog`, `/about` (`H2`)
All four currently have zero `<h1>` tags. Add one visible, keyword-relevant
H1 per page (not just an image-logo alt text):
- `/` → e.g. "Genuine Imported Laptops & Gadgets in Bangladesh"
- `/shop` → "All Products" (or reflect the active filter when one is applied)
- `/blog` → "Khan Gadget Blog"
- `/page/about-us` → "About Khan Gadget"

### 3.3 Unique meta descriptions per page (`H3`)
Home, `/shop`, `/blog` currently all output the identical boilerplate
string; `/page/about-us` has none at all. Write one unique 140–160 char
description per route/CMS page; remove the hard line-break characters
currently embedded in the shared string.

### 3.4 Shorten product `<title>` tags (`H4`)
Currently 150+ characters (full spec dump + brand). Google truncates around
60. New template:
```
{Brand} {Model} · {1-2 headline specs} {condition} | Khan Gadget
```
Keep the full spec string in the H1/body — just shorten the `<title>`.

### 3.5 Fix the homepage `<title>` (`H5`)
Current: `Khan Gadget - Eternal Tech Companion` (no commercial keyword).
Replace with something like:
`Khan Gadget — Genuine Imported Laptops & Gadgets in Bangladesh`

**Acceptance test:** re-run `parse_html` (or just `curl | grep`) on Home,
/shop, /blog, /about, and 3 product pages — confirm one H1 each, unique
meta descriptions, product titles ≤ ~60 visible chars, no Unicode styling in
any `<meta name="description">` or `<title>`.

---

## Phase 4 — Performance & hygiene (`H6`, `M2`–`M5`)

### 4.1 Stop sending `cache-control: no-cache, private` on every HTML page (`H6`)
**Problem:** every response — including plain catalogue/content pages —
sets `cache-control: no-cache, private` and issues a fresh session cookie,
because (most likely) session middleware runs globally.

**Fix:**
- Scope Laravel's `StartSession` / `VerifyCsrfToken` middleware to only the
  routes that actually need a session (cart, checkout, account, compare) —
  not the global `web` group applied to every route.
- For catalogue/content pages, set:
  `Cache-Control: public, max-age=300, s-maxage=3600, stale-while-revalidate=86400`
- If LiteSpeed cache (LSCache) is available, enable it for these routes with
  a cache-tag that gets purged when a product/price/stock updates.

**Acceptance test:**
```bash
curl -I https://www.khangadget.com/product/<slug>
# expect a public/max-age Cache-Control, and ideally no Set-Cookie on a first anonymous GET
```

### 4.2 Stop loading `unpkg.com/lucide@latest` (`M2`)
Replace the CDN `<script src="https://unpkg.com/lucide@latest">` with the
icons actually used, imported from the `lucide-react` (or equivalent)
package already likely in `package.json`, tree-shaken into the app bundle.
If it must stay external, pin an exact version and add `defer`.

### 4.3 Shrink the OG share image (`M3`)
`og:image` (`/media/share_image_1787501546.png`) is 1.28 MB. Re-export at
1200×630, JPEG or WebP, target < 200 KB. Give product pages their own
lightweight OG image derived from the primary product photo (resize/compress,
don't reuse the full-res upload).

### 4.4 Strip dev-only attributes from the production build (`M4`)
`data-tsd-source="/src/components/..."` attributes (57+ per page) are
leaking into production — this is a TanStack devtools/source-location
plugin left enabled. Find it in the Vite/TanStack Start config (likely
`vite.config.ts` or an `app.config.ts` plugin list) and disable it for
production builds (`NODE_ENV=production` / `import.meta.env.PROD` guard).

### 4.5 Consolidate Google Fonts requests (`M5`)
Currently two separate `fonts.googleapis.com` stylesheet links pulling in
Inter, Poppins, Playfair Display, Roboto Slab and Oswald. Audit which
families/weights are actually used in the shipped CSS, drop the rest, merge
into a single request, and self-host + `preload` the primary body/heading
face if the build tooling supports it.

**Acceptance test:** Chrome DevTools → Network tab on a fresh load — no
`unpkg.com` request, one `fonts.googleapis.com` request, OG image < 200 KB,
no `data-tsd-source` in view-source.

---

## Phase 5 — Depth & polish (`M1`, `M6`, `L1`, `L3`, `L4`)

- **M1** — `/shop?condition=intact` etc. are crawlable with no canonical.
  Decide which filtered views deserve their own indexable URL (e.g.
  `/shop/pre-owned` with a unique H1/title/intro) and canonical everything
  else back to `/shop`.
- **M6** — `/page/about-us` currently renders with no internal links/headings
  in raw HTML (looks client-rendered). Make sure CMS page bodies are in the
  server-rendered HTML, and expand About with real depth: founding story
  (since 2012), sourcing/warranty policy, physical store info — this is core
  E-E-A-T for a shop selling high-value imported electronics.
- **L1** — replace non-descriptive banner alt text (`alt="Hero Banner"`,
  `alt="Promo Banner"` ×2) with what the banner actually says.
- **L3** — set `<html lang="en-BD">` and `og:locale` = `en_BD`.
- **L4** — optional: add `/llms.txt` (Google ignores it; low priority, only
  do this after everything above ships).

---

## Notes for whoever implements this

- Don't guess at exact file paths from this brief — grep the real repo for
  the evidence strings quoted above (`"Eternal Tech Companion"`,
  `data-tsd-source`, the boilerplate description text, `unpkg.com/lucide`)
  to find the actual source files fast.
- Ship in the phase order above — Phase 2 (schema) and Phase 3 (on-page text)
  both assume Phase 1's canonical host is settled, since canonical URLs and
  JSON-LD `url`/`item` fields need to point at the final host.
- After each phase, re-run the acceptance tests in that section before moving
  on, and diff against the original audit (`khangadget-seo-report.html` /
  `Khan-Gadget-SEO-Audit.docx`) to confirm the finding is actually resolved,
  not just superficially patched.
