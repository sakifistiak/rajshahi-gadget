{{--
    Shared storefront paginator. Renders real prev / numbered / next links from a
    LengthAwarePaginator ($paginator), styled to match the design's pill pager.
    Usage: @include('partials.pager', ['paginator' => $items])
    Renders nothing when there is only a single page.
--}}
@php
    $p = $paginator ?? null;
@endphp
@if($p && $p->hasPages())
    @php
        $lastPage = $p->lastPage();
        $currentPage = $p->currentPage();
        $window = 1;
        $numbers = [];
        for ($i = 1; $i <= $lastPage; $i++) {
            if ($i === 1 || $i === $lastPage || ($i >= $currentPage - $window && $i <= $currentPage + $window)) {
                $numbers[] = $i;
            }
        }
        $previousNumber = null;
    @endphp
    <nav class="mt-10 flex items-center justify-center gap-2" aria-label="Pagination">
        @if($p->onFirstPage())
            <span class="grid h-9 w-9 place-items-center rounded-full border border-border opacity-40" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"></path></svg>
            </span>
        @else
            <a href="{{ $p->previousPageUrl() }}" rel="prev" aria-label="Previous page" class="grid h-9 w-9 place-items-center rounded-full border border-border transition-colors hover:bg-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"></path></svg>
            </a>
        @endif

        @foreach($numbers as $number)
            @if($previousNumber !== null && $number - $previousNumber > 1)
                <span class="grid h-9 min-w-9 place-items-center px-1 text-sm text-muted-foreground">&hellip;</span>
            @endif

            @if($number == $currentPage)
                <span aria-current="page" class="grid h-9 min-w-9 place-items-center rounded-full px-3 text-sm bg-foreground text-background">{{ $number }}</span>
            @else
                <a href="{{ $p->url($number) }}" class="grid h-9 min-w-9 place-items-center rounded-full border border-border px-3 text-sm transition-colors hover:bg-secondary">{{ $number }}</a>
            @endif

            @php($previousNumber = $number)
        @endforeach

        @if($p->hasMorePages())
            <a href="{{ $p->nextPageUrl() }}" rel="next" aria-label="Next page" class="grid h-9 w-9 place-items-center rounded-full border border-border transition-colors hover:bg-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg>
            </a>
        @else
            <span class="grid h-9 w-9 place-items-center rounded-full border border-border opacity-40" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg>
            </span>
        @endif
    </nav>
@endif
