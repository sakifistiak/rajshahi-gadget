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
</head>
<body class="bg-background text-foreground antialiased min-h-screen flex flex-col">
    <!-- Header Navbar -->
    @include('partials.header')

    <!-- Main Content Container -->
    <main class="flex-1 py-10 sm:py-16">
        <div class="container-page max-w-4xl mx-auto">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs text-muted-foreground mb-6">
                <a href="/" class="hover:text-foreground transition-colors">Home</a>
                <span>/</span>
                <span class="text-foreground font-medium">{{ $page->title }}</span>
            </nav>

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
            <article class="prose dark:prose-invert prose-slate max-w-none text-foreground leading-relaxed text-sm sm:text-base space-y-4">
                {!! $page->content !!}
            </article>
        </div>
    </main>

    <!-- Footer -->
    @include('partials.footer')
    @include('partials.mobile-drawer')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
