# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
composer install && npm install        # install PHP + JS dependencies
cp .env.example .env && php artisan key:generate   # first-time setup (DB is MySQL, see .env: DB_DATABASE=khanu)
php artisan migrate                    # run migrations

composer run dev                       # start everything: artisan serve + queue:listen + pail (logs) + vite, concurrently
php artisan serve                      # PHP server only
npm run dev                            # Vite dev server only (admin panel assets — see Architecture)
npm run build                          # build admin panel assets for production

php artisan test                       # run full test suite (Pest/PHPUnit)
php artisan test --filter=TestName     # run a single test by name
php artisan test tests/Feature/Auth/AuthenticationTest.php   # run a single test file

./vendor/bin/pint                      # format/lint PHP (Laravel Pint, default ruleset — no pint.json)
./vendor/bin/pint --dirty              # only format changed files
```

## Architecture

### Two unrelated frontends share this codebase
- **Admin panel** (`/admin/*`, `resources/views/layouts/app.blade.php`, `x-app-layout`) uses this repo's real Vite + Tailwind pipeline: `resources/css/app.css` and `resources/js/app.js`, compiled via `npm run build`/`npm run dev` and loaded with `@vite(...)`.
- **Public storefront** (`/`, `/shop`, `/product/{slug}`, `resources/views/pages/**`, `resources/views/partials/**`) is markup ported wholesale from a React/Lovable AI-exported design (hence the `data-tsd-source="/src/components/..."` attributes scattered through these blade files — leave them in the source: `AppServiceProvider::boot()` strips them from the compiled output, except `ProductCard.tsx` values, which `styles-CC_Lznyw.css` selects on). It loads a **static, pre-built CSS bundle directly**, `public/assets/styles-CC_Lznyw.css`, plus `/assets/theme.js` and a self-hosted, version-pinned copy of lucide (`public/assets/vendor/lucide-*.min.js`, included via `partials/lucide-script.blade.php` with `defer`) — none of this is touched by this repo's Vite config.
- **Consequence:** any Tailwind class used in a storefront blade file that wasn't already present in that original harvested build renders with **no styling and no error** — this bites hardest with arbitrary-value classes like `bg-[#1877F2]`. Before relying on a new class in `resources/views/pages/**` or `resources/views/partials/**`, verify it exists first: `grep -F 'classname' public/assets/styles-CC_Lznyw.css`. If it's missing, use an inline `style="..."` attribute instead of adding the class.

### Site settings drive nearly everything editable
- `SiteSetting` (`app/Models/SiteSetting.php`) is a plain key-value store via `SiteSetting::getValue($key, $default)`, backing site info, contact details, social links, footer columns, homepage section toggles, popup offer config, live chat widget config, etc. The whole table is loaded once per request and cached via `Cache::rememberForever` (`site_settings.map`); the cache self-invalidates on the model's `saved`/`deleted` events, so any write through Eloquent (`setValue`, `updateOrCreate`, `create`) is picked up automatically — a raw `DB::table` write would not be. Same pattern for the active `StoreLocation` list via `StoreLocation::activeOrdered()` (`store_locations.active`).
- `AppServiceProvider::boot()` registers a global `View::composer('*', ...)` that runs on **every** view render and injects ~30 settings-derived variables (`$siteName`, `$socialFacebook`, `$footerCol1Links`, ...) plus active `StoreLocation`s into every view. The settings/locations reads are cache-backed (above); only the JSON decoding of footer/menu link blobs happens per render.
- Adding a new admin-editable setting means touching three places: `Admin\SettingController` (default in `index()`, validation rule + key in `update()`), `AppServiceProvider::boot()` (share it globally), and the admin Blade form that edits it (`resources/views/admin/settings/index.blade.php` or the relevant admin screen).

### Routing: catch-all page resolver
- `routes/web.php` declares specific named routes first, then ends with a catch-all `Route::get('/{page}', [PageController::class, 'page'])`. `PageController::page()` resolves this to `resources/views/pages/{page}.blade.php` if it exists. New specific routes must be declared **above** this catch-all or they'll never be reached.
- `PageController::product()` looks up a DB-backed `Product` first (renders `pages.product.detail`); if no matching row exists it falls back to a static `resources/views/pages/product/{slug}.blade.php` view (leftover per-product pages from the original static build).

### Admin panel access control
`/admin/*` routes require `auth` + `verified` + the `admin` middleware alias (`App\Http\Middleware\EnsureUserIsAdmin`, registered in `bootstrap/app.php`), which checks the `users.is_admin` boolean column. `is_admin` is intentionally **not** in `User::$fillable` — it must only ever be flipped directly in the DB/tinker, never through mass assignment (registration, profile update). `/dashboard` and `/profile` only need `auth` + `verified`, not `admin` — any verified user can reach those, only the `admin.*` route group is gated. `/register` is still open to the public; new registrants get `is_admin = false` by default and see a 403 on any `/admin/*` route.

### Caching headers and sessions
- Every web route starts a session and sets three cookies (session, `XSRF-TOKEN`, `kg_cart_token`) by default, so every response is `Cache-Control: no-cache, private`. With the default `database` session driver that also writes a `sessions` row for every cookieless request (bots, first visits).
- `public.json:300` (`CachePublicJson`) makes a route's JSON public for 300 seconds with an ETag. It only does so for a successful GET/HEAD with no cookie attached. Use it together with `->withoutMiddleware($withoutSession)` (defined at the top of `routes/web.php`), and only for responses that are identical for every visitor: `/api/site-fonts`, `/api/nav-categories`, `/api/v1/categories`. Never put anything visitor-specific in them.
- `no.store` (`NoStore`) sends `Cache-Control: no-store, private` and is required on every route that shows order or customer data: `/thank-you`, the public invoice, `/chat/*`, and everything in the `auth` group (dashboard, profile, `/admin/*`). `tests/Feature/Performance/CacheMiddlewareTest.php` audits the routes and fails if an admin-controller route or one of those pages lacks it. It sits before `SubstituteBindings` in the priority list so a 404 for an unknown order number is not stored either.
- Public catalogue pages are intentionally not cached yet: they embed a per-session CSRF token in four places (the only per-visitor content), and page views are counted server-side by `TrackVisitorAnalytics`.

### Orders
- `App\Http\Controllers\OrderController::store` (public checkout) re-prices every line item from the `Product` table server-side inside a `DB::transaction`, ignoring any price the client submits — follow this same pattern for any future pricing/checkout code.
- `Admin\OrderController` and the `Order`/`OrderItem` models are a newer, in-progress feature — check `git status` before assuming they're finished or already reviewed.
