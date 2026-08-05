<article class="group flex h-full flex-col">
    <div class="relative overflow-hidden rounded-md bg-surface">
        <a href="/product/{{ $product->slug }}" class="block">
            <div class="aspect-square w-full overflow-hidden">
                <img src="{{ $product->primaryImage() }}" alt="{{ $product->name }}" loading="lazy" width="900" height="900" class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" />
            </div>
        </a>
        @if ($product->badge)
            <span class="product-card-badge absolute left-1.5 top-1.5 rounded font-extrabold uppercase tracking-normal shadow-xs pointer-events-none z-10" style="font-size: 8px !important; line-height: 10px !important; padding: 1.5px 4px !important; width: max-content !important; max-width: calc(100% - 12px) !important;">
                {{ $product->badge }}
            </span>
        @endif
    </div>
    <div class="flex flex-1 flex-col pt-2.5 sm:pt-3">
        <h3 class="line-clamp-2 text-xs sm:text-sm font-semibold leading-snug tracking-tight text-foreground">
            <a href="/product/{{ $product->slug }}" class="hover:underline">{{ $product->name }}</a>
        </h3>
        <ul class="mt-1.5 sm:mt-2 space-y-0.5 sm:space-y-1 text-[11px] sm:text-xs leading-relaxed text-muted-foreground">
            @if($product->relationLoaded('highlights') && $product->highlights->count())
                @foreach ($product->highlights->take(3) as $highlight)
                    <li class="flex gap-1.5">
                        <span aria-hidden="true" class="mt-[5px] sm:mt-[7px] h-1 w-1 shrink-0 rounded-full bg-muted-foreground/60"></span>
                        <span class="line-clamp-1">{{ $highlight->text }}</span>
                    </li>
                @endforeach
            @else
                @if($product->brand)
                    <li class="flex gap-1.5">
                        <span aria-hidden="true" class="mt-[5px] sm:mt-[7px] h-1 w-1 shrink-0 rounded-full bg-muted-foreground/60"></span>
                        <span class="line-clamp-1">Brand: {{ $product->brand->name }}</span>
                    </li>
                @endif
                @if($product->category)
                    <li class="flex gap-1.5">
                        <span aria-hidden="true" class="mt-[5px] sm:mt-[7px] h-1 w-1 shrink-0 rounded-full bg-muted-foreground/60"></span>
                        <span class="line-clamp-1">Category: {{ $product->category->name }}</span>
                    </li>
                @endif
            @endif
        </ul>
        <div class="mt-2 sm:mt-3 flex flex-wrap items-baseline gap-x-1.5 sm:gap-x-2">
            <span class="text-sm sm:text-base font-semibold text-foreground">৳ {{ number_format($product->price) }}</span>
            @if ($product->compare_at_price)
                <span class="text-xs sm:text-sm text-muted-foreground line-through">৳ {{ number_format($product->compare_at_price) }}</span>
            @endif
        </div>
        <div class="mt-2.5 sm:mt-3 flex items-center justify-between gap-1.5 sm:gap-2">
            <button type="button" class="h-8 w-8 sm:h-9 sm:w-9 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-100 border border-slate-200/80 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center shrink-0 transition-colors shadow-sm" title="Add to Cart" aria-label="Add to Cart">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart h-3.5 w-3.5 sm:h-4 sm:w-4" aria-hidden="true"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>
            </button>
            <a href="/checkout?product={{ $product->slug }}" class="btn-buy-now flex-1 inline-flex items-center justify-center h-8 sm:h-9 px-2.5 sm:px-4 rounded-full font-bold text-xs sm:text-sm transition-all shadow-sm active:scale-[0.98]" style="background-color: #24272c !important; color: #ffffff !important;">
                Buy Now
            </a>
        </div>
    </div>
</article>
