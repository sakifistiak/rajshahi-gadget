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
        @if ($product->is_new_arrival)
            <span class="product-card-badge absolute rounded font-extrabold uppercase pointer-events-none z-10 bg-accent text-accent-foreground" style="right: 6px !important; top: 6px !important; font-size: 8px !important; line-height: 10px !important; padding: 1.5px 4px !important; width: max-content !important; max-width: calc(100% - 12px) !important;">
                Pre-Order
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
        <div class="flex flex-wrap items-baseline" style="margin-top:12px; gap:6px">
            @if ($product->price_is_tba)
                <span class="text-sm font-semibold text-foreground">TBA</span>
            @else
                <span class="text-sm font-semibold text-foreground">৳ {{ number_format($product->price) }}</span>
                @if ($product->compare_at_price)
                    <span class="text-xs text-muted-foreground line-through">৳ {{ number_format($product->compare_at_price) }}</span>
                @endif
            @endif
        </div>
        <div class="flex items-center justify-between" style="margin-top:12px; gap:8px">
            @if ($product->is_new_arrival)
                <a href="https://wa.me/{{ \App\Support\PhoneNumber::whatsapp($whatsappNumber ?? '8801700000001') }}?text={{ rawurlencode('I want to order ' . $product->name . ' ' . route('product', $product->slug)) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-1.5 rounded-full font-bold text-sm transition-all shadow-sm" style="background-color:#25D366 !important; color:#ffffff !important; height:32px; padding:0 16px; width:80%; margin:0 auto;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.372-.01-.571-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884M20.52 3.449C18.24 1.245 15.24 0 12.045 0 5.463 0 .105 5.36.103 11.943c0 2.105.549 4.16 1.595 5.973L0 24l6.335-1.652a11.882 11.882 0 005.71 1.447h.006c6.585 0 11.94-5.36 11.943-11.943a11.874 11.874 0 00-3.474-8.403"/></svg>
                    Order On WhatsApp
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
