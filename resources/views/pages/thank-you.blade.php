<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Confirmed | {{ $siteName ?? 'Khan Gadget' }}</title>
    <link rel="stylesheet" href="/assets/styles-CC_Lznyw.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="/assets/theme.js?v=20260828"></script>
    <style>
        /* Same reasoning as checkout.blade.php: the static styles-CC_Lznyw.css
           bundle is missing fractional-value and sm:/lg: responsive utility
           classes entirely, so layout/spacing here is hand-covered instead of
           relying on Tailwind classes that silently do nothing in this bundle. */
        html, body { overflow-x: clip; }
        /* Flex/grid items default to min-width:auto, which sizes them to fit
           their content (e.g. a long un-wrapped product name or price) even
           past the viewport. min-width:0 lets them shrink instead of pushing
           the page into horizontal overflow on narrow screens. */
        .ty-wrap, .ty-wrap * { min-width: 0; }
        .ty-wrap { max-width: 720px; margin-inline: auto; }
        .ty-grid { display: grid; gap: 16px; grid-template-columns: 1fr; }
        @media (min-width: 640px) {
            .ty-grid-2 { grid-template-columns: 1fr 1fr; }
        }
        .ty-row { display: flex; justify-content: space-between; gap: 12px; }
        .ty-item { display: flex; gap: 12px; align-items: center; }
        .ty-contact-grid { display: grid; gap: 12px; grid-template-columns: 1fr; }
        @media (min-width: 640px) {
            .ty-contact-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body class="min-h-screen bg-background text-foreground">
    @include('partials.header')

    <main class="container-page py-8 sm:py-12">
        <div class="ty-wrap">
            <div class="rounded-lg border border-border bg-card p-5" style="text-align:center">
                <div style="display:flex; justify-content:center; margin-bottom:12px">
                    <span style="height:56px; width:56px; border-radius:9999px; background:var(--color-success, #16a34a); display:flex; align-items:center; justify-content:center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="height:28px;width:28px" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg>
                    </span>
                </div>
                <p class="text-2xl font-bold text-foreground">Order confirmed!</p>
                <p class="mt-2 text-sm text-muted-foreground">Thank you, {{ $order->customer_name }}. We will contact you soon to confirm delivery.</p>
                <p class="mt-4 text-sm text-muted-foreground">Order number</p>
                <p class="font-bold text-lg" style="letter-spacing:0.02em">{{ $order->order_number }}</p>
                <div class="ty-contact-grid" style="margin-top:16px">
                    <a href="{{ route('orders.invoice', $order->order_number) }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 rounded-full font-bold text-sm" style="border:1px solid var(--border); color:var(--foreground); height:44px; padding:0 16px; text-decoration:none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="height:16px;width:16px" aria-hidden="true"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
                        View Invoice
                    </a>
                    <button type="button" onclick="directDownloadInvoice('{{ route('orders.invoice', $order->order_number) }}?download=1', this)" class="inline-flex items-center justify-center gap-2 rounded-full font-bold text-sm cursor-pointer" style="background:#24272c; color:#ffffff; height:44px; padding:0 16px; border:none; width:100%">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="height:16px;width:16px" aria-hidden="true"><path d="M12 15V3"></path><path d="m6 11 6 6 6-6"></path><path d="M19 21H5"></path></svg>
                        <span>Download Invoice</span>
                    </button>
                </div>
            </div>

            <script>
                function directDownloadInvoice(url, btn) {
                    const originalHtml = btn.innerHTML;
                    btn.innerHTML = 'Downloading PDF...';
                    btn.disabled = true;

                    const iframe = document.createElement('iframe');
                    iframe.style.position = 'fixed';
                    iframe.style.left = '-9999px';
                    iframe.style.top = '0';
                    iframe.style.width = '900px';
                    iframe.style.height = '1200px';
                    iframe.style.opacity = '0';
                    iframe.style.pointerEvents = 'none';
                    iframe.src = url;
                    document.body.appendChild(iframe);

                    setTimeout(() => {
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                        setTimeout(() => iframe.remove(), 10000);
                    }, 3500);
                }
            </script>

            <div class="mt-6 rounded-lg border border-border bg-card p-5">
                <h2 class="text-lg font-semibold">Order details</h2>
                <div class="mt-4" style="display:flex; flex-direction:column; gap:12px">
                    @foreach($order->items as $item)
                        <div class="ty-item">
                            <div style="height:56px;width:56px;border-radius:8px;overflow:hidden;flex-shrink:0" class="bg-secondary">
                                <img src="{{ optional($item->product)->primaryImage() ?? '/assets/laptop-ultrabook-C5nU_6_f.jpg' }}" alt="" style="height:100%;width:100%;object-fit:cover">
                            </div>
                            <div style="flex:1; min-width:0">
                                <p class="text-sm font-medium line-clamp-1">{{ $item->product_name }}</p>
                                <p class="mt-1 text-sm text-muted-foreground">Qty: {{ $item->quantity }}</p>
                            </div>
                            <strong class="text-sm">৳ {{ number_format($item->line_total) }}</strong>
                        </div>
                    @endforeach
                </div>
                <div class="mt-5 space-y-2 border-t border-border pt-4 text-sm">
                    <div class="ty-row"><span>Subtotal</span><strong>৳ {{ number_format($order->subtotal) }}</strong></div>
                    <div class="ty-row"><span>Delivery</span><strong>{{ $order->shipping_fee > 0 ? '৳ ' . number_format($order->shipping_fee) : 'Free' }}</strong></div>
                    <div class="ty-row border-t border-border pt-3 text-base"><strong>Total</strong><strong>৳ {{ number_format($order->total) }}</strong></div>
                </div>
            </div>

            <div class="mt-6 rounded-lg border border-border bg-card p-5">
                <h2 class="text-lg font-semibold">Billing &amp; shipping details</h2>
                <div class="mt-4 ty-grid ty-grid-2">
                    <div>
                        <p class="text-xs font-medium text-muted-foreground">Full name</p>
                        <p class="mt-1 text-sm font-medium">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-muted-foreground">Mobile number</p>
                        <p class="mt-1 text-sm font-medium">{{ $order->phone }}</p>
                    </div>
                    @if($order->email)
                        <div>
                            <p class="text-xs font-medium text-muted-foreground">Email</p>
                            <p class="mt-1 text-sm font-medium">{{ $order->email }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-xs font-medium text-muted-foreground">Payment method</p>
                        <p class="mt-1 text-sm font-medium">{{ $order->payment_method === 'cod' ? 'Cash on Delivery' : ucfirst($order->payment_method) }}</p>
                    </div>
                    @if($order->delivery_method === 'store_pickup')
                        <div style="grid-column:1/-1">
                            <p class="text-xs font-medium text-muted-foreground">Pickup outlet</p>
                            @if($order->storeLocation)
                                <p class="mt-1 text-sm font-medium">{{ $order->storeLocation->name }}</p>
                                <div class="mt-0.5 text-sm text-muted-foreground">{!! $order->storeLocation->address !!}</div>
                            @else
                                <p class="mt-1 text-sm text-muted-foreground">Outlet no longer available — we will contact you.</p>
                            @endif
                        </div>
                    @else
                        <div>
                            <p class="text-xs font-medium text-muted-foreground">Delivery area</p>
                            <p class="mt-1 text-sm font-medium">{{ $order->delivery_area === 'inside_dhaka' ? 'Inside Dhaka' : ($order->delivery_area === 'outside_dhaka' ? 'Outside Dhaka' : '—') }}</p>
                        </div>
                        <div style="grid-column:1/-1">
                            <p class="text-xs font-medium text-muted-foreground">Delivery address</p>
                            <p class="mt-1 text-sm font-medium">{{ $order->address }}</p>
                        </div>
                    @endif
                    @if($order->note)
                        <div style="grid-column:1/-1">
                            <p class="text-xs font-medium text-muted-foreground">Order note</p>
                            <p class="mt-1 text-sm font-medium">{{ $order->note }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-6 rounded-lg border border-border bg-card p-5">
                <h2 class="text-lg font-semibold">Have A Question About Your Order?</h2>
                <p class="mt-1 text-sm text-muted-foreground">Reach Out Anytime, Our Team Is Ready To Assist You!</p>
                <div class="mt-4 ty-contact-grid">
                    <a href="tel:{{ \App\Support\PhoneNumber::tel($liveChatCallNumber ?? $sitePhone ?? '+8801700000000') }}" class="inline-flex items-center justify-center gap-2 rounded-full font-bold text-sm" style="background:#24272c; color:#ffffff; height:44px; padding:0 16px; text-decoration:none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="height:16px;width:16px" aria-hidden="true"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg>
                        Call Us
                    </a>
                    <a href="https://wa.me/{{ \App\Support\PhoneNumber::whatsapp($whatsappNumber ?? '8801700000001') }}?text={{ rawurlencode('Hi, I have a question about my order ' . $order->order_number) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2 rounded-full font-bold text-sm" style="background:#25D366; color:#ffffff; height:44px; padding:0 16px; text-decoration:none">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.372-.01-.571-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884M20.52 3.449C18.24 1.245 15.24 0 12.045 0 5.463 0 .105 5.36.103 11.943c0 2.105.549 4.16 1.595 5.973L0 24l6.335-1.652a11.882 11.882 0 005.71 1.447h.006c6.585 0 11.94-5.36 11.943-11.943a11.874 11.874 0 00-3.474-8.403"/></svg>
                        Message Us on WhatsApp
                    </a>
                </div>
                @if($siteEmail ?? null)
                    <p class="mt-4 text-xs text-muted-foreground" style="text-align:center">Or email us at <a href="mailto:{{ $siteEmail }}" style="text-decoration:underline">{{ $siteEmail }}</a></p>
                @endif
            </div>

            <div class="mt-6" style="text-align:center">
                <a href="/" class="font-semibold" style="text-decoration:underline">Back to home</a>
                <span class="text-muted-foreground"> · </span>
                <a href="/shop" class="font-semibold" style="text-decoration:underline">Continue shopping</a>
            </div>
        </div>
    </main>
    @include('partials.footer', ['hideOutlets' => true])
    @include('partials.mobile-drawer')
</body>
</html>
