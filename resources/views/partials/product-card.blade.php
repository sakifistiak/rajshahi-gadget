<article class="group flex h-full flex-col border border-border bg-secondary overflow-hidden transition-shadow hover:shadow-sm" style="border-radius:10px">
    <div class="relative overflow-hidden bg-surface">
        <a href="/product/{{ $product->slug }}" class="block">
            <div class="aspect-square w-full overflow-hidden">
                <img src="{{ $product->primaryImage() }}" alt="{{ $product->name }}" loading="lazy" width="900" height="900" class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" />
            </div>
        </a>
        @if ($product->badge)
            <span class="product-card-badge absolute rounded font-extrabold uppercase pointer-events-none z-10" style="left: 6px !important; top: 6px !important; font-size: 8px !important; line-height: 10px !important; padding: 1.5px 4px !important; width: max-content !important; max-width: calc(100% - 12px) !important;">
                {{ $product->badge }}
            </span>
        @endif
    </div>
    <div class="flex flex-1 flex-col" style="padding:10px 12px 12px">
        <h3 class="line-clamp-2 text-xs font-semibold leading-snug text-foreground" style="margin:0">
            <a href="/product/{{ $product->slug }}" style="text-decoration:none">{{ $product->name }}</a>
        </h3>
        <ul class="text-xs leading-relaxed text-muted-foreground" style="margin-top:8px; padding:0; list-style:none; display:flex; flex-direction:column; gap:4px">
            @if($product->relationLoaded('highlights') && $product->highlights->count())
                @foreach ($product->highlights->take(3) as $highlight)
                    <li class="flex shrink-0" style="gap:6px; align-items:flex-start">
                        <span aria-hidden="true" class="h-1 w-1 shrink-0 rounded-full bg-muted-foreground/60" style="margin-top:6px"></span>
                        <span class="line-clamp-1">{{ $highlight->text }}</span>
                    </li>
                @endforeach
            @else
                @if($product->brand)
                    <li class="flex shrink-0" style="gap:6px; align-items:flex-start">
                        <span aria-hidden="true" class="h-1 w-1 shrink-0 rounded-full bg-muted-foreground/60" style="margin-top:6px"></span>
                        <span class="line-clamp-1">Brand: {{ $product->brand->name }}</span>
                    </li>
                @endif
                @if($product->category)
                    <li class="flex shrink-0" style="gap:6px; align-items:flex-start">
                        <span aria-hidden="true" class="h-1 w-1 shrink-0 rounded-full bg-muted-foreground/60" style="margin-top:6px"></span>
                        <span class="line-clamp-1">Category: {{ $product->category->name }}</span>
                    </li>
                @endif
            @endif
        </ul>
                        <div class="flex flex-wrap items-baseline min-h-[20px]" style="margin-top:12px; gap:6px">
            @if ($product->in_stock)
                @if ($product->price_is_tba)
                    <span class="text-sm font-semibold text-foreground">TBA</span>
                @else
                    <span class="text-sm font-semibold text-foreground">৳ {{ number_format($product->price) }}</span>
                    @if ($product->compare_at_price && $product->compare_at_price > $product->price)
                        <span class="text-xs text-muted-foreground line-through">৳ {{ number_format($product->compare_at_price) }}</span>
                    @endif
                @endif
            @endif
        </div>
        <div class="flex items-center justify-between" style="margin-top:12px; gap:8px">
            @if (!$product->in_stock)
                <button type="button" disabled class="flex-1 inline-flex items-center justify-center rounded-full bg-secondary text-muted-foreground font-bold text-sm cursor-not-allowed" style="height:36px; padding:0 16px;">
                    Out of Stock
                </button>
            @elseif ($product->is_new_arrival)
                <a href="https://wa.me/{{ \App\Support\PhoneNumber::whatsapp($whatsappNumber ?? '8801700000001') }}?text={{ rawurlencode("I'm Interested In " . $product->name . ' ' . route('product', $product->slug)) }}" target="_blank" rel="noopener noreferrer" class="btn-buy-now flex-1 inline-flex items-center justify-center rounded-full font-bold text-sm transition-all shadow-sm" style="background-color: #24272c !important; color: #ffffff !important; height:36px; padding:0 16px;">
                    Order Now
                </a>
            @else
                <button type="button" class="h-9 w-9 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-100 border border-slate-200/80 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center shrink-0 transition-colors shadow-sm" title="Add to Cart" aria-label="Add to Cart">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="height:16px;width:16px" aria-hidden="true"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>
                </button>
                <a href="/checkout?product={{ $product->slug }}" class="btn-buy-now flex-1 inline-flex items-center justify-center rounded-full font-bold text-sm transition-all shadow-sm" style="background-color: #24272c !important; color: #ffffff !important; height:36px; padding:0 16px;">
                    Buy Now
                </a>
            @endif
        </div>
    </div>
</article>
