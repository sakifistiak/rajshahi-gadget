@foreach ($spotlights as $spotlight)
    <div>
        <article class="group overflow-hidden rounded-md border border-border bg-surface transition-all duration-300 hover:-translate-y-1 hover:shadow-md flex flex-col h-full">
            <button type="button" class="spotlight-modal-trigger block aspect-[4/3] w-full cursor-pointer overflow-hidden bg-slate-100 dark:bg-slate-800/50 flex items-center justify-center" style="border:0;padding:0;" data-product="{{ $spotlight->product }}" data-quote="{{ $spotlight->quote }}" data-image="{{ $spotlight->image ?: '/assets/no-image-placeholder.svg' }}" aria-label="View spotlight photo">
                <img src="{{ $spotlight->image ?: '/assets/no-image-placeholder.svg' }}" alt="{{ $spotlight->product }}" loading="lazy" class="h-full w-full object-contain transition-transform duration-500 group-hover:scale-105"/>
            </button>
            <div class="p-5 flex-1 flex flex-col justify-between">
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-foreground leading-snug line-clamp-2">{{ $spotlight->product }}</h3>
                    @if ($spotlight->quote)
                        <p class="mt-2 line-clamp-3 text-xs sm:text-sm text-muted-foreground italic">&ldquo;{{ $spotlight->quote }}&rdquo;</p>
                    @endif
                </div>
            </div>
        </article>
    </div>
@endforeach
