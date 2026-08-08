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
- **Public storefront** (`/`, `/shop`, `/product/{slug}`, `resources/views/pages/**`, `resources/views/partials/**`) is markup ported wholesale from a React/Lovable AI-exported design (hence the `data-tsd-source="/src/components/..."` attributes scattered through these blade files — harmless, leave them, they are not dead code to clean up). It loads a **static, pre-built CSS bundle directly**, `public/assets/styles-CC_Lznyw.css`, plus `/assets/theme.js` and the `unpkg.com/lucide` CDN script — none of this is touched by this repo's Vite config.
- **Consequence:** any Tailwind class used in a storefront blade file that wasn't already present in that original harvested build renders with **no styling and no error** — this bites hardest with arbitrary-value classes like `bg-[#1877F2]`. Before relying on a new class in `resources/views/pages/**` or `resources/views/partials/**`, verify it exists first: `grep -F 'classname' public/assets/styles-CC_Lznyw.css`. If it's missing, use an inline `style="..."` attribute instead of adding the class.

### Site settings drive nearly everything editable
- `SiteSetting` (`app/Models/SiteSetting.php`) is a plain key-value store via `SiteSetting::getValue($key, $default)`, backing site info, contact details, social links, footer columns, homepage section toggles, popup offer config, live chat widget config, etc.
- `AppServiceProvider::boot()` registers a global `View::composer('*', ...)` that runs on **every** view render and injects ~30 settings-derived variables (`$siteName`, `$socialFacebook`, `$footerCol1Links`, ...) plus active `StoreLocation`s into every view, uncached.
- Adding a new admin-editable setting means touching three places: `Admin\SettingController` (default in `index()`, validation rule + key in `update()`), `AppServiceProvider::boot()` (share it globally), and the admin Blade form that edits it (`resources/views/admin/settings/index.blade.php` or the relevant admin screen).

### Routing: catch-all page resolver
- `routes/web.php` declares specific named routes first, then ends with a catch-all `Route::get('/{page}', [PageController::class, 'page'])`. `PageController::page()` resolves this to `resources/views/pages/{page}.blade.php` if it exists. New specific routes must be declared **above** this catch-all or they'll never be reached.
- `PageController::product()` looks up a DB-backed `Product` first (renders `pages.product.detail`); if no matching row exists it falls back to a static `resources/views/pages/product/{slug}.blade.php` view (leftover per-product pages from the original static build).

### Admin panel access control
`/admin/*` routes require `auth` + `verified` + the `admin` middleware alias (`App\Http\Middleware\EnsureUserIsAdmin`, registered in `bootstrap/app.php`), which checks the `users.is_admin` boolean column. `is_admin` is intentionally **not** in `User::$fillable` — it must only ever be flipped directly in the DB/tinker, never through mass assignment (registration, profile update). `/dashboard` and `/profile` only need `auth` + `verified`, not `admin` — any verified user can reach those, only the `admin.*` route group is gated. `/register` is still open to the public; new registrants get `is_admin = false` by default and see a 403 on any `/admin/*` route.

### Orders
- `App\Http\Controllers\OrderController::store` (public checkout) re-prices every line item from the `Product` table server-side inside a `DB::transaction`, ignoring any price the client submits — follow this same pattern for any future pricing/checkout code.
- `Admin\OrderController` and the `Order`/`OrderItem` models are a newer, in-progress feature — check `git status` before assuming they're finished or already reviewed.
