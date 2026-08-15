@if($homeNewArrivalActive ?? false)
<section class="container-page py-12">
    <div class="rounded-md border border-border bg-gradient-to-br from-accent/10 via-surface to-background p-4 sm:p-10">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <div>
                @if($homeNewArrivalBadgeActive ?? true)
                    <span class="inline-flex items-center gap-2 rounded-full bg-accent/15 px-3 py-1 text-xs font-medium text-accent">
                        @if($homeNewArrivalBadgeIcon ?? '')
                            <span aria-hidden="true">{{ $homeNewArrivalBadgeIcon }}</span>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles h-3.5 w-3.5" aria-hidden="true"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3Z"></path></svg>
                        @endif
                        {{ $homeNewArrivalBadgeText ?? 'New Arrival' }}
                    </span>
                @endif
                <h2 class="mt-3 text-2xl font-semibold tracking-tight sm:text-3xl">
                    {!! renderSectionTitle($homeNewArrivalTitle ?? 'New Arrivals', $homeNewArrivalHighlight ?? 'New', null) !!}
                </h2>
                @if(($homeNewArrivalSubtitleActive ?? true) && ($homeNewArrivalSubtitleText ?? ''))
                    <p class="mt-2 text-sm text-muted-foreground">{{ $homeNewArrivalSubtitleText }}</p>
                @endif
            </div>
        </div>
        <div class="mt-6 grid grid-cols-2 gap-3 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($newArrivalProducts as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif
