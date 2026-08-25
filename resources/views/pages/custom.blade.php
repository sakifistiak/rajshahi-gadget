<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>{{ $page->meta_title ?? $page->title }} - {{ \App\Models\SiteSetting::getValue('site_name', 'Khan Gadget') }}</title>
    @if($page->meta_description)
        <meta name="description" content="{{ $page->meta_description }}"/>
    @endif
    <link rel="icon" href="{{ $siteFavicon ?? '/favicon.png' }}" type="image/png"/>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"/>
    <link rel="stylesheet" href="/assets/styles-CC_Lznyw.css"/>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="/assets/theme.js"></script>
    <style>
        /* The static styles-CC_Lznyw.css bundle has no Tailwind Typography
           (.prose) classes at all, so they render as a no-op here — paragraph
           spacing has to be hand-normalized instead. Quill (the admin rich
           text editor for custom pages) also saves every blank line as a
           literal <p><br></p>, which otherwise stacks its own margin on top
           of the next paragraph's, producing oversized gaps. */
        #custom-page-content > :where(p, ul, ol, h1, h2, h3, h4, h5, h6, blockquote) { margin: 0 0 1em; }
        #custom-page-content > :last-child { margin-bottom: 0; }
        #custom-page-content > p:empty,
        #custom-page-content > p:has(> br:only-child) { display: none; }

        /* A few hover:/spacing utilities used by the location cards aren't in
           the harvested styles-CC_Lznyw.css bundle (see CLAUDE.md), so those
           bits are defined here instead of via class. */
        .kg-details-btn:hover { background-color: var(--secondary); }
        .kg-map-btn:hover { background-color: var(--primary); opacity: .9; }
        .kg-phone-link:hover { color: var(--foreground); }
    </style>
</head>
<body class="bg-background text-foreground antialiased min-h-screen flex flex-col">
    <!-- Header Navbar -->
    @include('partials.header')

    <!-- Main Content Container -->
    <main class="flex-1 py-10 sm:py-16">
        <div class="container-page max-w-4xl mx-auto">
<!-- Page Header -->
            @if ($page->show_title)
                <header class="border-b border-border pb-6 mb-8">
                    <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-foreground">
                        {{ $page->title }}
                    </h1>
                    <p class="text-xs text-muted-foreground mt-2">Last updated: {{ $page->updated_at->format('F d, Y') }}</p>
                </header>
            @endif

            <!-- Render HTML Content -->
            <article id="custom-page-content" class="text-foreground leading-relaxed text-sm sm:text-base">
                {!! $page->content !!}
            </article>

            <!-- Store Locations (added per-page from the admin page editor) -->
            @if ($page->locations->isNotEmpty())
                <section aria-label="Store locations" class="mt-12 border-t border-border" style="padding-top:2.5rem">
                    <h2 class="text-2xl font-semibold tracking-tight text-foreground">Store Locations</h2>
                    <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2">
                        @foreach ($page->locations as $location)
                            <div class="flex flex-col overflow-hidden rounded-md border border-border bg-surface shadow-sm">
                                @if ($location->image_path)
                                    <div class="aspect-[4/3] w-full overflow-hidden">
                                        <img src="{{ $location->image_path }}" alt="{{ $location->name }}" loading="lazy" class="h-full w-full object-cover">
                                    </div>
                                @endif
                                <div class="flex flex-1 flex-col p-5">
                                    <h3 class="flex items-center gap-2 text-sm font-semibold text-foreground">
                                        <i data-lucide="house" class="h-4 w-4 shrink-0 text-muted-foreground"></i>
                                        {{ $location->name }}
                                    </h3>
                                    @if ($location->address)
                                        <div class="mt-2 flex items-start gap-2 text-xs text-muted-foreground">
                                            <i data-lucide="map-pin" class="mt-0.5 h-3.5 w-3.5 shrink-0"></i>
                                            <span style="white-space:pre-line">{{ $location->address }}</span>
                                        </div>
                                    @endif
                                    @if ($location->phone)
                                        <a href="tel:{{ \App\Support\PhoneNumber::tel($location->phone) }}" class="kg-phone-link mt-2 inline-flex items-center gap-2 text-xs text-muted-foreground">
                                            <i data-lucide="phone" class="h-3.5 w-3.5 shrink-0"></i>
                                            {{ $location->phone }}
                                        </a>
                                    @endif

                                    @if ($location->map_link || $location->details)
                                        <div class="mt-4 flex flex-wrap gap-2">
                                            @if ($location->map_link)
                                                <a href="{{ $location->map_link }}" target="_blank" rel="noopener noreferrer" class="kg-map-btn inline-flex flex-1 items-center justify-center gap-1.5 rounded-full bg-primary px-4 text-xs font-bold text-primary-foreground shadow" style="height:36px;">
                                                    <i data-lucide="map" class="h-3.5 w-3.5"></i>
                                                    Shop Map
                                                </a>
                                            @endif
                                            @if ($location->details)
                                                <button type="button" onclick="kgToggleLocationDetails(this, 'loc-details-{{ $location->id }}')" class="kg-details-btn inline-flex flex-1 items-center justify-center gap-1.5 rounded-full border border-border px-4 text-xs font-bold text-foreground" style="height:36px;">
                                                    Show Details
                                                </button>
                                            @endif
                                        </div>
                                    @endif

                                    @if ($location->details)
                                        <div id="loc-details-{{ $location->id }}" class="hidden mt-3 border-t border-border pt-3 text-xs leading-relaxed text-muted-foreground" style="white-space:pre-line">{{ $location->details }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </main>

    <!-- Footer -->
    @include('partials.footer', ['hideOutlets' => true])
    @include('partials.mobile-drawer')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        function kgToggleLocationDetails(button, panelId) {
            const panel = document.getElementById(panelId);
            if (!panel) return;
            const isHidden = panel.classList.contains('hidden');
            panel.classList.toggle('hidden');
            button.textContent = isHidden ? 'Hide Details' : 'Show Details';
        }
    </script>
</body>
</html>
