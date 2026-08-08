<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout - {{ $siteName ?? 'Khan Gadget' }}</title>
    <link rel="stylesheet" href="/assets/styles-CC_Lznyw.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="/assets/theme.js"></script>
</head>
<body class="min-h-screen bg-background text-foreground">
    @include('partials.header')

    <main class="container-page py-8 sm:py-12">
        <div class="mb-8 text-center"><p class="text-sm text-muted-foreground">Secure checkout</p><h1 class="mt-1 text-3xl font-semibold">Complete your order</h1></div>
        <div id="checkout-empty" class="hidden rounded-lg border border-border p-8 text-center">
            <p>Your cart is empty.</p><a href="/shop" class="mt-4 inline-block font-semibold underline">Continue shopping</a>
        </div>
        <form id="checkout-form" class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_380px]" novalidate>
            <section class="space-y-6">
                <div class="rounded-lg border border-border bg-card p-5 sm:p-6">
                    <h2 class="text-lg font-semibold">Delivery information</h2>
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <label class="grid gap-1.5 text-sm font-medium sm:col-span-2">Full name<input required name="customer_name" class="rounded-md border border-border bg-background px-3 py-2.5 font-normal" autocomplete="name"></label>
                        <label class="grid gap-1.5 text-sm font-medium">Mobile number<input required name="phone" inputmode="tel" class="rounded-md border border-border bg-background px-3 py-2.5 font-normal" placeholder="01XXXXXXXXX" autocomplete="tel"></label>
                        <label class="grid gap-1.5 text-sm font-medium">Email <span class="font-normal text-muted-foreground">(optional)</span><input name="email" type="email" class="rounded-md border border-border bg-background px-3 py-2.5 font-normal" autocomplete="email"></label>
                        <label class="grid gap-1.5 text-sm font-medium">District<input required name="district" class="rounded-md border border-border bg-background px-3 py-2.5 font-normal"></label>
                        <label class="grid gap-1.5 text-sm font-medium sm:col-span-2">Full address<textarea required name="address" rows="3" class="rounded-md border border-border bg-background px-3 py-2.5 font-normal"></textarea></label>
                        <label class="grid gap-1.5 text-sm font-medium sm:col-span-2">Order note <span class="font-normal text-muted-foreground">(optional)</span><textarea name="note" rows="2" class="rounded-md border border-border bg-background px-3 py-2.5 font-normal"></textarea></label>
                    </div>
                </div>
                <div class="rounded-lg border border-border bg-card p-5 sm:p-6"><h2 class="text-lg font-semibold">Payment method</h2><label class="mt-4 flex cursor-pointer items-center gap-3 rounded-md border border-border p-4"><input checked type="radio" name="payment_method" value="cod"><span><strong>Cash on Delivery</strong><br><span class="text-sm text-muted-foreground">Pay when your order arrives.</span></span></label></div>
            </section>
            <aside class="h-fit rounded-lg border border-border bg-card p-5 sm:p-6 lg:sticky lg:top-6"><h2 class="text-lg font-semibold">Order summary</h2><div id="checkout-items" class="mt-5 space-y-4"></div><div class="mt-5 space-y-2 border-t border-border pt-4 text-sm"><div class="flex justify-between"><span>Subtotal</span><strong id="checkout-subtotal"></strong></div><div class="flex justify-between"><span>Delivery</span><strong>Free</strong></div><div class="flex justify-between border-t border-border pt-3 text-base"><strong>Total</strong><strong id="checkout-total"></strong></div></div><p id="checkout-error" class="mt-4 hidden text-sm text-red-600 dark:text-red-400"></p><button id="place-order" type="submit" class="mt-6 w-full rounded-full bg-primary px-4 py-3 font-bold text-primary-foreground transition hover:bg-primary/90">Place order</button></aside>
        </form>
        <section id="order-success" class="hidden mx-auto max-w-xl rounded-lg border border-border bg-card p-8 text-center"><p class="text-2xl font-bold text-foreground">Order confirmed!</p><p class="mt-3">Your order number is <strong id="order-number"></strong>.</p><p class="mt-2 text-sm text-muted-foreground">We will contact you soon to confirm delivery.</p><a href="/" class="mt-6 inline-block font-semibold underline">Back to home</a></section>
    </main>
    @include('partials.footer')
    @include('partials.mobile-drawer')
    <script>
    (function () {
        var cartKey = 'kg_shopping_cart';
        var buyNow = @json($buyNow);
        var items = buyNow ? [buyNow] : JSON.parse(localStorage.getItem(cartKey) || '[]');
        var money = function (n) { return '৳ ' + Number(n || 0).toLocaleString('en-IN'); };
        var escapeHtml = function (s) { var d=document.createElement('div'); d.textContent=s || ''; return d.innerHTML; };
        var total = items.reduce(function (sum, item) { return sum + Number(item.price || 0) * Number(item.quantity || 1); }, 0);
        if (!items.length) { document.getElementById('checkout-form').classList.add('hidden'); document.getElementById('checkout-empty').classList.remove('hidden'); return; }
        document.getElementById('checkout-items').innerHTML = items.map(function (item) { return '<div class="flex gap-3"><img class="h-14 w-14 rounded object-cover bg-secondary" src="' + escapeHtml(item.image) + '" alt=""><div class="min-w-0 flex-1"><p class="truncate text-sm font-medium">' + escapeHtml(item.name) + '</p><p class="mt-1 text-sm text-muted-foreground">Qty: ' + Number(item.quantity || 1) + '</p></div><strong class="text-sm">' + money(Number(item.price || 0) * Number(item.quantity || 1)) + '</strong></div>'; }).join('');
        document.getElementById('checkout-subtotal').textContent = money(total); document.getElementById('checkout-total').textContent = money(total);
        document.getElementById('checkout-form').addEventListener('submit', async function (event) {
            event.preventDefault(); var form = event.currentTarget, button = document.getElementById('place-order'), error = document.getElementById('checkout-error');
            if (!form.reportValidity()) return;
            error.classList.add('hidden'); button.disabled = true; button.textContent = 'Placing order…';
            var data = Object.fromEntries(new FormData(form).entries()); data.items = items.map(function (item) { return { slug: item.slug, quantity: Number(item.quantity || 1) }; });
            try { var response = await fetch('/orders', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify(data) }); var payload = await response.json(); if (!response.ok) throw new Error(payload.message || Object.values(payload.errors || {})[0]?.[0] || 'Could not place the order.'); if (!buyNow) localStorage.removeItem(cartKey); document.getElementById('checkout-form').classList.add('hidden'); document.getElementById('order-number').textContent = payload.order_number; document.getElementById('order-success').classList.remove('hidden'); } catch (err) { error.textContent = err.message; error.classList.remove('hidden'); button.disabled = false; button.textContent = 'Place order'; }
        });
    })();
    </script>
</body></html>
