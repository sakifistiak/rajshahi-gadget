@if($homePreorderActive ?? false)
<section class="container-page py-12">
    <div class="rounded-md border border-border bg-gradient-to-br from-accent/10 via-surface to-background p-4 sm:p-10">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <div>
                @if($homePreorderBadgeActive ?? true)
                    <span class="inline-flex items-center gap-2 rounded-full bg-accent/15 px-3 py-1 text-xs font-medium text-accent">
                        @if($homePreorderBadgeIcon ?? '')
                            <span aria-hidden="true">{{ $homePreorderBadgeIcon }}</span>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock h-3.5 w-3.5" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        @endif
                        {{ $homePreorderBadgeText ?? 'Pre-Order' }}
                    </span>
                @endif
                <h2 class="mt-3 text-2xl font-semibold tracking-tight sm:text-3xl">
                    {!! renderSectionTitle($homePreorderTitle ?? 'Pre-Order Now', $homePreorderHighlight ?? 'Pre-Order', null) !!}
                </h2>
                @if(($homePreorderSubtitleActive ?? true) && ($homePreorderSubtitleText ?? ''))
                    <p class="mt-2 text-sm text-muted-foreground">{{ $homePreorderSubtitleText }}</p>
                @endif
            </div>
        </div>
        <div class="mt-6 grid grid-cols-2 gap-3 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($preorderProducts as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif
